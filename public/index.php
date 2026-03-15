<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/FolderManager.php';

$search = trim($_GET['q'] ?? '');
$category = (int) ($_GET['categoria'] ?? 0);
$folders = [];
$categories = [];
$dbError = null;

try {
    $manager = new FolderManager();
    $folders = $manager->publicFolders($search, $category);
    $categories = $manager->categories();
} catch (Throwable $e) {
    $dbError = 'Banco de dados indisponível no momento. Verifique sua conexão MySQL.';
}
$title = 'Biblioteca de Downloads';
include __DIR__ . '/../includes/header.php';
?>

<section class="hero mb-4">
  <div class="row align-items-center g-3">
    <div class="col-lg-8">
      <span class="hero-chip"><i class="bi bi-stars me-2"></i>GameVault Experience</span>
      <h1 class="h2 fw-bold mt-2">Plataforma moderna para downloads de jogos</h1>
      <p class="mb-0">Publicação profissional com busca inteligente, pasta privada por senha e painel completo para criadores.</p>
    </div>
    <div class="col-lg-4">
      <div class="panel-card p-3 text-dark stat-box">
        <div class="small-muted mb-2">Resumo público</div>
        <div class="d-flex justify-content-between"><span>Pastas disponíveis</span><strong><?= count($folders) ?></strong></div>
        <div class="d-flex justify-content-between"><span>Categorias ativas</span><strong><?= count($categories) ?></strong></div>
      </div>
    </div>
  </div>
</section>

<?php if ($dbError): ?><div class="alert alert-warning"><?= e($dbError) ?></div><?php endif; ?>

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
  <div class="col-md-2 d-grid"><button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
</form>

<div class="row g-4">
  <?php foreach ($folders as $folder): ?>
    <div class="col-md-6 col-xl-4">
      <div class="card h-100 p-2 folder-card">
        <img class="card-img-top card-cover" src="<?= $folder['capa'] ? BASE_URL . '/../uploads/covers/' . e($folder['capa']) : 'https://placehold.co/800x500/eceffd/5a6dff?text=GameVault' ?>" alt="capa">
        <div class="card-body d-flex flex-column">
          <div class="d-flex justify-content-between align-items-start mb-1">
            <h5 class="mb-0"><?= e($folder['nome']) ?></h5>
            <span class="badge text-bg-light border"><?= e($folder['categoria'] ?? 'Sem categoria') ?></span>
          </div>
          <p class="text-muted small mb-1">Por <?= e($folder['autor']) ?></p>
          <p class="small-muted mb-3"><?= e(mb_strimwidth($folder['descricao'], 0, 120, '...')) ?></p>
          <a class="btn btn-outline-primary mt-auto" href="<?= BASE_URL ?>/folder.php?slug=<?= urlencode($folder['slug']) ?>"><i class="bi bi-folder2-open me-1"></i>Abrir pasta</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (!$folders): ?><p class="small-muted">Nenhuma pasta encontrada para os filtros selecionados.</p><?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
