import { addOrder } from "./api-mock.js";
import { getCartDetails, getCart } from "./carrinho.js";
import { getUserSession } from "./auth.js";

export const finalizeOrder = (paymentMethod) => {
  const cartDetails = getCartDetails();
  const session = getUserSession();
  if (!session) return null;

  const order = {
    userId: session.id,
    status: "novo",
    paymentMethod,
    items: cartDetails.items.map((item) => ({
      productId: item.productId,
      name: item.product?.name,
      quantity: item.quantity,
      price: item.product?.price
    })),
    subtotal: cartDetails.subtotal,
    discount: cartDetails.discount,
    total: cartDetails.total,
    coupon: cartDetails.coupon?.code || null
  };

  const createdOrder = addOrder(order);

  const cart = getCart();
  cart.items = [];
  cart.coupon = null;
  localStorage.setItem("helpeCart", JSON.stringify(cart));

  return createdOrder;
};
