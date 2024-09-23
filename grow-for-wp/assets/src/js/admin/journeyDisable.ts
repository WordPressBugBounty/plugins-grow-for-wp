import { handleStateChange } from "../utils/journeyPage";

export function setupButtons() {
  const disableConfirm = document.querySelector('#grow-journey-disable-approve') as HTMLAnchorElement;

  if (!disableConfirm) {
    // Don't attach on pages where button is not found.
    return;
  }

  const disableDecline = document.querySelector('#grow-journey-disable-decline') as HTMLAnchorElement;
  const successReturn = document.querySelector('#grow-journey-success-return') as HTMLAnchorElement;

  disableConfirm.addEventListener("click", handleDisableConfirm);
  disableDecline.addEventListener("click", handleDisableDecline);
  successReturn.addEventListener("click", handleSuccessReturn);
}

const handleDisableConfirm = (e:MouseEvent) => {
  const btn = (e.target as HTMLButtonElement);
  btn.disabled = true;
  btn.classList.add('disabled');

  if (!growWPAdminData.journeyData) {
    throw new Error('Unable to submit form data without nonce.');
  }

  fetch(ajaxurl, {
    method: 'POST',
    body: new URLSearchParams({
      action: 'grow_journey_disable',
      _wpnonce: growWPAdminData.journeyData.disableNonce
    })
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error when trying to disable Journey.');
      }
      response.json().then(() => {
          handleStateChange('success');
      });
    })
    .catch(error => {
      console.error('API error:', error);
    })
    .finally(() => {
      btn.disabled = false;
      btn.classList.remove('disabled');
    });
};

const handleDisableDecline = () => {
  window.close();
};

const handleSuccessReturn = () => {
  window.location.href = `//${growWPAdminData.siteDomain}${growWPAdminData.siteBasePath ?? ''}/wp-admin/admin.php?page=grow`;
};
