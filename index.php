<?php
require_once __DIR__ . '/includes/config.php';

$categories = ['Gift Cards', 'Emuladores', 'Jogos Digitais', 'Outros'];
$productStmt = $db->query('SELECT * FROM products ORDER BY created_at DESC');
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Help Loj - Loja Gamer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body class="theme-white">
    <header class="site-header">
      <div class="container nav">
        <div class="logo">
          <img src="logo.svg" alt="Help Loj" class="logo__image" />
        </div>
        <nav class="nav__links">
          <a class="active" href="#">Home</a>
          <a href="#categorias">Loja</a>
          <a href="#sobre">Sobre</a>
          <a href="/admin/login.php">Admin</a>
          <a href="#suporte">Suporte</a>
        </nav>
        <div class="nav__icons">
          <button class="icon-button" id="cart-toggle" aria-label="Carrinho">
            <span class="badge">0</span>
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path
                d="M6 6h15l-1.7 8.2a2 2 0 0 1-2 1.6H9.1a2 2 0 0 1-2-1.6L5 4H2"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
              <circle cx="9" cy="19" r="1.5" />
              <circle cx="17" cy="19" r="1.5" />
            </svg>
          </button>
          <button class="icon-button" aria-label="Conta">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <circle cx="12" cy="8" r="3.5" fill="none" stroke="currentColor" stroke-width="1.5" />
              <path
                d="M5 19c1.6-2.5 4.1-4 7-4s5.4 1.5 7 4"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
              />
            </svg>
          </button>
        </div>
      </div>
    </header>

    <main>
      <section class="hero">
        <div class="container hero__content">
          <div class="hero__text">
            <div class="hero__badge">Entrega instantânea • Suporte rápido</div>
            <h1>
              Produtos Digitais
              <span>Para Gamers</span>
            </h1>
            <p>
              Gift cards, jogos digitais e emuladores com entrega instantânea.
              Sua diversão começa aqui.
            </p>
            <div class="hero__actions">
              <button class="btn btn--primary" id="explore-button">
                <span class="btn__icon">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path
                      d="M6 7h12M6 12h12M6 17h7"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                    />
                  </svg>
                </span>
                Explorar Loja
              </button>
              <button class="btn btn--ghost" id="offers-button">Ver Ofertas</button>
            </div>
          </div>
          <div class="hero__image">
            <img
              src="https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=800&q=80"
              alt="Setup gamer retro"
            />
          </div>
        </div>
      </section>

      <section class="section" id="categorias">
        <div class="container">
          <h2 class="section__title">Explore por Categoria</h2>
          <div class="category-grid">
            <?php foreach ($categories as $category): ?>
              <article class="category-card" data-category="<?= htmlspecialchars($category) ?>">
                <div class="category-card__icon">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path
                      d="M4 7h16v10H4z"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                    />
                    <path
                      d="M6 10h12"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                    />
                  </svg>
                </div>
                <h3><?= htmlspecialchars($category) ?></h3>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <section class="section" id="produtos">
        <div class="container">
          <div class="section__header">
            <h2>Produtos em Destaque</h2>
            <a class="section__link" href="#">Ver Tudo →</a>
          </div>
          <div class="product-grid" id="product-grid">
            <?php foreach ($products as $product): ?>
              <article
                class="product-card"
                data-category="<?= htmlspecialchars($product['category']) ?>"
                data-id="<?= htmlspecialchars($product['id']) ?>"
                data-name="<?= htmlspecialchars($product['name']) ?>"
                data-description="<?= htmlspecialchars($product['description']) ?>"
                data-price="<?= htmlspecialchars($product['price']) ?>"
              >
                <?php if (!empty($product['tag'])): ?>
                  <span class="tag"><?= htmlspecialchars($product['tag']) ?></span>
                <?php endif; ?>
                <?php if (!empty($product['discount'])): ?>
                  <span class="discount">-<?= htmlspecialchars($product['discount']) ?>%</span>
                <?php endif; ?>
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
                <div class="product-card__body">
                  <h3><?= htmlspecialchars($product['name']) ?></h3>
                  <p><?= htmlspecialchars($product['description']) ?></p>
                  <span class="product-card__price">R$ <?= number_format((float) $product['price'], 2, ',', '.') ?></span>
                  <div class="product-card__actions">
                    <button class="add" data-add="<?= htmlspecialchars($product['id']) ?>">Adicionar</button>
                    <button class="details" data-details="<?= htmlspecialchars($product['id']) ?>">Detalhes</button>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <section class="section" id="sobre">
        <div class="container">
          <h2 class="section__title">Por que Escolher a Help Loj?</h2>
          <div class="benefits-grid">
            <article class="benefit-card">
              <div class="benefit-card__icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path
                    d="M13 2L6 14h6l-1 8 7-12h-6z"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                  />
                </svg>
              </div>
              <h3>Entrega Instantânea</h3>
              <p>Receba seus códigos digitais imediatamente após o pagamento</p>
            </article>
            <article class="benefit-card">
              <div class="benefit-card__icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path
                    d="M12 3l7 3v6c0 4.4-2.9 8.2-7 9-4.1-.8-7-4.6-7-9V6l7-3z"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                  />
                </svg>
              </div>
              <h3>100% Seguro</h3>
              <p>Transações protegidas e dados criptografados</p>
            </article>
            <article class="benefit-card benefit-card--highlight">
              <div class="benefit-card__icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path
                    d="M4 13a8 8 0 0 1 16 0v5a2 2 0 0 1-2 2h-3"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                  />
                  <path d="M6 13v5" fill="none" stroke="currentColor" stroke-width="1.5" />
                  <circle cx="15" cy="18" r="1" />
                </svg>
              </div>
              <h3>Suporte Rápido</h3>
              <p>Atendimento ágil para resolver suas dúvidas</p>
            </article>
            <article class="benefit-card">
              <div class="benefit-card__icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <rect x="6" y="10" width="12" height="9" rx="2" fill="none" stroke="currentColor" stroke-width="1.5" />
                  <path d="M9 10V7a3 3 0 0 1 6 0v3" fill="none" stroke="currentColor" stroke-width="1.5" />
                </svg>
              </div>
              <h3>Garantia Total</h3>
              <p>Produtos originais com garantia de funcionamento</p>
            </article>
          </div>
        </div>
      </section>

      <section class="section support" id="suporte">
        <div class="container support__content">
          <div>
            <h2>Precisa de ajuda?</h2>
            <p>Atendimento dedicado, pedidos rastreáveis e suporte humano para cada compra.</p>
          </div>
          <button class="btn btn--ghost">Falar com suporte</button>
        </div>
      </section>
    </main>

    <aside class="cart" id="cart-panel" aria-hidden="true">
      <div class="cart__header">
        <h3>Seu carrinho</h3>
        <button class="icon-button" id="cart-close" aria-label="Fechar">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
          </svg>
        </button>
      </div>
      <div class="cart__items" id="cart-items"></div>
      <div class="cart__footer">
        <div class="cart__total">
          <span>Total</span>
          <strong id="cart-total">R$ 0,00</strong>
        </div>
        <button class="btn btn--primary">Finalizar compra</button>
      </div>
    </aside>
    <div class="cart__overlay" id="cart-overlay"></div>

    <footer class="site-footer">
      <div class="container footer__grid">
        <div class="footer__brand">
          <img src="logo.svg" alt="Help Loj" class="logo__image logo__image--small" />
          <p>Sua loja de produtos digitais gamers. Rápido, seguro e confiável.</p>
        </div>
        <div>
          <h4>Categorias</h4>
          <ul>
            <li>Gift Cards</li>
            <li>Emuladores</li>
            <li>Jogos Digitais</li>
          </ul>
        </div>
        <div>
          <h4>Links Úteis</h4>
          <ul>
            <li>Sobre Nós</li>
            <li>Suporte</li>
            <li>Meus Pedidos</li>
          </ul>
        </div>
        <div>
          <h4>Atendimento</h4>
          <ul>
            <li>Segunda a Sexta: 9h - 18h</li>
            <li>Sábado: 9h - 14h</li>
          </ul>
        </div>
      </div>
      <p class="footer__copy">© 2024 Help Loj. Todos os direitos reservados.</p>
    </footer>
    <script src="script.js"></script>
  </body>
</html>
