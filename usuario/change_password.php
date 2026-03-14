<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/Auth.php';

requireLogin();
$auth = new Auth();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->changePassword((int)$_SESSION['user']['id'], $_POST['senha_atual'] ?? '', $_POST['nova_senha'] ?? '');
    $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'danger', 'message' => $result['message']];
    redirect('usuario/change_password.php');
}
$title = 'Alterar senha';
include __DIR__ . '/../includes/header.php';
?>
<div class="col-md-6 mx-auto"><div class="card"><div class="card-body">
<h1 class="h4">Alterar senha</h1>
<form method="POST">
<div class="mb-3"><label>Senha atual</label><input class="form-control" type="password" name="senha_atual" required></div>
<div class="mb-3"><label>Nova senha</label><input class="form-control" type="password" name="nova_senha" minlength="6" required></div>
<button class="btn btn-primary">Atualizar senha</button>
</form>
</div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
