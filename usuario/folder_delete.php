<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/FolderManager.php';

requireLogin();
$manager = new FolderManager();
$manager->deleteFolder((int)($_GET['id'] ?? 0), (int)$_SESSION['user']['id']);
$_SESSION['flash'] = ['type' => 'success', 'message' => 'Pasta removida.'];
redirect('usuario/dashboard.php');
