<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/FolderManager.php';

$manager = new FolderManager();
$search = trim($_GET['q'] ?? '');
$category = (int) ($_GET['categoria'] ?? 0);
$folders = $manager->publicFolders($search, $category);
$categories = $manager->categories();
$title = 'Biblioteca de Downloads';
include __DIR__ . '/../includes/header.php';
?>

<section class="hero mb-4">
  <h1 class="h3">Plataforma de downloads de jogos</h1>
  <p class="mb-0">Publique suas pastas, gerencie arquivos e compartilhe com segurança.</p>
</section>

<form class="row g-2 mb-4" method="GET">
  <div class="col-md-6"><input class="form-control" name="q" placeholder="Buscar jogos/pastas" value="<?= e($search) ?>"></div>
  <div class="col-md-4">
    <select class="form-select" name="categoria">
      <option value="0">Todas categorias</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= (int) $cat['id'] ?>" <?= $category === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['nome']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2 d-grid"><button class="btn btn-primary">Filtrar</button></div>
</form>

<div class="row g-4">
  <?php foreach ($folders as $folder): ?>
    <div class="col-md-4">
      <div class="card h-100 shadow-sm">
        <img class="card-img-top card-cover" src="<?= $folder['capa'] ? BASE_URL . '/../uploads/covers/' . e($folder['capa']) : 'https://placehold.co/600x400' ?>" alt="capa">
        <div class="card-body d-flex flex-column">
          <h5><?= e($folder['nome']) ?></h5>
          <p class="text-muted small mb-1">Por <?= e($folder['autor']) ?> • <?= e($folder['categoria'] ?? 'Sem categoria') ?></p>
          <p class="small"><?= e(mb_strimwidth($folder['descricao'], 0, 120, '...')) ?></p>
          <a class="btn btn-outline-primary mt-auto" href="<?= BASE_URL ?>/p/<?= e($folder['slug']) ?>">Abrir pasta</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (!$folders): ?><p>Nenhuma pasta encontrada.</p><?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
