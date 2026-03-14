<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/AdminManager.php';

requireLogin();
requireAdmin();

$admin = new AdminManager();
if (isset($_GET['aprovar'])) {
    $admin->setFolderApproval((int) $_GET['aprovar'], 1);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Pasta aprovada.'];
    redirect('admin/dashboard.php');
}
if (isset($_GET['reprovar'])) {
    $admin->setFolderApproval((int) $_GET['reprovar'], 0);
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Pasta marcada como pendente.'];
    redirect('admin/dashboard.php');
}
if (isset($_GET['bloquear'])) {
    $admin->updateUserStatus((int) $_GET['bloquear'], 'bloqueado');
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Usuário bloqueado.'];
    redirect('admin/dashboard.php');
}
if (isset($_GET['ativar'])) {
    $admin->updateUserStatus((int) $_GET['ativar'], 'ativo');
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Usuário ativado.'];
    redirect('admin/dashboard.php');
}
if (isset($_GET['remover_pasta'])) {
    $admin->removeFolder((int) $_GET['remover_pasta']);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Pasta removida.'];
    redirect('admin/dashboard.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['categoria_nome'])) {
    $admin->addCategory(trim($_POST['categoria_nome']));
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Categoria adicionada.'];
    redirect('admin/dashboard.php');
}

$stats = $admin->stats();
$users = $admin->users();
$folders = $admin->folders();
$files = $admin->files();
$logs = $admin->logs();
$categories = $admin->categories();
$title = 'Painel Administrativo';
include __DIR__ . '/../includes/header.php';
?>
<h1 class="h3 mb-3">Dashboard administrativo</h1>
<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="card"><div class="card-body"><strong><?= $stats['usuarios'] ?></strong><br>Usuários</div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><strong><?= $stats['pastas'] ?></strong><br>Pastas</div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><strong><?= $stats['arquivos'] ?></strong><br>Arquivos</div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><strong><?= $stats['downloads'] ?></strong><br>Downloads</div></div></div>
</div>

<h2 class="h5">Usuários</h2>
<div class="table-responsive bg-white shadow-sm mb-4"><table class="table table-sm mb-0"><thead><tr><th>Nome</th><th>Email</th><th>Perfil</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($users as $u): ?><tr>
<td><?= e($u['nome']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td><td><?= e($u['status']) ?></td>
<td>
<?php if ($u['status'] === 'ativo'): ?><a class="btn btn-sm btn-outline-warning" href="?bloquear=<?= $u['id'] ?>">Bloquear</a><?php else: ?><a class="btn btn-sm btn-outline-success" href="?ativar=<?= $u['id'] ?>">Ativar</a><?php endif; ?>
</td>
</tr><?php endforeach; ?>
</tbody></table></div>

<h2 class="h5">Pastas publicadas</h2>
<div class="table-responsive bg-white shadow-sm mb-4"><table class="table table-sm mb-0"><thead><tr><th>Nome</th><th>Autor</th><th>Status</th><th>Aprovado</th><th></th></tr></thead><tbody>
<?php foreach ($folders as $f): ?><tr>
<td><?= e($f['nome']) ?></td><td><?= e($f['autor']) ?></td><td><?= e($f['status']) ?></td><td><?= $f['aprovado'] ? 'Sim' : 'Não' ?></td>
<td><a class="btn btn-sm btn-outline-success" href="?aprovar=<?= $f['id'] ?>">Aprovar</a> <a class="btn btn-sm btn-outline-secondary" href="?reprovar=<?= $f['id'] ?>">Pendente</a> <a class="btn btn-sm btn-outline-danger" href="?remover_pasta=<?= $f['id'] ?>">Remover</a></td>
</tr><?php endforeach; ?>
</tbody></table></div>

<h2 class="h5">Arquivos enviados</h2>
<div class="table-responsive bg-white shadow-sm mb-4"><table class="table table-sm mb-0"><thead><tr><th>Arquivo</th><th>Pasta</th><th>Tamanho</th><th>Downloads</th></tr></thead><tbody>
<?php foreach ($files as $file): ?><tr><td><?= e($file['nome']) ?></td><td><?= e($file['pasta']) ?></td><td><?= e(formatBytes((int)$file['tamanho'])) ?></td><td><?= (int)$file['downloads'] ?></td></tr><?php endforeach; ?>
</tbody></table></div>

<h2 class="h5">Categorias</h2>
<form method="POST" class="row g-2 mb-4"><div class="col-md-4"><input class="form-control" name="categoria_nome" placeholder="Nova categoria" required></div><div class="col-md-2"><button class="btn btn-primary">Adicionar</button></div></form>
<div class="mb-4"><?php foreach ($categories as $cat): ?><span class="badge text-bg-dark me-1"><?= e($cat['nome']) ?></span><?php endforeach; ?></div>

<h2 class="h5">Logs do sistema</h2>
<div class="table-responsive bg-white shadow-sm"><table class="table table-sm mb-0"><thead><tr><th>Usuário</th><th>Ação</th><th>Mensagem</th><th>IP</th><th>Data</th></tr></thead><tbody>
<?php foreach ($logs as $log): ?><tr><td><?= e($log['nome'] ?? 'Sistema') ?></td><td><?= e($log['acao']) ?></td><td><?= e($log['mensagem']) ?></td><td><?= e($log['ip']) ?></td><td><?= e($log['criado_em']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
