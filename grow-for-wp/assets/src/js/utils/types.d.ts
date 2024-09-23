export type GrowWPAdminData = {
  wpApi?: WordPressApiData;
  growRemote?: GrowRemoteData;
  adminPageCopy?: AllCopyState;
  journeyData?: JourneyData;
  siteDomain?: string;
  siteBasePath?: string;
  siteTitle?: string;
  growSiteId?: string;
};

export type PageState = keyof Omit<AdminPageCopy, "featureLinks">;

type WordPressApiData = {
  root: string;
  nonce: string;
};

type GrowRemoteData = {
  apiRoot: string;
  publisherDashboard: string;
};

type JourneyData = {
  enableNonce: string;
  disableNonce: string;
  troubleshootNonce: string;
};

type AdminPageCopy = {
  authenticated: AdminPageCopyMap;
  connected: AdminPageCopyMap;
  disconnected: AdminPageCopyMap;
  featureLinks: {
    [key: string]: {
      helpLink: string;
      settingsRoute: string;
    };
  };
};

export type AdminPageCopyMap = {
  connectedSite: string;
  title: string;
  subtitle: string;
  primaryButtonText: string;
  secondaryButtonText: string;
};

export interface WindowFeatures {
  width: number;
  height: number;
  popup: boolean;
  top: number;
}
