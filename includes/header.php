<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once __DIR__ . '/helpers.php';

$message = flash();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container-fluid px-3 px-lg-4">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/index.php"><i class="bi bi-controller me-2"></i>GameVault</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"><span class="navbar-toggler-icon"></span></button>
        <div id="menu" class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php">Início</a></li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/usuario/dashboard.php">Painel do Usuário</a></li>
                <?php endif; ?>
                <?php if (isAdmin()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/dashboard.php">Admin</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex gap-2 align-items-center">
                <?php if (!isLoggedIn()): ?>
                    <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/login.php">Login</a>
                    <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/register.php">Cadastro</a>
                <?php else: ?>
                    <span class="text-white mt-1 small"><i class="bi bi-person-circle me-1"></i><?= e($_SESSION['user']['nome']) ?></span>
                    <a class="btn btn-danger btn-sm" href="<?= BASE_URL ?>/logout.php">Sair</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="main-shell">
    <?php if ($message): ?>
        <div class="alert alert-<?= e($message['type']) ?>"><?= e($message['message']) ?></div>
    <?php endif; ?>
