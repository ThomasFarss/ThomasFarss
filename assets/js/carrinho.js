import { getProducts, getCoupons } from "./api-mock.js";
import { formatPrice } from "./catalogo.js";

const CART_KEY = "helpeCart";

export const getCart = () => {
  const raw = localStorage.getItem(CART_KEY);
  if (!raw) return { items: [], coupon: null };
  return JSON.parse(raw);
};

export const saveCart = (cart) => {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
};

export const addToCart = (productId) => {
  const cart = getCart();
  const existing = cart.items.find((item) => item.productId === productId);
  if (existing) {
    existing.quantity += 1;
  } else {
    cart.items.push({ productId, quantity: 1 });
  }
  saveCart(cart);
};

export const updateQuantity = (productId, quantity) => {
  const cart = getCart();
  const item = cart.items.find((entry) => entry.productId === productId);
  if (item) item.quantity = Math.max(1, quantity);
  saveCart(cart);
};

export const removeFromCart = (productId) => {
  const cart = getCart();
  cart.items = cart.items.filter((item) => item.productId !== productId);
  saveCart(cart);
};

export const applyCoupon = (code) => {
  const coupon = getCoupons().find((item) => item.code === code && item.active);
  const cart = getCart();
  if (coupon) {
    cart.coupon = coupon;
  } else {
    cart.coupon = null;
  }
  saveCart(cart);
  return coupon;
};

export const getCartDetails = () => {
  const cart = getCart();
  const products = getProducts();
  const items = cart.items.map((item) => {
    const product = products.find((prod) => prod.id === item.productId);
    return {
      ...item,
      product,
      subtotal: product ? product.price * item.quantity : 0
    };
  });

  const subtotal = items.reduce((sum, item) => sum + item.subtotal, 0);
  let discount = 0;
  if (cart.coupon) {
    if (cart.coupon.type === "percent") {
      discount = (subtotal * cart.coupon.value) / 100;
    }
    if (cart.coupon.type === "value") {
      discount = cart.coupon.value;
    }
  }
  const total = Math.max(subtotal - discount, 0);
  return {
    items,
    subtotal,
    discount,
    total,
    coupon: cart.coupon,
    formattedSubtotal: formatPrice(subtotal),
    formattedDiscount: formatPrice(discount),
    formattedTotal: formatPrice(total)
  };
};
