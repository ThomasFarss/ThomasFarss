<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/FolderManager.php';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Pasta inválida.'];
    redirect('index.php');
}

$folder = null;
$dbError = null;

try {
    $manager = new FolderManager();
    $folder = $manager->getFolderBySlug($slug);
} catch (Throwable $e) {
    $dbError = 'Não foi possível carregar a pasta. Verifique a conexão com o MySQL.';
}

if ($dbError) {
    $title = 'Erro de conexão';
    include __DIR__ . '/../includes/header.php';
    echo '<div class="alert alert-warning">' . e($dbError) . '</div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

if (!$folder) {
    http_response_code(404);
    exit('Pasta não encontrada.');
}

if ($folder['status'] === 'desenvolvimento') {
    $title = $folder['nome'];
    include __DIR__ . '/../includes/header.php';
    echo '<div class="alert alert-warning">Conteúdo em desenvolvimento. Em breve disponível.</div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

if ($folder['status'] === 'privada') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (password_verify($_POST['senha_acesso'] ?? '', $folder['senha_acesso'])) {
            $_SESSION['folder_access'][$folder['id']] = true;
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Senha incorreta para esta pasta.'];
        }
    }

    if (empty($_SESSION['folder_access'][$folder['id']])) {
        $title = 'Acesso privado';
        include __DIR__ . '/../includes/header.php';
        ?>
        <div class="col-md-6 mx-auto">
          <div class="card shadow-sm"><div class="card-body p-4">
            <h1 class="h5 fw-bold">Pasta privada: <?= e($folder['nome']) ?></h1>
            <p class="small-muted">Insira a senha definida pelo autor para visualizar os arquivos.</p>
            <form method="POST" class="needs-validation" novalidate>
              <div class="mb-3"><label class="form-label">Senha de acesso</label><input class="form-control" name="senha_acesso" required type="password"></div>
              <button class="btn btn-primary"><i class="bi bi-unlock me-1"></i>Entrar</button>
            </form>
          </div></div>
        </div>
        <?php
        include __DIR__ . '/../includes/footer.php';
        exit;
    }
}

$title = $folder['nome'];
include __DIR__ . '/../includes/header.php';
?>
<section class="folder-hero mb-4">
  <div class="row g-4 align-items-center">
    <div class="col-md-4">
      <img class="img-fluid rounded-4 shadow-sm" src="<?= $folder['capa'] ? BASE_URL . '/../uploads/covers/' . e($folder['capa']) : 'https://placehold.co/700x450/eceffd/5a6dff?text=GameVault' ?>" alt="capa">
    </div>
    <div class="col-md-8">
      <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge text-bg-light border"><?= e($folder['categoria'] ?? 'Sem categoria') ?></span>
        <span class="badge text-bg-primary"><?= e($folder['status']) ?></span>
      </div>
      <h1 class="h3 fw-bold mb-1"><?= e($folder['nome']) ?></h1>
      <p class="small-muted mb-2"><i class="bi bi-person-circle me-1"></i>Autor: <?= e($folder['autor']) ?></p>
      <p class="mb-0"><?= nl2br(e($folder['descricao'])) ?></p>
    </div>
  </div>
</section>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h5 mb-0">Arquivos para download</h2>
  <span class="small-muted">Total: <?= count($folder['arquivos']) ?> arquivo(s)</span>
</div>

<div class="table-responsive bg-white shadow-sm">
<table class="table table-striped mb-0 align-middle">
<thead><tr><th>Nome</th><th>Tamanho</th><th>Extensão</th><th>Downloads</th><th></th></tr></thead>
<tbody>
<?php if ($folder['arquivos']): ?>
<?php foreach ($folder['arquivos'] as $file): ?>
<tr>
<td><?= e($file['nome']) ?></td>
<td><?= e(formatBytes((int) $file['tamanho'])) ?></td>
<td><span class="badge text-bg-light border"><?= e($file['extensao']) ?></span></td>
<td><?= (int) $file['downloads'] ?></td>
<td><a class="btn btn-sm btn-success" href="<?= BASE_URL ?>/download.php?id=<?= (int) $file['id'] ?>&slug=<?= urlencode($folder['slug']) ?>"><i class="bi bi-download me-1"></i>Baixar</a></td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr><td colspan="5" class="text-center small-muted py-4">Esta pasta ainda não possui arquivos disponíveis.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
