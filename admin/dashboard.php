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

if (isset($_GET['remover_pasta'])) {
    $admin->removeFolder((int) $_GET['remover_pasta']);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Pasta removida.'];
    redirect('admin/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Falha de segurança CSRF.'];
        redirect('admin/dashboard.php');
    }

    if (!empty($_POST['categoria_nome'])) {
        $admin->addCategory(trim($_POST['categoria_nome']));
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Categoria adicionada.'];
        redirect('admin/dashboard.php');
    }

    if (($_POST['action'] ?? '') === 'update_user_access') {
        $id = (int) ($_POST['usuario_id'] ?? 0);
        $role = in_array($_POST['role'] ?? '', ['admin', 'usuario'], true) ? $_POST['role'] : 'usuario';
        $status = in_array($_POST['status'] ?? '', ['ativo', 'bloqueado'], true) ? $_POST['status'] : 'ativo';

        $permissions = [
            'manage_users' => !empty($_POST['manage_users']),
            'manage_content' => !empty($_POST['manage_content']),
            'manage_categories' => !empty($_POST['manage_categories']),
            'view_logs' => !empty($_POST['view_logs']),
        ];

        if ($id > 0) {
            $admin->updateUserAccess($id, $role, $status, $permissions);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Permissões e perfil do usuário atualizados.'];
        }

        redirect('admin/dashboard.php');
    }
}

$stats = ['usuarios' => 0, 'pastas' => 0, 'arquivos' => 0, 'downloads' => 0];
$users = [];
$folders = [];
$files = [];
$logs = [];
$categories = [];
$dbError = null;

try {
    $stats = $admin->stats();
    $users = $admin->users();
    $folders = $admin->folders();
    $files = $admin->files();
    $logs = $admin->logs();
    $categories = $admin->categories();
} catch (Throwable $e) {
    $dbError = 'Banco de dados indisponível no momento para o painel administrativo.';
}

$title = 'Painel Administrativo';
include __DIR__ . '/../includes/header.php';
?>

<?php if ($dbError): ?><div class="alert alert-warning"><?= e($dbError) ?></div><?php endif; ?>

<section class="admin-header mb-4">
  <div class="row align-items-center g-3">
    <div class="col-xl-4">
      <h1 class="h4 fw-bold mb-1">Visão Geral Administrativa</h1>
      <p class="mb-0">Gerencie usuários, permissões, moderação de conteúdo e segurança da plataforma.</p>
    </div>
    <div class="col-xl-8">
      <div class="row g-2">
        <div class="col-md-4"><input class="form-control" placeholder="Ano" value="2026" disabled></div>
        <div class="col-md-4"><input class="form-control" placeholder="Mês" value="Todos" disabled></div>
        <div class="col-md-4"><input class="form-control" placeholder="Filtro" value="GameVault" disabled></div>
      </div>
    </div>
  </div>
</section>

<div class="row g-3 mb-4">
  <div class="col-md-6 col-xl-3"><div class="card panel-card metric-bg-1"><div class="card-body metric-card"><div><div class="small-muted text-white-50">Usuários</div><h4 class="mb-0 text-white"><?= $stats['usuarios'] ?></h4></div><div class="icon"><i class="bi bi-people"></i></div></div></div></div>
  <div class="col-md-6 col-xl-3"><div class="card panel-card metric-bg-2"><div class="card-body metric-card"><div><div class="small-muted text-white-50">Pastas</div><h4 class="mb-0 text-white"><?= $stats['pastas'] ?></h4></div><div class="icon"><i class="bi bi-folder2-open"></i></div></div></div></div>
  <div class="col-md-6 col-xl-3"><div class="card panel-card metric-bg-3"><div class="card-body metric-card"><div><div class="small-muted text-white-50">Arquivos</div><h4 class="mb-0 text-white"><?= $stats['arquivos'] ?></h4></div><div class="icon"><i class="bi bi-file-earmark-arrow-up"></i></div></div></div></div>
  <div class="col-md-6 col-xl-3"><div class="card panel-card metric-bg-4"><div class="card-body metric-card"><div><div class="small-muted text-white-50">Downloads</div><h4 class="mb-0 text-white"><?= $stats['downloads'] ?></h4></div><div class="icon"><i class="bi bi-cloud-arrow-down"></i></div></div></div></div>
</div>

<div class="row g-4">
  <div class="col-xl-8">
    <h2 class="h5 mb-3">Controle de usuários e permissões</h2>
    <div class="table-responsive bg-white shadow-sm mb-4">
      <table class="table table-sm align-middle mb-0">
        <thead>
          <tr><th>Usuário</th><th>Perfil</th><th>Status</th><th>Permissões</th><th>Ações</th></tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td>
              <div class="fw-semibold"><?= e($u['nome']) ?></div>
              <div class="small-muted"><?= e($u['email']) ?></div>
            </td>
            <td><?= e($u['role']) ?></td>
            <td><?= e($u['status']) ?></td>
            <td>
              <span class="badge text-bg-light border">Usuários: <?= !empty($u['manage_users']) ? 'Sim' : 'Não' ?></span>
              <span class="badge text-bg-light border">Conteúdo: <?= !empty($u['manage_content']) ? 'Sim' : 'Não' ?></span>
              <span class="badge text-bg-light border">Categorias: <?= !empty($u['manage_categories']) ? 'Sim' : 'Não' ?></span>
              <span class="badge text-bg-light border">Logs: <?= !empty($u['view_logs']) ? 'Sim' : 'Não' ?></span>
            </td>
            <td>
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#user-access-<?= (int) $u['id'] ?>">
                Editar acesso
              </button>
            </td>
          </tr>
          <tr class="collapse" id="user-access-<?= (int) $u['id'] ?>">
            <td colspan="5" class="bg-light">
              <form method="POST" class="row g-2 align-items-center">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="action" value="update_user_access">
                <input type="hidden" name="usuario_id" value="<?= (int) $u['id'] ?>">
                <div class="col-md-2">
                  <label class="form-label mb-1">Perfil</label>
                  <select class="form-select" name="role">
                    <option value="usuario" <?= $u['role'] === 'usuario' ? 'selected' : '' ?>>Usuário</option>
                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label class="form-label mb-1">Status</label>
                  <select class="form-select" name="status">
                    <option value="ativo" <?= $u['status'] === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="bloqueado" <?= $u['status'] === 'bloqueado' ? 'selected' : '' ?>>Bloqueado</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label mb-1 d-block">Permissões</label>
                  <div class="d-flex flex-wrap gap-3">
                    <label><input type="checkbox" name="manage_users" <?= !empty($u['manage_users']) ? 'checked' : '' ?>> Gerenciar usuários</label>
                    <label><input type="checkbox" name="manage_content" <?= !empty($u['manage_content']) ? 'checked' : '' ?>> Moderar conteúdo</label>
                    <label><input type="checkbox" name="manage_categories" <?= !empty($u['manage_categories']) ? 'checked' : '' ?>> Categorias</label>
                    <label><input type="checkbox" name="view_logs" <?= !empty($u['view_logs']) ? 'checked' : '' ?>> Ver logs</label>
                  </div>
                </div>
                <div class="col-md-2 d-grid align-self-end">
                  <button class="btn btn-primary btn-sm">Salvar</button>
                </div>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <h2 class="h5">Pastas publicadas</h2>
    <div class="table-responsive bg-white shadow-sm mb-4"><table class="table table-sm mb-0"><thead><tr><th>Nome</th><th>Autor</th><th>Status</th><th>Aprovado</th><th></th></tr></thead><tbody>
    <?php foreach ($folders as $f): ?><tr>
    <td><?= e($f['nome']) ?></td><td><?= e($f['autor']) ?></td><td><?= e($f['status']) ?></td><td><?= $f['aprovado'] ? 'Sim' : 'Não' ?></td>
    <td><a class="btn btn-sm btn-outline-success" href="?aprovar=<?= $f['id'] ?>">Aprovar</a> <a class="btn btn-sm btn-outline-secondary" href="?reprovar=<?= $f['id'] ?>">Pendente</a> <a class="btn btn-sm btn-outline-danger" href="?remover_pasta=<?= $f['id'] ?>">Remover</a></td>
    </tr><?php endforeach; ?>
    </tbody></table></div>

    <h2 class="h5">Arquivos enviados</h2>
    <div class="table-responsive bg-white shadow-sm mb-4"><table class="table table-sm mb-0"><thead><tr><th>Arquivo</th><th>Pasta</th><th>Tamanho</th><th>Downloads</th></tr></thead><tbody>
    <?php foreach ($files as $file): ?><tr><td><?= e($file['nome']) ?></td><td><?= e($file['pasta']) ?></td><td><?= e(formatBytes((int) $file['tamanho'])) ?></td><td><?= (int) $file['downloads'] ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
  </div>

  <div class="col-xl-4">
    <h2 class="h5">Categorias</h2>
    <div class="card panel-card mb-4"><div class="card-body">
      <form method="POST" class="row g-2">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <div class="col-12"><input class="form-control" name="categoria_nome" placeholder="Nova categoria" required></div>
        <div class="col-12 d-grid"><button class="btn btn-primary">Adicionar</button></div>
      </form>
      <hr>
      <div class="d-flex flex-wrap gap-2"><?php foreach ($categories as $cat): ?><span class="badge text-bg-dark"><?= e($cat['nome']) ?></span><?php endforeach; ?></div>
    </div></div>

    <h2 class="h5">Logs do sistema</h2>
    <div class="table-responsive bg-white shadow-sm"><table class="table table-sm mb-0"><thead><tr><th>Usuário</th><th>Ação</th><th>Data</th></tr></thead><tbody>
    <?php foreach ($logs as $log): ?><tr><td><?= e($log['nome'] ?? 'Sistema') ?></td><td><?= e($log['acao']) ?></td><td><?= e($log['criado_em']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
