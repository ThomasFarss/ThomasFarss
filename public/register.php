<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';

$auth = new Auth();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Falha de segurança CSRF.'];
        redirect('register.php');
    }

    $result = $auth->register($_POST);
    $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'danger', 'message' => $result['message']];
    if ($result['success']) {
        redirect('login.php');
    }
}

$title = 'Cadastro';
include __DIR__ . '/../includes/header.php';
?>
<div class="col-md-6 mx-auto">
  <div class="card shadow-sm"><div class="card-body">
    <h1 class="h4 mb-3">Criar conta</h1>
    <form method="POST" class="needs-validation" novalidate>
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <div class="mb-3"><label>Nome</label><input class="form-control" type="text" name="nome" required></div>
      <div class="mb-3"><label>E-mail</label><input class="form-control" type="email" name="email" required></div>
      <div class="mb-3"><label>Senha</label><input class="form-control" type="password" name="password" minlength="6" required></div>
      <button class="btn btn-success w-100">Cadastrar</button>
    </form>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
