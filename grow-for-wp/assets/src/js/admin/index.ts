import {
  initializeTokenRefresh,
  loadTokenData,
  handleAuthMessage,
  getExpirationInterval,
} from "./remoteAuth";
import { updateContent, setupButtons } from "./adminPageContent";
import { setupButtons as setupJourneyEnableButtons } from "./journeyEnable";
import { setupButtons as setupJourneyDisableButtons } from "./journeyDisable";
import { setupButtons as setupJourneyTroubleshootButtons } from "./journeyTroubleshoot";
import { GrowWPAdminData } from "../utils/types";
declare global {
  let growWPAdminData: GrowWPAdminData, ajaxurl: string;
}

/**
 * Setup admin landing page functionality.
 */
async function initAdmin() {
  window.addEventListener("message", handleAuthMessage);
  setupButtons();
  const { refreshToken, accessToken, expirationTime, readerName } = loadTokenData();

  if (!refreshToken || !accessToken || !expirationTime) {
    return;
  }
  const tokenExpirationInterval = getExpirationInterval(expirationTime);
  if (tokenExpirationInterval > 0) {
    updateContent("authenticated", readerName);
  }
  initializeTokenRefresh(refreshToken, tokenExpirationInterval);
}

/**
 * Setup Journey pages' functionality.
 */
async function initJourney() {
  setupJourneyEnableButtons();
  setupJourneyDisableButtons();
  setupJourneyTroubleshootButtons();
}

void initAdmin();
void initJourney();
