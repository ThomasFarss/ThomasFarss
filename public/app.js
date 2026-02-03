const adsGrid = document.getElementById("adsGrid");
const externalAdsGrid = document.getElementById("externalAdsGrid");
const affiliateTable = document.querySelector("#affiliateTable tbody");

const adForm = document.getElementById("adForm");
const affiliateForm = document.getElementById("affiliateForm");
const discountForm = document.getElementById("discountForm");

const adNotice = document.getElementById("adNotice");
const affiliateNotice = document.getElementById("affiliateNotice");
const discountNotice = document.getElementById("discountNotice");

async function loadAds() {
  if (!adsGrid && !externalAdsGrid) return;
  const response = await fetch("/api/ads");
  const data = await response.json();

  if (adsGrid) {
    adsGrid.innerHTML = data.ads
      .map(
        (ad) => `
      <div class="card">
        <span class="badge">${ad.type}</span>
        <h3>${ad.title}</h3>
        <p>${ad.price}</p>
        <p><strong>Vendedor:</strong> ${ad.seller}</p>
        <p><strong>Categoria:</strong> ${ad.category}</p>
      </div>
    `
      )
      .join("");
  }

  if (externalAdsGrid) {
    externalAdsGrid.innerHTML = data.externalAds
      .map(
        (ad) => `
      <div class="card">
        <span class="badge">Externo</span>
        <h3>${ad.title}</h3>
        <p><strong>Marca:</strong> ${ad.brand}</p>
        <p><strong>Meta:</strong> ${ad.goal}</p>
        <p><strong>Orçamento:</strong> ${ad.budget}</p>
        <a class="button" href="${ad.url}" target="_blank" rel="noreferrer">Visitar campanha</a>
      </div>
    `
      )
      .join("");
  }
}

async function loadAffiliates() {
  if (!affiliateTable) return;
  const response = await fetch("/api/affiliates");
  const data = await response.json();

  affiliateTable.innerHTML = data.affiliates
    .map(
      (item) => `
    <tr>
      <td>${item.platform}</td>
      <td>${item.title}</td>
      <td><a class="button secondary" href="${item.link}" target="_blank" rel="noreferrer">Abrir</a></td>
    </tr>
  `
    )
    .join("");
}

if (adForm) {
  adForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    adNotice.style.display = "none";

    const formData = new FormData(adForm);
    const payload = Object.fromEntries(formData.entries());

    const response = await fetch("/api/ads", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });

    const data = await response.json();
    if (!response.ok) {
      adNotice.textContent = data.error;
      adNotice.style.display = "block";
      return;
    }

    adNotice.textContent = "Anúncio criado com sucesso!";
    adNotice.style.display = "block";
    adForm.reset();
    loadAds();
  });
}

if (affiliateForm) {
  affiliateForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    affiliateNotice.style.display = "none";

    const formData = new FormData(affiliateForm);
    const payload = Object.fromEntries(formData.entries());

    const response = await fetch("/api/affiliates", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });

    const data = await response.json();
    if (!response.ok) {
      affiliateNotice.textContent = data.error;
      affiliateNotice.style.display = "block";
      return;
    }

    affiliateNotice.textContent = "Link de afiliado publicado!";
    affiliateNotice.style.display = "block";
    affiliateForm.reset();
    loadAffiliates();
  });
}

if (discountForm) {
  discountForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    discountNotice.style.display = "none";

    const formData = new FormData(discountForm);
    const tier = formData.get("tier");
    const price = Number(formData.get("price"));

    const response = await fetch(`/api/discount?tier=${tier}`);
    const data = await response.json();

    if (!response.ok) {
      discountNotice.textContent = data.error;
      discountNotice.style.display = "block";
      return;
    }

    const discountValue = (price * data.discount) / 100;
    const finalPrice = price - discountValue;

    discountNotice.innerHTML = `Você recebeu <strong>${data.discount}%</strong> de desconto. Valor final: <strong>R$ ${finalPrice.toFixed(2)}</strong>.`;
    discountNotice.style.display = "block";
  });
}

loadAds();
loadAffiliates();
