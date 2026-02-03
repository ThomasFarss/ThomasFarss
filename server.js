const express = require("express");
const path = require("path");

const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.json());
app.use(express.static(path.join(__dirname, "public")));

const ads = [
  {
    id: 1,
    title: "Lâmpada Inteligente Smart Home",
    price: "R$ 129,90",
    seller: "TechVision",
    category: "Casa Inteligente",
    type: "Interno"
  },
  {
    id: 2,
    title: "Tênis Esportivo Dream Runner",
    price: "R$ 219,90",
    seller: "Fox Sports",
    category: "Moda",
    type: "Interno"
  }
];

const externalAds = [
  {
    id: 101,
    title: "Super Promoção AliExpress",
    brand: "AliExpress",
    url: "https://www.aliexpress.com/",
    goal: "Conversões",
    budget: "R$ 2.500"
  },
  {
    id: 102,
    title: "Amazon Prime Deals",
    brand: "Amazon",
    url: "https://www.amazon.com.br/",
    goal: "Vendas",
    budget: "R$ 3.800"
  }
];

const affiliates = [
  {
    id: 201,
    platform: "AliExpress",
    link: "https://www.aliexpress.com/",
    title: "Gadgets com frete grátis"
  },
  {
    id: 202,
    platform: "Amazon",
    link: "https://www.amazon.com.br/",
    title: "Ofertas do dia em eletrônicos"
  },
  {
    id: 203,
    platform: "Mercado Livre",
    link: "https://www.mercadolivre.com.br/",
    title: "Top sellers e envios rápidos"
  },
  {
    id: 204,
    platform: "Shopee",
    link: "https://shopee.com.br/",
    title: "Moda e acessórios com cupom"
  }
];

const discounts = {
  prata: 5,
  gold: 8
};

const users = [
  {
    id: 1,
    name: "Thiago Ramos",
    email: "prata@dreamslive.com",
    password: "prata123",
    tier: "prata"
  },
  {
    id: 2,
    name: "Camila Andrade",
    email: "gold@dreamslive.com",
    password: "gold123",
    tier: "gold"
  },
  {
    id: 3,
    name: "Visitante",
    email: "free@dreamslive.com",
    password: "free123",
    tier: "free"
  }
];

app.get("/api/ads", (req, res) => {
  res.json({ ads, externalAds });
});

app.post("/api/ads", (req, res) => {
  const { title, price, seller, category, type } = req.body;
  if (!title || !price || !seller) {
    return res.status(400).json({ error: "Preencha título, preço e vendedor." });
  }

  const newAd = {
    id: ads.length + externalAds.length + 1,
    title,
    price,
    seller,
    category: category || "Geral",
    type: type || "Interno"
  };

  ads.push(newAd);
  res.status(201).json(newAd);
});

app.get("/api/affiliates", (req, res) => {
  res.json({ affiliates });
});

app.post("/api/affiliates", (req, res) => {
  const { platform, link, title } = req.body;
  if (!platform || !link || !title) {
    return res.status(400).json({ error: "Preencha plataforma, link e título." });
  }
  const newAffiliate = {
    id: affiliates.length + 1,
    platform,
    link,
    title
  };
  affiliates.push(newAffiliate);
  res.status(201).json(newAffiliate);
});

app.get("/api/discount", (req, res) => {
  const tier = String(req.query.tier || "").toLowerCase();
  const discount = discounts[tier];
  if (!discount) {
    return res.status(404).json({ error: "Nível não encontrado." });
  }
  res.json({ tier, discount });
});

app.post("/api/login", (req, res) => {
  const { email, password } = req.body;
  const user = users.find(
    (item) => item.email === email && item.password === password
  );

  if (!user) {
    return res.status(401).json({ error: "Credenciais inválidas." });
  }

  res.json({
    id: user.id,
    name: user.name,
    email: user.email,
    tier: user.tier
  });
});

app.listen(PORT, () => {
  console.log(`DreamsLive Shop rodando em http://localhost:${PORT}`);
});
