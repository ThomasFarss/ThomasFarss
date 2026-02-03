import { getUsers } from "./api-mock.js";

const USER_SESSION_KEY = "helpeUserSession";

export const loginUser = (email, password) => {
  const user = getUsers().find((item) => item.email === email && item.password === password);
  if (!user) return null;
  sessionStorage.setItem(USER_SESSION_KEY, JSON.stringify({ id: user.id, email: user.email }));
  return user;
};

export const logoutUser = () => sessionStorage.removeItem(USER_SESSION_KEY);

export const getUserSession = () => {
  const raw = sessionStorage.getItem(USER_SESSION_KEY);
  return raw ? JSON.parse(raw) : null;
};

export const guardUserPage = () => {
  if (!getUserSession()) {
    window.location.href = "login.html";
  }
};
