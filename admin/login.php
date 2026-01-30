<?php
require_once __DIR__ . '/../includes/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: /admin/index.php');
        exit();
    }

    $error = 'Usuário ou senha inválidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin - Help Loj</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="/styles.css" />
  </head>
  <body class="theme-white">
    <main class="auth">
      <div class="auth__card">
        <img src="/logo.svg" alt="Help Loj" class="logo__image" />
        <h1>Entrar no Admin</h1>
        <p>Use o usuário padrão <strong>admin</strong> e senha <strong>admin123</strong>.</p>
        <?php if ($error): ?>
          <div class="auth__error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" class="auth__form">
          <label>
            Usuário
            <input type="text" name="username" required />
          </label>
          <label>
            Senha
            <input type="password" name="password" required />
          </label>
          <button class="btn btn--primary" type="submit">Entrar</button>
        </form>
        <a class="auth__back" href="/index.php">← Voltar para loja</a>
      </div>
    </main>
  </body>
</html>
