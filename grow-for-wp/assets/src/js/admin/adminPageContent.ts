import { PageState, AdminPageCopyMap } from "../utils/types";
import { triggerPopUpWindow } from "../utils/triggerPopUpWindow";
import { handleDisconnect, handleSignOut } from "./remoteAuth";

export const PAGE_SELECTOR = ".grow-admin-page";
export const CONNECTED_SITE_SELECTOR = ".grow-admin-page__connected-site";
export const TITLE_SELECTOR = ".grow-admin-page__title";
export const SUBTITLE_SELECTOR = ".grow-admin-page__subtitle";
export const PRIMARY_BUTTON_SELECTOR = ".grow-admin-page__primary-button";
export const SECONDARY_BUTTON_SELECTOR = ".grow-admin-page__secondary-button";

export const updateContent = (
  pageState: PageState,
  readerName: string | null = null
) => {
  if (!growWPAdminData.adminPageCopy) {
    return;
  }
  const isConnected = pageState !== "disconnected";
  const isAuthenticated = pageState === "authenticated";
  setConnectedClass(isConnected);
  setAuthenticatedClass(isAuthenticated);
  generateElementCopyMap({
    copy: growWPAdminData.adminPageCopy[pageState],
    readerName,
    isAuthenticated,
  }).map(setElementContent);
  if (growWPAdminData.adminPageCopy.featureLinks) {
    Object.keys(growWPAdminData.adminPageCopy.featureLinks).map((slug) => {
      updateFeatureLink(isAuthenticated, slug);
    });
  }
};

interface GenerateElementCopyMapProps {
  copy: AdminPageCopyMap;
  readerName: string | null;
  isAuthenticated: boolean;
}
function generateElementCopyMap({
  copy,
  readerName,
  isAuthenticated,
}: GenerateElementCopyMapProps) {
  return [
    {
      element: document.querySelector(CONNECTED_SITE_SELECTOR),
      content: copy.connectedSite,
    },
    {
      element: document.querySelector(TITLE_SELECTOR),
      content:
        isAuthenticated && readerName
          ? `Hi, ${readerName}! ${copy.title}`
          : copy.title,
    },
    {
      element: document.querySelector(SUBTITLE_SELECTOR),
      content: copy.subtitle,
    },
    {
      element: document.querySelector(PRIMARY_BUTTON_SELECTOR),
      content: copy.primaryButtonText,
      href: isAuthenticated
        ? buildSiteRoute('/settings/')
        : `${growWPAdminData.growRemote?.publisherDashboard}/wp-auth?domain=${growWPAdminData.siteDomain}&path=${growWPAdminData.siteBasePath ?? ''}&title=${growWPAdminData.siteTitle}`,
    },
    {
      element: document.querySelector(`${SECONDARY_BUTTON_SELECTOR} > span`),
      content: copy.secondaryButtonText,
    },
  ];
}

export function setupButtons() {
  const primaryButton = document.querySelector(
    PRIMARY_BUTTON_SELECTOR
  ) as HTMLAnchorElement;

  if (!primaryButton) {
    // Don't attach on pages where button is not found.
    return;
  }

  const secondaryButton = document.querySelector(
    SECONDARY_BUTTON_SELECTOR
  ) as HTMLAnchorElement;

  primaryButton.addEventListener("click", (e) => {
    if (!getIsAuthenticated(document
        .querySelector(PAGE_SELECTOR))) {
      e.preventDefault();
      triggerPopUpWindow(primaryButton, "growWPAuthWindow");
    }
  });
  secondaryButton.addEventListener("click", (e) => {
    document.querySelector(".grow-admin-page--is-authenticated")
      ? handleSignOut(e)
      : handleDisconnect(e);
  });
  setupCollapseButtons();
}

export function setupCollapseButtons() {
  [...document.getElementsByClassName("collapse-button")].map((button) => {
    const section = document.getElementById(
      button.getAttribute("aria-controls") ?? ""
    );
    section?.style.setProperty(
      "--section-height",
      `${section?.scrollHeight ?? 0}px`
    );
    section?.addEventListener("transitionstart", () => {
      if (section.getAttribute("aria-hidden") === "true") {
        section.style.setProperty("overflow", "hidden");
      }
    });
    section?.addEventListener("transitionend", () => {
      if (section.getAttribute("aria-hidden") === "false") {
        section.style.setProperty("overflow", "visible");
      }
    });
    button.addEventListener("click", () => {
      section?.style.setProperty(
        "--section-height",
        `${section?.scrollHeight ?? 0}px`
      );
      section && toggleSection(section, button);
    });
  });
}

const toggleSection = (section: HTMLElement, button: Element) => {
  const expanded = section.getAttribute("aria-hidden") === "false";
  if (expanded) {
    button.setAttribute("aria-expanded", "false");
    section.setAttribute("aria-hidden", "true");
  } else {
    button.setAttribute("aria-expanded", "true");
    section.setAttribute("aria-hidden", "false");
  }
};

const updateFeatureLink: (isAuthenticated: boolean, slug: string) => void = (
  isAuthenticated,
  slug
) => {
  document
    .getElementsByClassName(`grow-admin-page__feature--${slug}`)[0]
    ?.setAttribute(
      "href",
      isAuthenticated
        ? buildSiteRoute(
            growWPAdminData.adminPageCopy.featureLinks[slug].settingsRoute
          )
        : growWPAdminData.adminPageCopy.featureLinks[slug].helpLink
    );
};

export const buildSiteRoute = (route: string) => {
  return `${growWPAdminData.growRemote?.publisherDashboard}/dashboard/sites/${growWPAdminData.growSiteId}${route}`;
};

const setElementContent = ({
  element,
  content,
  href,
}: {
  element: Element | null;
  content: string;
  href?: string;
}) => {
  if (element) {
    element.innerHTML = content;
    if (element.getAttribute("href") && href) {
      element.setAttribute("href", href);
    }
  }
};

const setConnectedClass = (add = true) =>
  document
    .querySelector(PAGE_SELECTOR)
    ?.classList[add ? "add" : "remove"](
      `${PAGE_SELECTOR.substring(1)}--is-connected`
    );

const setAuthenticatedClass = (add = true) =>
  document
    .querySelector(PAGE_SELECTOR)
    ?.classList[add ? "add" : "remove"](
      `${PAGE_SELECTOR.substring(1)}--is-authenticated`
    );

export const getIsAuthenticated = (pageElement : Element | null) => {
  return pageElement?.classList.contains(
      `${PAGE_SELECTOR.substring(1)}--is-authenticated`
  );
}
