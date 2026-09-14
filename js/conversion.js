const form = document.getElementById('processing-form');
const convertButton = document.getElementById('pdfa-convert-button');
const statusContainer = document.getElementById('conversion-status');
const statusText = document.getElementById('conversion-status-text');
const statusIcon = document.getElementById('conversion-status-icon');
const resultContainer = document.getElementById('conversion-result');
const conversionDetails = document.getElementById('conversion-details');

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
  } else {
    iconClasses.push('bi-arrow-repeat');
  }

  if (spinning) {
    iconClasses.push('conversion-spinner');
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
  if (!data || data.status !== 'success' || !data.downloadUrl) {
    resultContainer.innerHTML = '';
    toggleElementHidden(resultContainer, true);
    return;
  }

  resultContainer.innerHTML = '';

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

/**
 * Download the converted file
 *
 * @param {string} downloadUrl URL for the download
 * @param {string} downloadName Name of the file to download
 */
function downloadFile(downloadUrl, downloadName) {
  if (!downloadUrl) {
    return;
  }

  const downloadStatusText = resultContainer.dataset.downloadingLabel || 'Downloading…';
  setStatus('info', downloadStatusText, true);

  fetch(downloadUrl, { credentials: 'same-origin' })
    .then((response) => {
      if (!response.ok) {
        throw new Error('Download failed');
      }

      return response.blob().then((blob) => ({ blob, response }));
    })
    .then(({ blob, response }) => {
      let filename = downloadName;
      const disposition = response.headers.get('Content-Disposition') || '';

      if (!filename) {
        const match = disposition.match(/filename\*?=(?:UTF-8''|\"?)([^;\"]+)/i);
        if (match && match[1]) {
          filename = decodeURIComponent(match[1].replace(/\"/g, '').trim());
        }
      }

      const objectUrl = URL.createObjectURL(blob);
      const tempLink = document.createElement('a');
      tempLink.href = objectUrl;
      tempLink.download = filename || '';
      document.body.appendChild(tempLink);
      tempLink.click();
      tempLink.remove();
      URL.revokeObjectURL(objectUrl);

      setStatus('success', statusContainer.dataset.success, false);
    })
    .catch(() => {
      setStatus('danger', statusContainer.dataset.failed, false);
    });
};

/**
 * Update contents of #conversion-details element
 *
 * @param {string} summary Summary of conversion process
 */
function updateDetails(summary) {
  if (summary) {
    conversionDetails.textContent = summary;
    // TODO: this does not make the element visible, since it has display: none set in CSS file, implementation should be changed if element should be made visible
    toggleElementHidden(conversionDetails, false);
  } else {
    conversionDetails.textContent = '';
    toggleElementHidden(conversionDetails, true);
  }
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

  statusPoller = setInterval(() => {
    fetch('conversion_status.php', { credentials: 'same-origin' })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === 'success') {
          setStatus('success', data.message || successText, false);
          renderDownload(data);
          stopPolling();
        } else if (data.status === 'error') {
          setStatus('danger', data.message || failedText, false);
          stopPolling();
        }
      })
      .catch(() => {
        // Ignore polling errors; the main request will handle failures.
      });
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
  convertButton.innerHTML = convertButton.dataset.textContent;
  updateDetails(data.returnValue || '');
  stopPolling();
};

/**
 * Create and return elements for adding spinner within a button element wrapped in document fragment
 *
 * @returns {HTMLDocumentFragment}
 */
function createSpinnerBtn() {
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

convertButton?.addEventListener('click', async (event) => {
  event.preventDefault();

  convertButton.textContent = "";
  convertButton.appendChild(createSpinnerBtn());

  const inProgressText = statusContainer.dataset.inProgress;
  const successText = statusContainer.dataset.success;
  const failedText = statusContainer.dataset.failed;

  const formData = new FormData(form);
  formData.set('pdfa_convert', '1');

  convertButton.disabled = true;
  setStatus('info', inProgressText, true);
  toggleElementHidden(resultContainer, true);
  toggleElementHidden(conversionDetails, true);

  pollStatus(successText, failedText);

  fetch('convert.php', {
    method: 'POST',
    body: formData,
    credentials: 'same-origin',
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === 'success') {
        handleFinalStatus(data, successText, failedText);
        return;
      }

      fetch('conversion_status.php', { credentials: 'same-origin' })
        .then((response) => response.json())
        .then((statusData) => {
          if (statusData.status === 'success' || statusData.status === 'error') {
            handleFinalStatus(statusData, successText, failedText);
          } else {
            updateDetails(data.returnValue || '');
          }
        })
        .catch(() => {
          updateDetails(data.returnValue || '');
        });
      })
    .catch(() => {
      handleFinalStatus({ status: 'error', message: failedText }, successText, failedText);
    })
    .finally(() => {
      convertButton.disabled = false;
    });
});

resultBox?.addEventListener('click', (event) => {
  const downloadTrigger = event.target.closest('[data-download-url]');

  if (!downloadTrigger) {
    return;
  }

  event.preventDefault();
  downloadFile(downloadTrigger.dataset.downloadUrl, downloadTrigger.dataset.downloadName || '');
});
