export type UpdateSettingsData = {
  grow_site_id: string;
};

export const updateSettings = async (data: UpdateSettingsData) => {
  const restNonce = growWPAdminData?.wpApi?.nonce;
  const restRoot = growWPAdminData?.wpApi?.root;
  if (!restRoot || !restNonce) {
    return;
  }

  const response = await fetch(`${restRoot}grow/v1/settings`, {
    method: "POST",
    headers: {
      "Content-type": "application/json",
      "X-WP-Nonce": restNonce,
    },
    body: JSON.stringify(data),
  });

  return await response.json();
};
