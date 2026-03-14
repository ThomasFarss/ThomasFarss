<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/FolderManager.php';

requireLogin();
$manager = new FolderManager();
$folderId = (int) ($_GET['folder_id'] ?? 0);
$folder = $manager->findFolder($folderId, (int) $_SESSION['user']['id']);
if (!$folder) {
    redirect('usuario/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $manager->addFile($folderId, (int) $_SESSION['user']['id'], $_FILES['arquivo'], $_POST['nome'] ?? '');
    $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'danger', 'message' => $result['message']];
    redirect('usuario/files.php?folder_id=' . $folderId);
}

$fullFolder = $manager->getFolderBySlug($folder['slug']);
$title = 'Arquivos da pasta';
include __DIR__ . '/../includes/header.php';
?>
<h1 class="h4">Arquivos - <?= e($folder['nome']) ?></h1>
<div class="row g-4">
  <div class="col-md-4">
    <div class="card shadow-sm"><div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3"><label>Nome do arquivo</label><input class="form-control" name="nome" required></div>
        <div class="mb-3"><label>Arquivo</label><input class="form-control" type="file" name="arquivo" required></div>
        <button class="btn btn-success w-100">Enviar</button>
      </form>
    </div></div>
  </div>
  <div class="col-md-8">
    <div class="table-responsive bg-white shadow-sm">
      <table class="table table-striped mb-0"><thead><tr><th>Arquivo</th><th>Tamanho</th><th>Downloads</th></tr></thead><tbody>
      <?php foreach ($fullFolder['arquivos'] as $a): ?>
      <tr><td><?= e($a['nome']) ?></td><td><?= e(formatBytes((int)$a['tamanho'])) ?></td><td><?= (int)$a['downloads'] ?></td></tr>
      <?php endforeach; ?>
      </tbody></table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
