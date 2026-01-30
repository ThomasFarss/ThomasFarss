import {
  getProducts,
  getCategories,
  getOrders,
  getUsers,
  getCoupons,
  getSettings,
  saveProduct,
  deleteProduct,
  saveCategory,
  deleteCategory,
  saveCoupon,
  deleteCoupon,
  updateOrder,
  saveSettings
} from "./api-mock.js";
import { formatPrice } from "./catalogo.js";

export const buildAdminSidebar = (active) => {
  const links = [
    { href: "dashboard.html", label: "Dashboard", key: "dashboard" },
    { href: "produtos.html", label: "Produtos", key: "produtos" },
    { href: "categorias.html", label: "Categorias", key: "categorias" },
    { href: "pedidos.html", label: "Pedidos", key: "pedidos" },
    { href: "clientes.html", label: "Clientes", key: "clientes" },
    { href: "cupons.html", label: "Cupons", key: "cupons" },
    { href: "config.html", label: "Configurações", key: "config" }
  ];

  return links
    .map(
      (link) => `<a href="${link.href}" class="${active === link.key ? "active" : ""}">${
        link.label
      }</a>`
    )
    .join("");
};

export const setupDashboardPage = () => {
  const orders = getOrders();
  const users = getUsers();
  const products = getProducts();
  const today = new Date().toISOString().slice(0, 10);
  const month = new Date().getMonth();

  const ordersToday = orders.filter((order) => order.createdAt?.slice(0, 10) === today);
  const ordersMonth = orders.filter((order) => new Date(order.createdAt).getMonth() === month);

  document.querySelector("#stat-orders-today").textContent = ordersToday.length;
  document.querySelector("#stat-orders-month").textContent = formatPrice(
    ordersMonth.reduce((sum, order) => sum + order.total, 0)
  );
  document.querySelector("#stat-users").textContent = users.length;
  document.querySelector("#stat-products").textContent = products.length;

  const chartContainer = document.querySelector("#orders-chart");
  const statusCounts = ["novo", "pago", "entregue", "cancelado"].map((status) => ({
    status,
    count: orders.filter((order) => order.status === status).length
  }));

  chartContainer.innerHTML = statusCounts
    .map(
      (item) => `<span style="width:${Math.max(item.count, 1) * 20}px">${item.status} (${item.count})</span>`
    )
    .join("");
};

export const setupProductsPage = () => {
  const tableBody = document.querySelector("#products-table");
  const form = document.querySelector("#product-form");
  const searchInput = document.querySelector("#product-search");

  const render = (filter = "") => {
    const products = getProducts().filter((item) =>
      item.name.toLowerCase().includes(filter.toLowerCase())
    );
    tableBody.innerHTML = products
      .map(
        (product) => `<tr>
          <td>${product.name}</td>
          <td>${product.category}</td>
          <td>${formatPrice(product.price)}</td>
          <td>${product.status}</td>
          <td class="admin-table-actions">
            <button class="button outline" data-edit="${product.id}">Editar</button>
            <button class="button secondary" data-delete="${product.id}">Excluir</button>
          </td>
        </tr>`
      )
      .join("");
  };

  render();

  searchInput.addEventListener("input", (event) => render(event.target.value));

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(form).entries());
    saveProduct({
      id: data.id || undefined,
      name: data.name,
      category: data.category,
      price: Number(data.price),
      status: data.status,
      stock: Number(data.stock || 0),
      image: data.image,
      description: data.description
    });
    form.reset();
    render();
  });

  tableBody.addEventListener("click", (event) => {
    const editId = event.target.dataset.edit;
    const deleteId = event.target.dataset.delete;
    if (deleteId) {
      deleteProduct(deleteId);
      render();
    }
    if (editId) {
      const product = getProducts().find((item) => item.id === editId);
      if (product) {
        form.querySelector("[name='id']").value = product.id;
        form.querySelector("[name='name']").value = product.name;
        form.querySelector("[name='category']").value = product.category;
        form.querySelector("[name='price']").value = product.price;
        form.querySelector("[name='status']").value = product.status;
        form.querySelector("[name='stock']").value = product.stock;
        form.querySelector("[name='image']").value = product.image;
        form.querySelector("[name='description']").value = product.description;
      }
    }
  });
};

export const setupCategoriesPage = () => {
  const tableBody = document.querySelector("#categories-table");
  const form = document.querySelector("#category-form");

  const render = () => {
    const categories = getCategories();
    tableBody.innerHTML = categories
      .map(
        (category) => `<tr>
          <td>${category.name}</td>
          <td>${category.slug}</td>
          <td>${category.order}</td>
          <td>${category.status}</td>
          <td class="admin-table-actions">
            <button class="button outline" data-edit="${category.id}">Editar</button>
            <button class="button secondary" data-delete="${category.id}">Excluir</button>
          </td>
        </tr>`
      )
      .join("");
  };

  render();

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(form).entries());
    saveCategory({
      id: data.id || undefined,
      name: data.name,
      slug: data.slug,
      order: Number(data.order),
      status: data.status
    });
    form.reset();
    render();
  });

  tableBody.addEventListener("click", (event) => {
    const editId = event.target.dataset.edit;
    const deleteId = event.target.dataset.delete;
    if (deleteId) {
      deleteCategory(deleteId);
      render();
    }
    if (editId) {
      const category = getCategories().find((item) => item.id === editId);
      if (category) {
        form.querySelector("[name='id']").value = category.id;
        form.querySelector("[name='name']").value = category.name;
        form.querySelector("[name='slug']").value = category.slug;
        form.querySelector("[name='order']").value = category.order;
        form.querySelector("[name='status']").value = category.status;
      }
    }
  });
};

export const setupOrdersPage = () => {
  const tableBody = document.querySelector("#orders-table");
  const statusFilter = document.querySelector("#orders-filter");
  const detailContainer = document.querySelector("#order-detail");

  const render = (status = "") => {
    const orders = getOrders().filter((order) => (status ? order.status === status : true));
    tableBody.innerHTML = orders
      .map(
        (order) => `<tr>
          <td>${order.id}</td>
          <td>${order.status}</td>
          <td>${formatPrice(order.total)}</td>
          <td>${new Date(order.createdAt).toLocaleDateString("pt-BR")}</td>
          <td><button class="button outline" data-detail="${order.id}">Detalhes</button></td>
        </tr>`
      )
      .join("");
  };

  render();

  statusFilter.addEventListener("change", (event) => render(event.target.value));

  tableBody.addEventListener("click", (event) => {
    const orderId = event.target.dataset.detail;
    if (!orderId) return;
    const order = getOrders().find((item) => item.id === orderId);
    if (!order) return;
    detailContainer.innerHTML = `
      <h3>Pedido ${order.id}</h3>
      <p>Status: <strong>${order.status}</strong></p>
      <p>Total: <strong>${formatPrice(order.total)}</strong></p>
      <div class="form-row">
        <select id="order-status" class="input">
          ${["novo", "pago", "entregue", "cancelado"]
            .map(
              (status) =>
                `<option value="${status}" ${status === order.status ? "selected" : ""}>${
                  status
                }</option>`
            )
            .join("")}
        </select>
        <button class="button" id="update-order">Atualizar status</button>
      </div>
      <ul>
        ${order.items
          .map((item) => `<li>${item.name} • ${item.quantity}x</li>`)
          .join("")}
      </ul>
    `;

    detailContainer.querySelector("#update-order").addEventListener("click", () => {
      const newStatus = detailContainer.querySelector("#order-status").value;
      updateOrder(order.id, { status: newStatus });
      render(statusFilter.value);
    });
  });

  document.querySelector("#export-orders").addEventListener("click", () => {
    const orders = getOrders();
    const csv = [
      ["id", "status", "total", "date"],
      ...orders.map((order) => [order.id, order.status, order.total, order.createdAt])
    ]
      .map((row) => row.join(","))
      .join("\n");
    const blob = new Blob([csv], { type: "text/csv" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = "orders.csv";
    link.click();
    URL.revokeObjectURL(url);
  });
};

export const setupClientsPage = () => {
  const tableBody = document.querySelector("#clients-table");
  const orders = getOrders();
  const users = getUsers();

  tableBody.innerHTML = users
    .map((user) => {
      const userOrders = orders.filter((order) => order.userId === user.id);
      return `<tr>
        <td>${user.name}</td>
        <td>${user.email}</td>
        <td>${userOrders.length}</td>
        <td>${userOrders.map((order) => order.id).join(", ") || "-"}</td>
      </tr>`;
    })
    .join("");
};

export const setupCouponsPage = () => {
  const tableBody = document.querySelector("#coupons-table");
  const form = document.querySelector("#coupon-form");

  const render = () => {
    const coupons = getCoupons();
    tableBody.innerHTML = coupons
      .map(
        (coupon) => `<tr>
          <td>${coupon.code}</td>
          <td>${coupon.type}</td>
          <td>${coupon.value}</td>
          <td>${coupon.validUntil}</td>
          <td>${coupon.active ? "ativo" : "inativo"}</td>
          <td class="admin-table-actions">
            <button class="button outline" data-edit="${coupon.id}">Editar</button>
            <button class="button secondary" data-delete="${coupon.id}">Excluir</button>
          </td>
        </tr>`
      )
      .join("");
  };

  render();

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(form).entries());
    saveCoupon({
      id: data.id || undefined,
      code: data.code,
      type: data.type,
      value: Number(data.value),
      validUntil: data.validUntil,
      active: data.active === "on"
    });
    form.reset();
    render();
  });

  tableBody.addEventListener("click", (event) => {
    const editId = event.target.dataset.edit;
    const deleteId = event.target.dataset.delete;
    if (deleteId) {
      deleteCoupon(deleteId);
      render();
    }
    if (editId) {
      const coupon = getCoupons().find((item) => item.id === editId);
      if (coupon) {
        form.querySelector("[name='id']").value = coupon.id;
        form.querySelector("[name='code']").value = coupon.code;
        form.querySelector("[name='type']").value = coupon.type;
        form.querySelector("[name='value']").value = coupon.value;
        form.querySelector("[name='validUntil']").value = coupon.validUntil;
        form.querySelector("[name='active']").checked = coupon.active;
      }
    }
  });
};

export const setupConfigPage = () => {
  const form = document.querySelector("#config-form");
  const settings = getSettings();

  form.querySelector("[name='storeName']").value = settings.storeName || "";
  form.querySelector("[name='primaryColor']").value = settings.primaryColor || "#4c6ef5";
  form.querySelector("[name='secondaryColor']").value = settings.secondaryColor || "#f59f00";
  form.querySelector("[name='accentColor']").value = settings.accentColor || "#12b886";
  form.querySelector("[name='contactEmail']").value = settings.contactEmail || "";
  form.querySelector("[name='contactPhone']").value = settings.contactPhone || "";

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(form).entries());
    saveSettings({
      storeName: data.storeName,
      primaryColor: data.primaryColor,
      secondaryColor: data.secondaryColor,
      accentColor: data.accentColor,
      contactEmail: data.contactEmail,
      contactPhone: data.contactPhone,
      banners: settings.banners || []
    });
    alert("Configurações atualizadas!");
  });
};
