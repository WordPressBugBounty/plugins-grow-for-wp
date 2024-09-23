import { handleStateChange } from "../utils/journeyPage";

export function setupButtons() {
  const troubleshootApprove = document.querySelector('#grow-journey-troubleshoot-approve') as HTMLAnchorElement;

  if (!troubleshootApprove) {
    // Don't attach on pages where button is not found.
    return;
  }

  const troubleshootDecline = document.querySelector('#grow-journey-troubleshoot-decline') as HTMLAnchorElement;
  const successReturn = document.querySelector('#grow-journey-success-return') as HTMLAnchorElement;
  const errorCancel = document.querySelector('#grow-journey-error-cancel') as HTMLAnchorElement;
  const errorRetry = document.querySelector('#grow-journey-error-retry') as HTMLAnchorElement;

  troubleshootApprove.addEventListener("click", handleTroubleshootApprove);
  troubleshootDecline.addEventListener("click", handleTroubleshootDecline);
  successReturn.addEventListener("click", handleSuccessReturn);
  errorCancel.addEventListener("click", handleErrorCancel);
  errorRetry.addEventListener("click", handleErrorRetry);
}

const handleTroubleshootApprove = (e:MouseEvent) => {
  const btn = (e.target as HTMLButtonElement);
  btn.disabled = true;
  btn.classList.add('disabled');

  if (!growWPAdminData.journeyData) {
    throw new Error('Unable to submit form data without nonce.');
  }

  fetch(ajaxurl, {
    method: 'POST',
    body: new URLSearchParams({
      action: 'grow_journey_troubleshoot',
      _wpnonce: growWPAdminData.journeyData.troubleshootNonce
    })
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error when trying to troubleshoot Journey.');
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

const handleTroubleshootDecline = () => {
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
