<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/Auth.php';

requireLogin();
$auth = new Auth();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->updateProfile((int)$_SESSION['user']['id'], $_POST);
    $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'danger', 'message' => $result['message']];
    redirect('usuario/profile.php');
}
$title = 'Meu perfil';
include __DIR__ . '/../includes/header.php';
?>
<div class="col-md-6 mx-auto"><div class="card"><div class="card-body">
<h1 class="h4">Editar perfil</h1>
<form method="POST">
<div class="mb-3"><label>Nome</label><input class="form-control" name="nome" value="<?= e($_SESSION['user']['nome']) ?>" required></div>
<div class="mb-3"><label>E-mail</label><input class="form-control" type="email" name="email" value="<?= e($_SESSION['user']['email']) ?>" required></div>
<button class="btn btn-primary">Salvar alterações</button>
</form>
</div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
