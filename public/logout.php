<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';

$auth = new Auth();
$auth->logout();
session_start();
$_SESSION['flash'] = ['type' => 'success', 'message' => 'Logout realizado com sucesso.'];
header('Location: ' . BASE_URL . '/login.php');
exit;
