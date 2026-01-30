export const getQueryParams = () =>
  Object.fromEntries(new URLSearchParams(window.location.search));
