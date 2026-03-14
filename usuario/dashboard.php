<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/FolderManager.php';

requireLogin();
$manager = new FolderManager();
$folders = $manager->userFolders((int) $_SESSION['user']['id']);
$title = 'Painel do Usuário';
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Minhas publicações</h1>
  <a class="btn btn-primary" href="<?= BASE_URL ?>/usuario/folder_form.php">Nova pasta</a>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="card"><div class="card-body"><strong><?= count($folders) ?></strong><br>Pastas</div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-body"><strong><?= array_sum(array_column($folders, 'total_arquivos')) ?></strong><br>Arquivos</div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-body"><strong><?= array_sum(array_column($folders, 'total_downloads')) ?></strong><br>Downloads</div></div></div>
</div>

<div class="table-responsive bg-white shadow-sm">
<table class="table table-hover mb-0">
<thead><tr><th>Nome</th><th>Status</th><th>Aprovação</th><th>Arquivos</th><th>Downloads</th><th>Ações</th></tr></thead>
<tbody>
<?php foreach ($folders as $f): ?>
<tr>
  <td><?= e($f['nome']) ?></td>
  <td><span class="badge text-bg-secondary status-pill"><?= e($f['status']) ?></span></td>
  <td><?= $f['aprovado'] ? 'Aprovada' : 'Pendente' ?></td>
  <td><?= (int)$f['total_arquivos'] ?></td>
  <td><?= (int)$f['total_downloads'] ?></td>
  <td>
    <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/usuario/folder_form.php?id=<?= (int)$f['id'] ?>">Editar</a>
    <a class="btn btn-sm btn-outline-success" href="<?= BASE_URL ?>/usuario/files.php?folder_id=<?= (int)$f['id'] ?>">Arquivos</a>
    <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir pasta?')" href="<?= BASE_URL ?>/usuario/folder_delete.php?id=<?= (int)$f['id'] ?>">Excluir</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<div class="mt-3 d-flex gap-2">
  <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/usuario/profile.php">Editar perfil</a>
  <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/usuario/change_password.php">Alterar senha</a>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
