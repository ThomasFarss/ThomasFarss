import { initStorage } from "./storage.js";
import { getSettings, getCategories, getProducts, getUsers, getOrders } from "./api-mock.js";
import { addToCart, getCartDetails, updateQuantity, removeFromCart, applyCoupon } from "./carrinho.js";
import { filterProducts, paginate, renderProductCard, renderCategoryOptions, formatPrice } from "./catalogo.js";
import { getQueryParams } from "./router.js";
import { loginUser, logoutUser, guardUserPage, getUserSession } from "./auth.js";

const applyBranding = () => {
  const settings = getSettings();
  if (!settings) return;
  const root = document.documentElement;
  if (settings.primaryColor) root.style.setProperty("--color-primary", settings.primaryColor);
  if (settings.secondaryColor) root.style.setProperty("--color-secondary", settings.secondaryColor);
  if (settings.accentColor) root.style.setProperty("--color-accent", settings.accentColor);
};

const renderNavbar = () => {
  const nav = document.querySelector("#navbar");
  if (!nav) return;
  const session = getUserSession();
  const cart = getCartDetails();

  nav.innerHTML = `
    <div class="topbar">
      <div class="container topbar-inner">
        <span>Entrega digital imediata • Suporte 24/7</span>
        <span class="badge">PIX com desconto</span>
      </div>
    </div>
    <nav class="navbar">
      <div class="container nav-inner">
        <div class="brand">
          <a href="index.html"><strong>Helpe Lojas</strong></a>
          <small>Gift cards e produtos digitais</small>
        </div>
        <div class="search-bar">
          <input class="input" id="nav-search" placeholder="Buscar produtos, cartões, assinaturas">
          <button class="button" id="nav-search-btn">Buscar</button>
        </div>
        <div class="nav-actions">
          <a href="categoria.html">Categorias</a>
          <a href="conta.html">Minha conta</a>
          <a href="carrinho.html">Carrinho <span class="badge-counter" id="cart-count">${
            cart.items.length
          }</span></a>
          ${
            session
              ? `<button class="button secondary" id="logout-btn">Sair</button>`
              : `<a class="button" href="login.html">Entrar</a>`
          }
        </div>
      </div>
    </nav>
  `;
  const logoutBtn = nav.querySelector("#logout-btn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", () => {
      logoutUser();
      window.location.reload();
    });
  }

  const searchBtn = nav.querySelector("#nav-search-btn");
  searchBtn.addEventListener("click", () => {
    const term = nav.querySelector("#nav-search").value;
    window.location.href = `categoria.html?search=${encodeURIComponent(term)}`;
  });
};

const renderFooter = () => {
  const footer = document.querySelector("#footer");
  if (!footer) return;
  const settings = getSettings();
  footer.innerHTML = `
    <div class="footer">
      <div class="container grid grid-3">
        <div>
          <h4>${settings.storeName || "Helpe Lojas"}</h4>
          <p class="text-muted">Especialistas em produtos digitais, gift cards e assinaturas.</p>
        </div>
        <div>
          <h4>Suporte</h4>
          <p class="text-muted">${settings.contactEmail || "contato@helplojas.com"}</p>
          <p class="text-muted">${settings.contactPhone || "(11) 99999-0000"}</p>
        </div>
        <div>
          <h4>Políticas</h4>
          <a href="termos.html">Termos de uso</a><br>
          <a href="politica.html">Política de privacidade</a>
        </div>
      </div>
    </div>
  `;
};

const updateCartCount = () => {
  const counter = document.querySelector("#cart-count");
  if (!counter) return;
  counter.textContent = getCartDetails().items.length;
};

const setupAddToCart = () => {
  document.addEventListener("click", (event) => {
    const button = event.target.closest("[data-add-cart]");
    if (!button) return;
    addToCart(button.dataset.addCart);
    updateCartCount();
  });
};

const setupHomePage = () => {
  const banner = document.querySelector("#home-banner");
  const featured = document.querySelector("#featured-products");
  const categoriesContainer = document.querySelector("#category-sections");
  const settings = getSettings();
  const products = getProducts();

  if (banner && settings.banners?.length) {
    const hero = settings.banners[0];
    banner.innerHTML = `
      <div class="hero">
        <div>
          <span class="badge">Destaque</span>
          <h1>${hero.title}</h1>
          <p class="text-muted">${hero.subtitle}</p>
          <a class="button" href="categoria.html">${hero.cta}</a>
        </div>
        <img src="${hero.image}" alt="${hero.title}">
      </div>
    `;
  }

  if (featured) {
    featured.innerHTML = products.slice(0, 4).map(renderProductCard).join("");
  }

  if (categoriesContainer) {
    const categories = getCategories();
    categoriesContainer.innerHTML = categories
      .map((category) => {
        const items = products.filter((product) => product.category === category.slug).slice(0, 3);
        return `
          <section class="section">
            <div class="section-title">
              <h2>${category.name}</h2>
              <a href="categoria.html?category=${category.slug}">Ver tudo</a>
            </div>
            <div class="grid grid-3">
              ${items.map(renderProductCard).join("")}
            </div>
          </section>
        `;
      })
      .join("");
  }
};

const setupCategoryPage = () => {
  const list = document.querySelector("#category-list");
  const select = document.querySelector("#category-filter");
  const search = document.querySelector("#category-search");
  const sort = document.querySelector("#category-sort");
  const pagination = document.querySelector("#pagination");
  const params = getQueryParams();
  let currentPage = 1;

  renderCategoryOptions(select);
  if (params.category) select.value = params.category;
  if (params.search) search.value = params.search;

  const render = () => {
    const products = filterProducts({
      category: select.value,
      search: search.value,
      sort: sort.value
    });
    const { items, totalPages } = paginate(products, currentPage, 6);
    list.innerHTML = items.map(renderProductCard).join("") || "<p>Nenhum produto encontrado.</p>";
    pagination.innerHTML = Array.from({ length: totalPages }, (_, index) => {
      const page = index + 1;
      return `<button class="button outline" data-page="${page}">${page}</button>`;
    }).join("");
  };

  render();

  [select, sort].forEach((input) =>
    input.addEventListener("change", () => {
      currentPage = 1;
      render();
    })
  );

  search.addEventListener("input", () => {
    currentPage = 1;
    render();
  });

  pagination.addEventListener("click", (event) => {
    const page = event.target.dataset.page;
    if (!page) return;
    currentPage = Number(page);
    render();
  });
};

const setupProductPage = () => {
  const detail = document.querySelector("#product-detail");
  const related = document.querySelector("#related-products");
  const { id } = getQueryParams();
  const product = getProducts().find((item) => item.id === id) || getProducts()[0];
  if (!product) return;

  detail.innerHTML = `
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
      <img src="${product.image}" alt="${product.name}">
      <div>
        <span class="badge">${product.category}</span>
        <h2>${product.name}</h2>
        <p class="text-muted">${product.description}</p>
        <h3>${formatPrice(product.price)}</h3>
        <button class="button" data-add-cart="${product.id}">Adicionar ao carrinho</button>
      </div>
    </div>
  `;

  const relatedItems = getProducts()
    .filter((item) => item.category === product.category && item.id !== product.id)
    .slice(0, 3);
  related.innerHTML = relatedItems.map(renderProductCard).join("");
};

const setupCartPage = () => {
  const list = document.querySelector("#cart-items");
  const summary = document.querySelector("#cart-summary");
  const couponForm = document.querySelector("#coupon-form");

  const render = () => {
    const cart = getCartDetails();
    list.innerHTML = cart.items
      .map(
        (item) => `
      <div class="cart-item card">
        <img src="${item.product?.image}" alt="${item.product?.name}">
        <div>
          <h4>${item.product?.name}</h4>
          <p class="text-muted">${formatPrice(item.product?.price || 0)}</p>
          <input class="input" type="number" min="1" value="${item.quantity}" data-qty="${
          item.productId
        }">
        </div>
        <button class="button secondary" data-remove="${item.productId}">Remover</button>
      </div>`
      )
      .join("") || `<p class="text-muted">Seu carrinho está vazio.</p>`;

    summary.innerHTML = `
      <div class="card cart-summary">
        <p>Subtotal: <strong>${cart.formattedSubtotal}</strong></p>
        <p>Desconto: <strong>${cart.formattedDiscount}</strong></p>
        <h3>Total: ${cart.formattedTotal}</h3>
        <a class="button" href="checkout.html">Finalizar compra</a>
      </div>
    `;
  };

  render();

  list.addEventListener("change", (event) => {
    const productId = event.target.dataset.qty;
    if (!productId) return;
    updateQuantity(productId, Number(event.target.value));
    render();
    updateCartCount();
  });

  list.addEventListener("click", (event) => {
    const productId = event.target.dataset.remove;
    if (!productId) return;
    removeFromCart(productId);
    render();
    updateCartCount();
  });

  couponForm.addEventListener("submit", (event) => {
    event.preventDefault();
    const code = couponForm.querySelector("input").value.toUpperCase();
    const coupon = applyCoupon(code);
    alert(coupon ? "Cupom aplicado!" : "Cupom inválido.");
    render();
  });
};

const setupCheckoutPage = () => {
  guardUserPage();
  const form = document.querySelector("#checkout-form");
  const summary = document.querySelector("#checkout-summary");
  const user = getUsers().find((item) => item.id === getUserSession().id);
  const cart = getCartDetails();

  form.querySelector("[name='name']").value = user?.name || "";
  form.querySelector("[name='email']").value = user?.email || "";

  summary.innerHTML = `
    <div class="card">
      <h3>Resumo do pedido</h3>
      ${cart.items.map((item) => `<p>${item.product?.name} • ${item.quantity}x</p>`).join("")}
      <p>Subtotal: ${cart.formattedSubtotal}</p>
      <p>Desconto: ${cart.formattedDiscount}</p>
      <h3>Total: ${cart.formattedTotal}</h3>
    </div>
  `;

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const payment = form.querySelector("[name='payment']:checked").value;
    import("./checkout.js").then(({ finalizeOrder }) => {
      const order = finalizeOrder(payment);
      if (order) {
        document.querySelector("#checkout-success").classList.remove("hidden");
        form.reset();
      }
    });
  });
};

const setupLoginPage = () => {
  const form = document.querySelector("#login-form");
  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(form).entries());
    const user = loginUser(data.email, data.password);
    if (!user) {
      alert("Credenciais inválidas.");
      return;
    }
    window.location.href = "conta.html";
  });
};

const setupRegisterPage = () => {
  const form = document.querySelector("#register-form");
  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(form).entries());
    if (data.password.length < 8) {
      alert("Senha deve ter pelo menos 8 caracteres.");
      return;
    }
    if (data.password !== data.confirmPassword) {
      alert("Confirmação de senha incorreta.");
      return;
    }
    const users = getUsers();
    if (users.some((user) => user.email === data.email)) {
      alert("Email já cadastrado.");
      return;
    }
    users.push({
      id: `user-${crypto.randomUUID()}`,
      name: data.name,
      email: data.email,
      password: data.password,
      createdAt: new Date().toISOString()
    });
    localStorage.setItem("users", JSON.stringify(users));
    alert("Cadastro realizado! Faça login.");
    window.location.href = "login.html";
  });
};

const setupAccountPage = () => {
  guardUserPage();
  const user = getUsers().find((item) => item.id === getUserSession().id);
  const orders = getOrders().filter((order) => order.userId === user.id);

  document.querySelector("#account-name").value = user.name;
  document.querySelector("#account-email").value = user.email;

  document.querySelector("#account-form").addEventListener("submit", (event) => {
    event.preventDefault();
    user.name = document.querySelector("#account-name").value;
    user.email = document.querySelector("#account-email").value;
    const users = getUsers().map((item) => (item.id === user.id ? user : item));
    localStorage.setItem("users", JSON.stringify(users));
    alert("Dados atualizados!");
  });

  document.querySelector("#orders-history").innerHTML = orders
    .map(
      (order) => `
      <div class="card">
        <h4>Pedido ${order.id}</h4>
        <p>Status: ${order.status}</p>
        <p>Total: ${formatPrice(order.total)}</p>
        <p>Itens: ${order.items.map((item) => item.name).join(", ")}</p>
      </div>
    `
    )
    .join("") || `<p class="text-muted">Nenhum pedido encontrado.</p>`;
};

const setupPage = () => {
  const page = document.body.dataset.page;
  if (document.body.dataset.layout === "public") {
    renderNavbar();
    renderFooter();
    setupAddToCart();
  }

  switch (page) {
    case "home":
      setupHomePage();
      break;
    case "categoria":
      setupCategoryPage();
      break;
    case "produto":
      setupProductPage();
      break;
    case "carrinho":
      setupCartPage();
      break;
    case "checkout":
      setupCheckoutPage();
      break;
    case "login":
      setupLoginPage();
      break;
    case "cadastro":
      setupRegisterPage();
      break;
    case "conta":
      setupAccountPage();
      break;
    default:
      break;
  }
};

const bootstrap = async () => {
  await initStorage();
  applyBranding();
  setupPage();
};

bootstrap();
