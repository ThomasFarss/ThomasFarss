<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/FolderManager.php';

$slug = $_GET['slug'] ?? '';
$manager = new FolderManager();
$folder = $manager->getFolderBySlug($slug);

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
          <div class="card shadow-sm"><div class="card-body">
            <h1 class="h5">Pasta privada: <?= e($folder['nome']) ?></h1>
            <form method="POST" class="needs-validation" novalidate>
              <div class="mb-3"><label class="form-label">Senha de acesso</label><input class="form-control" name="senha_acesso" required type="password"></div>
              <button class="btn btn-primary">Entrar</button>
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
<div class="row g-4">
  <div class="col-md-4"><img class="img-fluid rounded" src="<?= $folder['capa'] ? BASE_URL . '/../uploads/covers/' . e($folder['capa']) : 'https://placehold.co/600x400' ?>" alt="capa"></div>
  <div class="col-md-8">
    <h1 class="h3"><?= e($folder['nome']) ?></h1>
    <p class="text-muted">Autor: <?= e($folder['autor']) ?> • Categoria: <?= e($folder['categoria'] ?? 'N/A') ?></p>
    <p><?= nl2br(e($folder['descricao'])) ?></p>
  </div>
</div>

<h2 class="h5 mt-4">Arquivos para download</h2>
<div class="table-responsive bg-white shadow-sm">
<table class="table table-striped mb-0">
<thead><tr><th>Nome</th><th>Tamanho</th><th>Extensão</th><th>Downloads</th><th></th></tr></thead>
<tbody>
<?php foreach ($folder['arquivos'] as $file): ?>
<tr>
<td><?= e($file['nome']) ?></td>
<td><?= e(formatBytes((int) $file['tamanho'])) ?></td>
<td><?= e($file['extensao']) ?></td>
<td><?= (int) $file['downloads'] ?></td>
<td><a class="btn btn-sm btn-success" href="<?= BASE_URL ?>/download/<?= (int) $file['id'] ?>">Baixar</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
