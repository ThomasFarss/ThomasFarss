<?php
require_once __DIR__ . '/../includes/config.php';
require_login();

$user = current_user($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $tag = trim($_POST['tag'] ?? '');
    $discount = (int) ($_POST['discount'] ?? 0);

    if ($name && $price && $category && $description && $image) {
        $stmt = $db->prepare(
            'INSERT INTO products (name, price, category, description, image, tag, discount)
             VALUES (:name, :price, :category, :description, :image, :tag, :discount)'
        );
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':category' => $category,
            ':description' => $description,
            ':image' => $image,
            ':tag' => $tag,
            ':discount' => $discount,
        ]);
        header('Location: /admin/index.php');
        exit();
    }
}

if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $deleteStmt = $db->prepare('DELETE FROM products WHERE id = :id');
    $deleteStmt->execute([':id' => $deleteId]);
    header('Location: /admin/index.php');
    exit();
}

$productStmt = $db->query('SELECT * FROM products ORDER BY created_at DESC');
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Painel Admin - Help Loj</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="/styles.css" />
  </head>
  <body class="theme-white">
    <header class="site-header">
      <div class="container nav">
        <div class="logo">
          <img src="/logo.svg" alt="Help Loj" class="logo__image" />
        </div>
        <nav class="nav__links">
          <a href="/index.php">Loja</a>
          <a class="active" href="/admin/index.php">Painel</a>
        </nav>
        <div class="nav__icons">
          <span class="admin__welcome">Olá, <?= htmlspecialchars($user['username'] ?? 'Admin') ?></span>
          <a class="btn btn--ghost" href="/admin/logout.php">Sair</a>
        </div>
      </div>
    </header>

    <main class="admin-page">
      <section class="section admin">
        <div class="container">
          <h2 class="section__title">Painel do Administrador</h2>
          <div class="admin__grid">
            <form class="admin__form" method="post">
              <div class="form__group">
                <label for="product-name">Nome do produto</label>
                <input id="product-name" name="name" type="text" placeholder="Gift Card Steam R$ 100" required />
              </div>
              <div class="form__group">
                <label for="product-price">Preço (R$)</label>
                <input id="product-price" name="price" type="number" step="0.01" placeholder="199.90" required />
              </div>
              <div class="form__group">
                <label for="product-category">Categoria</label>
                <select id="product-category" name="category" required>
                  <option value="Gift Cards">Gift Cards</option>
                  <option value="Emuladores">Emuladores</option>
                  <option value="Jogos Digitais">Jogos Digitais</option>
                  <option value="Outros">Outros</option>
                </select>
              </div>
              <div class="form__group">
                <label for="product-image">URL da imagem</label>
                <input id="product-image" name="image" type="url" placeholder="https://..." required />
              </div>
              <div class="form__group">
                <label for="product-description">Descrição curta</label>
                <input id="product-description" name="description" type="text" placeholder="Entrega rápida e segura" required />
              </div>
              <div class="form__row">
                <div class="form__group">
                  <label for="product-tag">Tag</label>
                  <input id="product-tag" name="tag" type="text" placeholder="Destaque" />
                </div>
                <div class="form__group">
                  <label for="product-discount">Desconto (%)</label>
                  <input id="product-discount" name="discount" type="number" placeholder="10" min="0" max="90" />
                </div>
              </div>
              <button class="btn btn--primary" type="submit">Adicionar produto</button>
            </form>
            <div class="admin__list">
              <h3>Produtos cadastrados</h3>
              <div class="admin__items">
                <?php foreach ($products as $product): ?>
                  <div class="admin__item">
                    <div>
                      <span><?= htmlspecialchars($product['name']) ?></span>
                      <div><?= htmlspecialchars($product['category']) ?> • R$ <?= number_format((float) $product['price'], 2, ',', '.') ?></div>
                    </div>
                    <a class="admin__delete" href="/admin/index.php?delete=<?= (int) $product['id'] ?>">Excluir</a>
                  </div>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                  <p>Nenhum produto cadastrado ainda.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>
