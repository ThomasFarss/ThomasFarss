export const STORAGE_KEYS = [
  "users",
  "adminUsers",
  "products",
  "categories",
  "orders",
  "coupons",
  "settings"
];

const fallbackSeed = {
  settings: {
    storeName: "Helpe Lojas",
    primaryColor: "#4c6ef5",
    secondaryColor: "#f59f00",
    accentColor: "#12b886",
    contactEmail: "contato@helplojas.com",
    contactPhone: "+55 (11) 99999-0000",
    banners: [
      {
        title: "Gift Cards instantâneos",
        subtitle: "Receba em minutos e aproveite seus jogos e serviços favoritos.",
        cta: "Ver ofertas",
        image:
          "https://images.unsplash.com/photo-1523475472560-d2df97ec485c?auto=format&fit=crop&w=1200&q=60"
      }
    ]
  },
  categories: [
    { id: "cat-1", name: "Gift Cards", slug: "gift-cards", order: 1, status: "active" },
    { id: "cat-2", name: "Emuladores", slug: "emuladores", order: 2, status: "active" },
    { id: "cat-3", name: "Assinaturas", slug: "assinaturas", order: 3, status: "active" },
    { id: "cat-4", name: "Outros", slug: "outros", order: 4, status: "active" }
  ],
  products: [
    {
      id: "prod-1",
      name: "Gift Card PlayStation R$100",
      category: "gift-cards",
      price: 100,
      status: "active",
      stock: 200,
      image:
        "https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=800&q=60",
      description: "Crédito instantâneo para a PlayStation Store. Entrega digital imediata.",
      sales: 180,
      createdAt: "2024-01-12T10:00:00Z"
    }
  ],
  users: [
    {
      id: "user-1",
      name: "Cliente Demo",
      email: "cliente@helplojas.com",
      password: "12345678",
      createdAt: "2024-02-01T10:00:00Z"
    }
  ],
  adminUsers: [
    {
      id: "admin-1",
      name: "Admin Helpe",
      email: "admin@helplojas.com",
      password: "12345678"
    }
  ],
  orders: [],
  coupons: [
    { id: "coupon-1", code: "HELPE10", type: "percent", value: 10, validUntil: "2025-12-31", active: true }
  ]
};

export const readData = (key, fallback = []) => {
  const raw = localStorage.getItem(key);
  if (!raw) return fallback;
  try {
    return JSON.parse(raw);
  } catch (error) {
    console.error("Erro ao ler storage", error);
    return fallback;
  }
};

export const writeData = (key, value) => {
  localStorage.setItem(key, JSON.stringify(value));
};

export const initStorage = async () => {
  const isEmpty = STORAGE_KEYS.every((key) => !localStorage.getItem(key));
  if (!isEmpty) return;

  let seed = fallbackSeed;
  try {
    const response = await fetch("../data/seed.json");
    if (response.ok) {
      seed = await response.json();
    }
  } catch (error) {
    console.warn("Falha ao carregar seed.json, usando fallback.", error);
  }

  STORAGE_KEYS.forEach((key) => {
    writeData(key, seed[key] || (key === "settings" ? {} : []));
  });
};

export const generateId = (prefix = "id") => `${prefix}-${crypto.randomUUID()}`;
