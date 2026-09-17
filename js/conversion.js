const form = document.getElementById('processing-form');
const convertButton = document.getElementById('pdfa-convert-button');
const statusContainer = document.getElementById('conversion-status');
const statusText = document.getElementById('conversion-status-text');
const statusIcon = document.getElementById('conversion-status-icon');
const resultContainer = document.getElementById('conversion-result');

/**
 * Add or remove "conversion-hidden" CSS class to/from HTML element
 *
 * @param {HTMLElement} element
 * @param {boolean} hidden Boolean indicating if the hidden class should be added or removed
 */
function toggleElementHidden(element, hidden) {
  element.classList.toggle('conversion-hidden', hidden);
};

/**
 * Replace current class names of status icon with new class names
 *
 * @param {Array} classes An array of new class names
 */
function resetIcon(classes) {
  statusIcon.className = classes.join(' ');
};

/**
 * Set class names and text content in the #conversion-status container
 *
 * @param {string} type Status type
 * @param {string} text Status text
 * @param {boolean} spinning Boolean indicating if there should be a spinner shown
 */
function setStatus(type, text, spinning = false) {
  const containerClasses = ['message-container', `message-${type}`];
  statusContainer.className = containerClasses.join(' ');
  statusText.textContent = text;

  const iconClasses = ['bi'];

  if (type === 'success') {
    iconClasses.push('bi-check');
  } else if (type === 'danger') {
    iconClasses.push('bi-exclamation-triangle-fill');
  }

  if (spinning) {
    iconClasses.push('visually-hidden');
    statusContainer.prepend(createSpinner());
  }

  resetIcon(iconClasses);
  toggleElementHidden(statusContainer, false);
};

/**
 * Render contents to #conversion-result container
 *
 * @param {Object} data
 */
function renderDownload(data) {
  resultContainer.innerHTML = '';

  if (!data || data.status !== 'success' || !data.downloadUrl) {
    toggleElementHidden(resultContainer, true);
    return;
  }

  const displayName = data.displayName ? `(${data.displayName})` : '';
  const downloadName = data.displayName || '';

  const icon = document.createElement("i");
  icon.classList.add("bi", "bi-box-arrow-down");
  icon.setAttribute("aria-hidden", true);

  const link = document.createElement("a");
  link.href = data.downloadUrl;
  link.classList.add("alert-link");
  link.setAttribute("data-download-url", data.downloadUrl);
  link.setAttribute("data-download-name", downloadName);
  link.textContent = `${resultContainer.dataset.readyLabel} ${displayName}`;

  resultContainer.append(icon, link);
  toggleElementHidden(resultContainer, false);
};

let statusPoller = null;

/**
 * Stop status polling by clearing statusPoller interval
 */
function stopPolling() {
  if (statusPoller !== null) {
    clearInterval(statusPoller);
    statusPoller = null;
  }
};

/**
 * Start polling the status of conversion process in intervals of 5 seconds
 *
 * @param {string} successText Fallback text for success status
 * @param {string} failedText Fallback text for error status
 */
function pollStatus(successText, failedText) {
  if (statusPoller !== null) {
    return;
  }

  statusPoller = setInterval(async () => {
    try {
      const response = await fetch('conversion_status.php', { credentials: 'same-origin' });

      if (!response.ok) {
        throw new Error(response.status);
      }

      const data = await response.json();

      if (data.status === 'success') {
        setStatus('success', data.message || successText, false);
        renderDownload(data);
        stopPolling();
      } else if (data.status === 'error') {
        setStatus('danger', data.message || failedText, false);
        stopPolling();
      }
    }
    catch(error) {
      // Ignore polling errors; the main request will handle failures.
    }
  }, 5000);
};

/**
 * Handle finishing the conversion process
 *
 * @param {Object} data JSON object containing information about the conversion process
 * @param {string} successText Fallback text for success status
 * @param {string} failedText Fallback text for error status
 */
function handleFinalStatus(data, successText, failedText) {
  if (data.status === 'success') {
    setStatus('success', data.message || successText, false);
  } else {
    setStatus('danger', data.message || failedText, false);
  }

  renderDownload(data);
  statusContainer.querySelector('.spinner-border').remove();
  convertButton.innerHTML = convertButton.dataset.textContent;
  stopPolling();
};

/**
 * Create and return elements for adding a spinner wrapped in document fragment
 *
 * @returns {HTMLDocumentFragment}
 */
function createSpinner() {
  const spinner = document.createElement("span");
  spinner.classList.add("spinner-border", "spinner-border-sm");
  spinner.setAttribute("aria-hidden", true);
  const accessibilityMessage = document.createElement("span");
  accessibilityMessage.textContent = convertButton.dataset.inProgress;
  accessibilityMessage.setAttribute("role", "status");
  accessibilityMessage.classList.add("visually-hidden");

  const fragment = document.createDocumentFragment();
  fragment.append(spinner, accessibilityMessage);
  return fragment;
}

/**
 * Call backend to convert the file and handle the result
 *
 * @param {Object} metadata Metadata for the file from <form> element
 * @param {string} successText Fallback text for success events
 * @param {string} failedText Fallback text for failure events
 */
async function convertFile(metadata, successText, failedText) {
  try {
    const response = await fetch('convert.php', {
      method: "POST",
      body: metadata,
      credentials: 'same-origin',
      signal: AbortSignal.timeout(6000)
    });

    if (!response.ok) {
      throw new Error(response.status);
    }

    const data = await response.json();
    console.log(data);

    // TODO: could PHP process be changed so that in case of error the status of response would be in range 5**?
    if (data.status === 'success' || data.status === 'error') {
      handleFinalStatus(data, successText, failedText);
    }
  }
  catch (error) {
    if (error.name === 'TimeoutError') {
      console.warn("TIMEOUT ERROR");
    }
    // TODO: currently this branch is only visited in errors originating in JS code, fetch errors do not come here ever
    console.log("IN THE ERROR ", error);
    handleFinalStatus({ status: 'error', message: failedText }, successText, failedText);
  }
  finally {
    convertButton.disabled = false;
  }
}

/**
 * Handle all the actions needed for the file conversion process
 */
function handleFileConversion() {
  convertButton.textContent = "";
  convertButton.appendChild(createSpinner());

  const inProgressText = statusContainer.dataset.inProgress;
  const successText = statusContainer.dataset.success;
  const failedText = statusContainer.dataset.failed;

  // TODO: formData is not trimmed or sanitized here, does PHP handle that and/or should it be done here?
  const formData = new FormData(form);

  // TODO: should this be moved to configs?
  formData.set('pdfa_convert', '1');

  convertButton.disabled = true;
  setStatus('info', inProgressText, true);
  toggleElementHidden(resultContainer, true);

  pollStatus(successText, failedText);
  convertFile(formData, successText, failedText);
}

// Click event listener for starting the file conversion process
convertButton?.addEventListener('click', (event) => {
  event.preventDefault();
  handleFileConversion();
});

// Input event listener for the #description <textarea> that updates the associated character counter element
document.getElementById("description")?.addEventListener("input", (event) => {
  const textArea = event.target;
  const counter = textArea.nextElementSibling;
  const maxLength = textArea.maxLength;
  counter.textContent = `${textArea.textLength}/${maxLength}`;
});
