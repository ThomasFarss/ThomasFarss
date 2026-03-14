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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">GameVault</a>
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
            <div class="d-flex gap-2">
                <?php if (!isLoggedIn()): ?>
                    <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/login.php">Login</a>
                    <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/register.php">Cadastro</a>
                <?php else: ?>
                    <span class="text-white mt-1 small">Olá, <?= e($_SESSION['user']['nome']) ?></span>
                    <a class="btn btn-danger btn-sm" href="<?= BASE_URL ?>/logout.php">Sair</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="container pb-5">
    <?php if ($message): ?>
        <div class="alert alert-<?= e($message['type']) ?>"><?= e($message['message']) ?></div>
    <?php endif; ?>
