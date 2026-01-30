const cartBadge = document.querySelector(".badge");
const exploreButton = document.querySelector("#explore-button");
const offerButton = document.querySelector("#offers-button");
const navLinks = document.querySelectorAll(".nav__links a");
const categoryCards = document.querySelectorAll(".category-card");
const productGrid = document.querySelector("#product-grid");
const cartPanel = document.querySelector("#cart-panel");
const cartToggle = document.querySelector("#cart-toggle");
const cartClose = document.querySelector("#cart-close");
const cartOverlay = document.querySelector("#cart-overlay");
const cartItems = document.querySelector("#cart-items");
const cartTotal = document.querySelector("#cart-total");

const formatCurrency = (value) =>
  Number(value).toLocaleString("pt-BR", { style: "currency", currency: "BRL" });

const setActiveLink = (link) => {
  navLinks.forEach((item) => item.classList.remove("active"));
  link.classList.add("active");
};

navLinks.forEach((link) => {
  link.addEventListener("click", (event) => {
    if (!link.getAttribute("href")?.startsWith("#")) {
      return;
    }
    event.preventDefault();
    setActiveLink(link);
    const target = document.querySelector(link.getAttribute("href"));
    if (target) {
      target.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  });
});

const toggleCart = (open) => {
  if (!cartPanel || !cartOverlay) {
    return;
  }
  cartPanel.classList.toggle("cart--open", open);
  cartOverlay.classList.toggle("cart__overlay--visible", open);
  cartPanel.setAttribute("aria-hidden", String(!open));
};

cartToggle?.addEventListener("click", () => toggleCart(true));
cartClose?.addEventListener("click", () => toggleCart(false));
cartOverlay?.addEventListener("click", () => toggleCart(false));

const loadCart = () => {
  const saved = localStorage.getItem("cart");
  if (!saved) {
    return [];
  }
  try {
    return JSON.parse(saved);
  } catch (error) {
    return [];
  }
};

let cart = loadCart();

const updateBadge = () => {
  if (!cartBadge) {
    return;
  }
  const count = cart.reduce((total, item) => total + item.quantity, 0);
  cartBadge.textContent = String(count);
};

const renderCart = () => {
  if (!cartItems || !cartTotal) {
    return;
  }
  cartItems.innerHTML = "";
  let total = 0;

  cart.forEach((item) => {
    total += item.price * item.quantity;
    const card = document.createElement("div");
    card.className = "cart__item";
    card.innerHTML = `
      <strong>${item.name}</strong>
      <span>${item.quantity}x • ${formatCurrency(item.price)}</span>
      <button data-remove="${item.id}">Remover</button>
    `;
    card.querySelector("button")?.addEventListener("click", () => removeFromCart(item.id));
    cartItems.appendChild(card);
  });

  cartTotal.textContent = formatCurrency(total);
  updateBadge();
  localStorage.setItem("cart", JSON.stringify(cart));
};

const addToCart = (product) => {
  const existing = cart.find((item) => item.id === product.id);
  if (existing) {
    existing.quantity += 1;
  } else {
    cart.push({ ...product, quantity: 1 });
  }
  renderCart();
  toggleCart(true);
};

const removeFromCart = (productId) => {
  cart = cart.filter((item) => item.id !== productId);
  renderCart();
};

const getProductData = (card) => ({
  id: card.dataset.id,
  name: card.dataset.name,
  description: card.dataset.description,
  price: Number.parseFloat(card.dataset.price ?? "0"),
});

const bindProductActions = () => {
  if (!productGrid) {
    return;
  }
  productGrid.querySelectorAll(".product-card").forEach((card) => {
    const product = getProductData(card);
    card.querySelector("[data-add]")?.addEventListener("click", () => addToCart(product));
    card.querySelector("[data-details]")?.addEventListener("click", () => {
      alert(`${product.name}\n${product.description}\nPreço: ${formatCurrency(product.price)}`);
    });
  });
};

categoryCards.forEach((card) => {
  card.addEventListener("click", () => {
    categoryCards.forEach((item) => item.classList.remove("category-card--active"));
    card.classList.add("category-card--active");
    const category = card.dataset.category;
    document.querySelectorAll(".product-card").forEach((product) => {
      const matches = !category || product.dataset.category === category;
      product.style.display = matches ? "flex" : "none";
    });
    document.querySelector("#produtos")?.scrollIntoView({ behavior: "smooth", block: "start" });
  });
});

exploreButton?.addEventListener("click", () => {
  document.querySelector("#produtos")?.scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
});

offerButton?.addEventListener("click", () => {
  document.querySelector("#sobre")?.scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
});

bindProductActions();
renderCart();
