import { getUsers, getAdminUsers } from "./api-mock.js";

const USER_SESSION_KEY = "helpeUserSession";
const ADMIN_SESSION_KEY = "helpeAdminSession";

export const loginUser = (email, password) => {
  const user = getUsers().find((item) => item.email === email && item.password === password);
  if (!user) return null;
  sessionStorage.setItem(USER_SESSION_KEY, JSON.stringify({ id: user.id, email: user.email }));
  return user;
};

export const loginAdmin = (email, password) => {
  const admin = getAdminUsers().find((item) => item.email === email && item.password === password);
  if (!admin) return null;
  sessionStorage.setItem(ADMIN_SESSION_KEY, JSON.stringify({ id: admin.id, email: admin.email }));
  return admin;
};

export const logoutUser = () => sessionStorage.removeItem(USER_SESSION_KEY);
export const logoutAdmin = () => sessionStorage.removeItem(ADMIN_SESSION_KEY);

export const getUserSession = () => {
  const raw = sessionStorage.getItem(USER_SESSION_KEY);
  return raw ? JSON.parse(raw) : null;
};

export const getAdminSession = () => {
  const raw = sessionStorage.getItem(ADMIN_SESSION_KEY);
  return raw ? JSON.parse(raw) : null;
};

export const guardUserPage = () => {
  if (!getUserSession()) {
    window.location.href = "login.html";
  }
};

export const guardAdminPage = () => {
  if (!getAdminSession()) {
    window.location.href = "admin-login.html";
  }
};
