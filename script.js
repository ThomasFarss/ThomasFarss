const cartBadge = document.querySelector(".badge");
const exploreButton = document.querySelector("#explore-button");
const offerButton = document.querySelector("#offers-button");
const navLinks = document.querySelectorAll(".nav__links a");
const categoryCards = document.querySelectorAll(".category-card");
const productGrid = document.querySelector("#product-grid");
const adminForm = document.querySelector("#admin-form");
const adminItems = document.querySelector("#admin-items");
const cartPanel = document.querySelector("#cart-panel");
const cartToggle = document.querySelector("#cart-toggle");
const cartClose = document.querySelector("#cart-close");
const cartOverlay = document.querySelector("#cart-overlay");
const cartItems = document.querySelector("#cart-items");
const cartTotal = document.querySelector("#cart-total");

const defaultProducts = [
  {
    id: "xbox-gift",
    name: "Gift Card Xbox R$ 50",
    price: 50,
    category: "Gift Cards",
    description: "Entrega instantânea",
    image:
      "https://images.unsplash.com/photo-1606813907291-d86efa86ca65?auto=format&fit=crop&w=400&q=80",
    tag: "Destaque",
    discount: 0,
  },
  {
    id: "steam-gift",
    name: "Steam Gift Card R$ 200",
    price: 200,
    category: "Gift Cards",
    description: "Entrega rápida",
    image:
      "https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=400&q=80",
    tag: "Destaque",
    discount: 10,
  },
  {
    id: "eshop-gift",
    name: "Nintendo eShop R$ 100",
    price: 100,
    category: "Jogos Digitais",
    description: "Oficial e seguro",
    image:
      "https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=400&q=80",
    tag: "Destaque",
    discount: 0,
  },
  {
    id: "indie-pack",
    name: "Pacote de Jogos Indie",
    price: 79.9,
    category: "Jogos Digitais",
    description: "Seleção premium",
    image:
      "https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=400&q=80",
    tag: "Destaque",
    discount: 40,
  },
];

const loadState = (key, fallback) => {
  const saved = localStorage.getItem(key);
  if (!saved) {
    return fallback;
  }
  try {
    return JSON.parse(saved);
  } catch (error) {
    return fallback;
  }
};

const saveState = (key, value) => {
  localStorage.setItem(key, JSON.stringify(value));
};

let products = loadState("products", defaultProducts);
let cart = loadState("cart", []);

const formatCurrency = (value) =>
  value.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });

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
  saveState("cart", cart);
};

const addToCart = (productId) => {
  const existing = cart.find((item) => item.id === productId);
  if (existing) {
    existing.quantity += 1;
  } else {
    const product = products.find((item) => item.id === productId);
    if (!product) {
      return;
    }
    cart.push({
      id: product.id,
      name: product.name,
      price: product.price,
      quantity: 1,
    });
  }
  renderCart();
  toggleCart(true);
};

const removeFromCart = (productId) => {
  cart = cart.filter((item) => item.id !== productId);
  renderCart();
};

const renderProducts = (list = products) => {
  if (!productGrid) {
    return;
  }
  productGrid.innerHTML = "";
  list.forEach((product) => {
    const card = document.createElement("article");
    card.className = "product-card";
    card.innerHTML = `
      ${product.tag ? `<span class="tag">${product.tag}</span>` : ""}
      ${product.discount ? `<span class="discount">-${product.discount}%</span>` : ""}
      <img src="${product.image}" alt="${product.name}" />
      <div class="product-card__body">
        <h3>${product.name}</h3>
        <p>${product.description}</p>
        <span class="product-card__price">${formatCurrency(product.price)}</span>
        <div class="product-card__actions">
          <button class="add" data-add="${product.id}">Adicionar</button>
          <button class="details" data-details="${product.id}">Detalhes</button>
        </div>
      </div>
    `;
    card.querySelector("[data-add]")?.addEventListener("click", () => addToCart(product.id));
    card.querySelector("[data-details]")?.addEventListener("click", () =>
      alert(`${product.name}\n${product.description}\nPreço: ${formatCurrency(product.price)}`)
    );
    productGrid.appendChild(card);
  });
};

const renderAdminList = () => {
  if (!adminItems) {
    return;
  }
  adminItems.innerHTML = "";
  products.forEach((product) => {
    const row = document.createElement("div");
    row.className = "admin__item";
    row.innerHTML = `
      <div>
        <span>${product.name}</span>
        <div>${product.category} • ${formatCurrency(product.price)}</div>
      </div>
      <button data-delete="${product.id}">Excluir</button>
    `;
    row.querySelector("button")?.addEventListener("click", () => {
      products = products.filter((item) => item.id !== product.id);
      saveState("products", products);
      renderProducts();
      renderAdminList();
    });
    adminItems.appendChild(row);
  });
};

adminForm?.addEventListener("submit", (event) => {
  event.preventDefault();
  const formData = new FormData(adminForm);
  const newProduct = {
    id: `prod-${Date.now()}`,
    name: String(formData.get("name")),
    price: Number.parseFloat(String(formData.get("price"))),
    category: String(formData.get("category")),
    image: String(formData.get("image")),
    description: String(formData.get("description")),
    tag: String(formData.get("tag")) || "",
    discount: Number.parseInt(String(formData.get("discount")) || "0", 10),
  };

  products.unshift(newProduct);
  saveState("products", products);
  renderProducts();
  renderAdminList();
  adminForm.reset();
});

categoryCards.forEach((card) => {
  card.addEventListener("click", () => {
    categoryCards.forEach((item) => item.classList.remove("category-card--active"));
    card.classList.add("category-card--active");
    const category = card.dataset.category;
    const filtered = category ? products.filter((item) => item.category === category) : products;
    renderProducts(filtered);
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

renderProducts();
renderAdminList();
renderCart();
