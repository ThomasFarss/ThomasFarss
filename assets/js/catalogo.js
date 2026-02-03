import { getProducts, getCategories } from "./api-mock.js";

export const formatPrice = (value) =>
  value.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });

export const renderProductCard = (product) => `
  <article class="card product-card">
    <img src="${product.image}" alt="${product.name}">
    <span class="badge">${product.category}</span>
    <h3>${product.name}</h3>
    <p class="text-muted">${product.description}</p>
    <strong>${formatPrice(product.price)}</strong>
    <button class="button" data-add-cart="${product.id}">Adicionar ao carrinho</button>
  </article>
`;

export const renderCategoryOptions = (select) => {
  const categories = getCategories();
  select.innerHTML = `<option value="">Todas as categorias</option>${categories
    .map((category) => `<option value="${category.slug}">${category.name}</option>`)
    .join("")}`;
};

export const filterProducts = ({ category, search, sort }) => {
  let products = getProducts().filter((item) => item.status === "active");

  if (category) {
    products = products.filter((item) => item.category === category);
  }

  if (search) {
    const term = search.toLowerCase();
    products = products.filter((item) => item.name.toLowerCase().includes(term));
  }

  if (sort === "price-asc") {
    products = products.sort((a, b) => a.price - b.price);
  }

  if (sort === "price-desc") {
    products = products.sort((a, b) => b.price - a.price);
  }

  if (sort === "sales") {
    products = products.sort((a, b) => b.sales - a.sales);
  }

  if (sort === "recent") {
    products = products.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
  }

  return products;
};

export const paginate = (items, page = 1, perPage = 6) => {
  const totalPages = Math.ceil(items.length / perPage) || 1;
  const start = (page - 1) * perPage;
  return {
    items: items.slice(start, start + perPage),
    totalPages
  };
};
