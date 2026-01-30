const cartBadge = document.querySelector(".badge");
const exploreButton = document.querySelector(".btn--primary");
const offerButton = document.querySelector(".btn--ghost");
const navLinks = document.querySelectorAll(".nav__links a");
const categoryCards = document.querySelectorAll(".category-card");

let cartCount = Number.parseInt(cartBadge?.textContent ?? "0", 10);

const setActiveLink = (link) => {
  navLinks.forEach((item) => item.classList.remove("active"));
  link.classList.add("active");
};

navLinks.forEach((link) => {
  link.addEventListener("click", (event) => {
    event.preventDefault();
    setActiveLink(link);
  });
});

const incrementCart = () => {
  if (!cartBadge) {
    return;
  }
  cartCount += 1;
  cartBadge.textContent = String(cartCount);
};

exploreButton?.addEventListener("click", () => {
  incrementCart();
  document.querySelector(".product-grid")?.scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
});

offerButton?.addEventListener("click", () => {
  document.querySelector(".benefits-grid")?.scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
});

categoryCards.forEach((card) => {
  card.addEventListener("click", () => {
    categoryCards.forEach((item) => item.classList.remove("category-card--active"));
    card.classList.add("category-card--active");
    document.querySelector(".section__header")?.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  });
});
