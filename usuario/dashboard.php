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
<div class="dashboard-title">
  <div><h1 class="h4 mb-0 fw-bold">Minhas publicações</h1><p class="small-muted mb-0">Gerencie suas pastas, uploads e desempenho de downloads.</p></div>
  <a class="btn btn-primary" href="<?= BASE_URL ?>/usuario/folder_form.php"><i class="bi bi-plus-circle me-1"></i>Nova pasta</a>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="card panel-card"><div class="card-body metric-card"><div><div class="small-muted">Pastas</div><h4 class="mb-0"><?= count($folders) ?></h4></div><div class="icon"><i class="bi bi-folder"></i></div></div></div></div>
  <div class="col-md-4"><div class="card panel-card"><div class="card-body metric-card"><div><div class="small-muted">Arquivos</div><h4 class="mb-0"><?= array_sum(array_column($folders, 'total_arquivos')) ?></h4></div><div class="icon"><i class="bi bi-file-earmark"></i></div></div></div></div>
  <div class="col-md-4"><div class="card panel-card"><div class="card-body metric-card"><div><div class="small-muted">Downloads</div><h4 class="mb-0"><?= array_sum(array_column($folders, 'total_downloads')) ?></h4></div><div class="icon"><i class="bi bi-download"></i></div></div></div></div>
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
