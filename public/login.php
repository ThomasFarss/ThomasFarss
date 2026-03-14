<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';

$auth = new Auth();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Falha de segurança CSRF.'];
        redirect('login.php');
    }

    $result = $auth->login($_POST['email'] ?? '', $_POST['password'] ?? '');
    $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'danger', 'message' => $result['message']];
    if ($result['success']) {
        redirect(isAdmin() ? 'admin/dashboard.php' : 'usuario/dashboard.php');
    }
}

$title = 'Login';
include __DIR__ . '/../includes/header.php';
?>
<div class="col-md-5 mx-auto">
  <div class="card shadow-sm"><div class="card-body">
    <h1 class="h4 mb-3">Entrar</h1>
    <form method="POST" class="needs-validation" novalidate>
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <div class="mb-3"><label>E-mail</label><input class="form-control" type="email" name="email" required></div>
      <div class="mb-3"><label>Senha</label><input class="form-control" type="password" name="password" required></div>
      <button class="btn btn-primary w-100">Entrar</button>
      <a class="d-block mt-3 text-center" href="<?= BASE_URL ?>/forgot_password.php">Esqueci minha senha</a>
    </form>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
