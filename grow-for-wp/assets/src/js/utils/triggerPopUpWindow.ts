import { WindowFeatures } from "./types";

const DEFAULT_FEATURES: WindowFeatures = {
  width: 980,
  height: 820,
  popup: true,
  top: 60,
};

/**
 * Open Popup
 * @param {Element} triggerButton The button element that was clicked on
 * @param {string} target Target value for the window
 * @param {Partial<WindowFeatures> }features triggerButton The button element that was clicked on
 * @returns {Window}
 */
export function triggerPopUpWindow(
  triggerButton: HTMLAnchorElement,
  target = "_blank",
  features: Partial<WindowFeatures> = {}
) {
  triggerButton.blur();
  const popupFeatures: WindowFeatures = Object.assign(
    {},
    DEFAULT_FEATURES,
    features
  );
  return window.open(
    triggerButton.href || (triggerButton.getAttribute("data-href") ?? ""),
    target,
    processFeatures(popupFeatures)
  );
}

/**
 * Turn a window features object into a string that can be used to open a window
 * @param features
 * @returns {string}
 */
const processFeatures = (features: WindowFeatures): string => {
  let featureString = (
    Object.keys(features) as Array<keyof WindowFeatures>
  ).reduce(function featureReduce(a, feature: keyof WindowFeatures) {
    return `${a}${feature}=${getFeatureStringValue(features[feature])},`;
  }, "");
  if (!Object.prototype.hasOwnProperty.call(features, "left")) {
    featureString = `${featureString}left=${
      (window.innerWidth - features.width) / 2
    }`;
  }
  return featureString;
};

/**
 * Convert feature boolean values to a string yes or no, but leave non-boolean values intact
 * @param value
 * @returns {string|*}
 */
const getFeatureStringValue: (value: boolean | number) => string = (value) => {
  if (typeof value !== "boolean") {
    return `${value}`;
  }

  if (value) {
    return "yes";
  }

  return "no";
};
