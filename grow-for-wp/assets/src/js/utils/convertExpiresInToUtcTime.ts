export function convertExpiresInToUtcTime(expiresIn: number | string): string {
  return new Date(Date.now() + Number(expiresIn) * 1000).toUTCString();
}
