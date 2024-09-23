import { convertExpiresInToUtcTime } from "../utils/convertExpiresInToUtcTime";
import { updateSettings } from "../utils/updateSettings";
import { updateContent } from "./adminPageContent";

const ACCESS_TOKEN_KEY = "growAccessTokenData";
const GROW_WP_AUTH_ROUTE = "/wp-auth/token/";
const REFRESH_TOKEN_GRANT_TYPE = "refresh_token";

const MINUTE_IN_MS = 60000;

export type AccessTokenData = {
  accessToken: string | null;
  refreshToken: string | null;
  readerName: string | null;
  expirationTime: string | null;
};

export async function initializeTokenRefresh(
  refreshToken: string,
  tokenExpirationInterval: number
) {
  if (tokenExpirationInterval < MINUTE_IN_MS) {
    await refreshAccessToken(refreshToken);
    return;
  }
  setupRefreshTimeout();
}

export const getExpirationInterval = (expirationTime: string | null) =>
  Date.parse(expirationTime ?? "") - Date.now();

export function loadTokenData(): AccessTokenData {
  const data = localStorage.getItem(ACCESS_TOKEN_KEY);
  if (!data) {
    return {
      accessToken: null,
      expirationTime: null,
      refreshToken: null,
      readerName: null,
    };
  }
  return JSON.parse(data);
}

export function saveTokenData({
  accessToken,
  refreshToken,
  readerName,
  expirationTime,
}: AccessTokenData) {
  localStorage.setItem(
    ACCESS_TOKEN_KEY,
    JSON.stringify({
      accessToken,
      refreshToken,
      readerName,
      expirationTime,
    })
  );
}

let refreshTimeout: ReturnType<typeof setTimeout>;

function setupRefreshTimeout() {
  if (refreshTimeout) {
    clearTimeout(refreshTimeout);
  }

  const { expirationTime, refreshToken } = loadTokenData();
  if (!expirationTime || !refreshToken) {
    return;
  }

  const timeoutMs = Date.parse(expirationTime) - Date.now() - MINUTE_IN_MS;
  refreshTimeout = setTimeout(() => {
    return refreshAccessToken(refreshToken);
  }, timeoutMs);
}

export async function refreshAccessToken(refreshToken: string) {
  saveTokenData(await fetchNewAccessToken(refreshToken));
}

interface RefreshTokensResponse {
  access_token: string;
  refresh_token: string;
  expires_in: number;
  reader_name: string;
}

export async function fetchNewAccessToken(
  refreshToken: string
): Promise<AccessTokenData> {
  const response = await fetch(
    `${growWPAdminData?.growRemote?.apiRoot}${GROW_WP_AUTH_ROUTE}`,
    {
      method: "POST",
      body: new URLSearchParams({
        refresh_token: refreshToken,
        grant_type: REFRESH_TOKEN_GRANT_TYPE,
      }),
      headers: {
        "content-type": "application/x-www-form-urlencoded",
      },
    }
  );

  const body: RefreshTokensResponse | { error: string } = await response.json();
  if ("error" in body) {
    throw new Error(body.error);
  }

  return {
    refreshToken: body.refresh_token,
    accessToken: body.access_token,
    readerName: body.reader_name,
    expirationTime: convertExpiresInToUtcTime(body.expires_in),
  };
}

type AuthMessageData = {
  siteId: string;
  accessToken: string;
  refreshToken: string;
  readerName: string;
  expiresIn: string;
};

export async function handleAuthMessage({
  origin,
  data,
}: {
  origin: string;
  data: AuthMessageData;
}) {
  if (origin !== growWPAdminData.growRemote?.publisherDashboard) {
    return;
  }
  const { siteId, accessToken, readerName, refreshToken, expiresIn } = data;
  updateSettings({ grow_site_id: siteId });
  saveTokenData({
    accessToken,
    refreshToken,
    readerName,
    expirationTime: convertExpiresInToUtcTime(expiresIn),
  });
  growWPAdminData.growSiteId = siteId;
  updateContent("authenticated", readerName);
}

export function handleDisconnect(e?: MouseEvent) {
  e && e.preventDefault();
  updateSettings({ grow_site_id: "" });
  saveTokenData({
    accessToken: null,
    refreshToken: null,
    readerName: null,
    expirationTime: null,
  });
  updateContent("disconnected");
}

export function handleSignOut(e?: MouseEvent) {
  e && e.preventDefault();
  saveTokenData({
    accessToken: null,
    refreshToken: null,
    readerName: null,
    expirationTime: null,
  });
  updateContent("connected");
}
