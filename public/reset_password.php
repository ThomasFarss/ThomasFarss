<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';

$auth = new Auth();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->resetPassword($_POST['token'] ?? '', $_POST['password'] ?? '');
    $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'danger', 'message' => $result['message']];
    if ($result['success']) {
        redirect('login.php');
    }
}

$title = 'Redefinir senha';
include __DIR__ . '/../includes/header.php';
?>
<div class="col-md-6 mx-auto">
  <div class="card shadow-sm"><div class="card-body">
    <h1 class="h4">Redefinir senha</h1>
    <form method="POST">
      <div class="mb-3"><label>Token</label><input class="form-control" name="token" required></div>
      <div class="mb-3"><label>Nova senha</label><input class="form-control" type="password" name="password" minlength="6" required></div>
      <button class="btn btn-success">Atualizar senha</button>
    </form>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
