const conversionInfoContainer = document.querySelector('.conversion-info');
const fileInput = document.getElementById('fileToUpload');
const startConversionBtn = document.getElementById('start-conversion-btn');
const form = document.getElementById('processing-form');
const convertButton = document.getElementById('pdfa-convert-button');
const statusContainer = document.getElementById('conversion-status');
const statusText = document.getElementById('conversion-status-text');
const statusIcon = document.getElementById('conversion-status-icon');

/**
 * Add or remove "hidden" CSS class to/from HTML element
 *
 * @param {HTMLElement} element
 * @param {boolean} hidden Boolean indicating if the hidden class should be added or removed
 */
function toggleElementHidden(element, hidden) {
  element.classList.toggle('hidden', hidden);
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
 * Replace convert button in DOM with download button
 *
 * @param {Object} data JSON object containing information about the conversion process
 */
function renderDownloadBtn(data) {
  if (!data || data.status !== 'success' || !data.downloadUrl) {
    return;
  }

  const btnContainer = document.querySelector('.control-buttons');

  // Remove the convert file button from DOM
  btnContainer.firstElementChild.remove();

  // Add download button to the DOM
  btnContainer.prepend(createDownloadBtn(btnContainer.dataset.readyLabel, data.downloadUrl));
};

/**
 * Create button used for downloading the converted file
 *
 * @param {string} text Text shown within the button
 * @param {string} url Url for the href attribute of the download link
 * @returns {HTMLButtonElement}
 */
function createDownloadBtn(text, url) {
  const downloadBtn = document.createElement('button');
  downloadBtn.classList.add('btn', 'btn-success');
  downloadBtn.textContent = text;

  const link = document.createElement('a');
  link.href = url;

  downloadBtn.addEventListener('click', (event) => {
    event.preventDefault();
    link.click();
  });

  return downloadBtn;
}

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

  renderDownloadBtn(data);
  statusContainer.querySelector('.spinner-border').remove();
  convertButton.innerHTML = convertButton.dataset.textContent;
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
  // TODO: it should be considered if the content of this message could be improved
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
      signal: AbortSignal.timeout(6000) // should be 3 min === 180000
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

  const formData = new FormData(form);

  // TODO: should this be moved to configs?
  formData.set('pdfa_convert', '1');

  convertButton.disabled = true;
  setStatus('info', inProgressText, true);
  toggleElementHidden(conversionInfoContainer, false);

  convertFile(formData, successText, failedText);
}

// Change event listener for the file input element
fileInput?.addEventListener('change', () => {
  if (fileInput.files.length > 0) {
    const label = document.querySelector('.file-upload label');
    const icon = document.createElement('i');
    icon.classList.add('bi', 'bi-cloud-upload');
    icon.setAttribute('ariaHidden', true);

    if (fileInput.files[0].type !== 'application/pdf') {
      // Add class in case it has been removed previously to hide the button
      startConversionBtn.classList.add('hidden');

      label.textContent = label.dataset.fileNotPdf;
      label.prepend(icon);
      icon.classList.add('error');
      label.classList.add('error', 'border-error');
      return;
    }

    // Remove classes in case they has been set previously to reset the look of the paragraph
    label.classList.remove('error');
    label.classList.remove('border-error');
    icon.classList.remove('error');

    const fileName = document.createElement('span');
    fileName.classList.add('fw-bold', 'd-block');
    fileName.textContent = fileInput.files[0].name;

    label.textContent = label.dataset.uploadedFile;
    label.prepend(icon);
    label.append(fileName);

    startConversionBtn.classList.remove('hidden');
  }
});

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
