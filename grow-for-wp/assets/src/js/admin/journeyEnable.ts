import { handleStateChange } from "../utils/journeyPage";

export function setupButtons() {
  const enableApprove = document.querySelector('#grow-journey-enable-approve') as HTMLAnchorElement;

  if (!enableApprove) {
    // Don't attach on pages where button is not found.
    return;
  }

  const enableDecline = document.querySelector('#grow-journey-enable-decline') as HTMLAnchorElement;
  const successReturn = document.querySelector('#grow-journey-success-return') as HTMLAnchorElement;
  const errorCancel = document.querySelector('#grow-journey-error-cancel') as HTMLAnchorElement;
  const errorRetry = document.querySelector('#grow-journey-error-retry') as HTMLAnchorElement;

  enableApprove.addEventListener("click", handleEnableApprove);
  enableDecline.addEventListener("click", handleEnableDecline);
  successReturn.addEventListener("click", handleSuccessReturn);
  errorCancel.addEventListener("click", handleErrorCancel);
  errorRetry.addEventListener("click", handleErrorRetry);
}

const handleEnableApprove = (e:MouseEvent) => {
  const btn = (e.target as HTMLButtonElement);
  btn.disabled = true;
  btn.classList.add('disabled');

  if (!growWPAdminData.journeyData) {
    throw new Error('Unable to submit form data without nonce.');
  }

  fetch(ajaxurl, {
    method: 'POST',
    body: new URLSearchParams({
      action: 'grow_journey_enable',
      _wpnonce: growWPAdminData.journeyData.enableNonce
    })
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error when trying to enable Journey.');
      }
      response.json().then(data => {
        if (data.success) {
          handleStateChange('success');
        } else {
          handleStateChange('error');
        }
      });
    })
    .catch(error => {
      console.error('API error:', error);
      handleStateChange('error');
    })
    .finally(() => {
      btn.disabled = false;
      btn.classList.remove('disabled');
    });
};

const handleEnableDecline = () => {
  window.close();
};

const handleSuccessReturn = () => {
  window.close();
};

const handleErrorRetry = () => {
  handleStateChange('confirm');
};

const handleErrorCancel = () => {
  window.close();
};
