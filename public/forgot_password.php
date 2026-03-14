<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';

$auth = new Auth();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->forgotPassword($_POST['email'] ?? '');
    $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'danger', 'message' => $result['message']];
}

$title = 'Recuperar senha';
include __DIR__ . '/../includes/header.php';
?>
<div class="col-md-6 mx-auto">
  <div class="card shadow-sm"><div class="card-body">
    <h1 class="h4">Recuperação de senha</h1>
    <p class="text-muted">Em ambiente local o token é exibido em tela para facilitar testes.</p>
    <form method="POST">
      <div class="mb-3"><label>E-mail</label><input class="form-control" type="email" name="email" required></div>
      <button class="btn btn-primary">Gerar token</button>
    </form>
    <a class="d-block mt-3" href="<?= BASE_URL ?>/reset_password.php">Já tenho token</a>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
