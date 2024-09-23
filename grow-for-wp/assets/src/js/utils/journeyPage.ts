/**
 * Change current state of Journey confirmation page.
 *
 * @param {string} state The page state to change to.
 */
export function handleStateChange(state:string) {
  const growJourneyForm = document.querySelector('.grow-admin-page.grow-journey') as HTMLDivElement;
  growJourneyForm.classList.remove('grow-journey__state-confirm');
  growJourneyForm.classList.remove('grow-journey__state-success');
  growJourneyForm.classList.remove('grow-journey__state-error');
  growJourneyForm.classList.add('grow-journey__state-' + state);
}
