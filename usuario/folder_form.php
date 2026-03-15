<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/FolderManager.php';

requireLogin();
$manager = new FolderManager();
$categories = $manager->categories();
$id = (int) ($_GET['id'] ?? 0);
$folder = $id ? $manager->findFolder($id, (int) $_SESSION['user']['id']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Falha CSRF.'];
        redirect('usuario/folder_form.php' . ($id ? '?id=' . $id : ''));
    }
    $result = $id
        ? $manager->updateFolder($id, (int) $_SESSION['user']['id'], $_POST, $_FILES['capa'])
        : $manager->createFolder((int) $_SESSION['user']['id'], $_POST, $_FILES['capa']);

    $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'danger', 'message' => $result['message']];
    if ($result['success']) {
        redirect('usuario/dashboard.php');
    }
}

$title = $id ? 'Editar pasta' : 'Nova pasta';
include __DIR__ . '/../includes/header.php';
?>
<div class="col-lg-8 mx-auto">
  <div class="card shadow-sm"><div class="card-body">
    <h1 class="h4"><?= e($title) ?></h1>
    <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <div class="mb-3"><label>Nome</label><input class="form-control" name="nome" required value="<?= e($folder['nome'] ?? '') ?>"></div>
      <div class="mb-3"><label>Descrição</label><textarea class="form-control" rows="4" name="descricao" required><?= e($folder['descricao'] ?? '') ?></textarea></div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label>Categoria</label>
          <select class="form-select" name="categoria_id" required>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= (int)$cat['id'] ?>" <?= ($folder['categoria_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>><?= e($cat['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label>Status</label>
          <select class="form-select" name="status" required>
            <?php foreach (['publica','privada','desenvolvimento'] as $s): ?>
              <option value="<?= $s ?>" <?= ($folder['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="mb-3"><label>Senha da pasta (quando privada)</label><input class="form-control" name="senha_acesso" type="password"></div>
      <div class="mb-3"><label>Imagem de capa</label><input class="form-control" type="file" name="capa" accept=".jpg,.jpeg,.png,.webp"></div>
      <button class="btn btn-primary">Salvar</button>
    </form>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
