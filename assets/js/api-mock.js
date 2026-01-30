import { readData, writeData, generateId } from "./storage.js";

export const getProducts = () => readData("products");
export const getCategories = () => readData("categories");
export const getUsers = () => readData("users");
export const getAdminUsers = () => readData("adminUsers");
export const getOrders = () => readData("orders");
export const getCoupons = () => readData("coupons");
export const getSettings = () => readData("settings", {});

export const saveProduct = (product) => {
  const products = getProducts();
  const existingIndex = products.findIndex((item) => item.id === product.id);
  if (existingIndex >= 0) {
    products[existingIndex] = { ...products[existingIndex], ...product };
  } else {
    products.push({ ...product, id: generateId("prod"), createdAt: new Date().toISOString(), sales: 0 });
  }
  writeData("products", products);
  return products;
};

export const deleteProduct = (id) => {
  const products = getProducts().filter((item) => item.id !== id);
  writeData("products", products);
  return products;
};

export const saveCategory = (category) => {
  const categories = getCategories();
  const existingIndex = categories.findIndex((item) => item.id === category.id);
  if (existingIndex >= 0) {
    categories[existingIndex] = { ...categories[existingIndex], ...category };
  } else {
    categories.push({ ...category, id: generateId("cat") });
  }
  writeData("categories", categories);
  return categories;
};

export const deleteCategory = (id) => {
  const categories = getCategories().filter((item) => item.id !== id);
  writeData("categories", categories);
  return categories;
};

export const saveCoupon = (coupon) => {
  const coupons = getCoupons();
  const existingIndex = coupons.findIndex((item) => item.id === coupon.id);
  if (existingIndex >= 0) {
    coupons[existingIndex] = { ...coupons[existingIndex], ...coupon };
  } else {
    coupons.push({ ...coupon, id: generateId("coupon") });
  }
  writeData("coupons", coupons);
  return coupons;
};

export const deleteCoupon = (id) => {
  const coupons = getCoupons().filter((item) => item.id !== id);
  writeData("coupons", coupons);
  return coupons;
};

export const saveSettings = (settings) => {
  writeData("settings", settings);
};

export const addOrder = (order) => {
  const orders = getOrders();
  orders.unshift({ ...order, id: generateId("order"), createdAt: new Date().toISOString() });
  writeData("orders", orders);
  return orders[0];
};

export const updateOrder = (orderId, updates) => {
  const orders = getOrders();
  const index = orders.findIndex((order) => order.id === orderId);
  if (index >= 0) {
    orders[index] = { ...orders[index], ...updates };
    writeData("orders", orders);
  }
  return orders[index];
};
