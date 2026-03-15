<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../classes/FolderManager.php';

$manager = new FolderManager();
$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Arquivo inválido para download.'];
    redirect('index.php');
}

$arquivo = $manager->registerDownload($id);
if (!$arquivo) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Arquivo não encontrado.'];
    redirect('index.php');
}

$path = FILE_DIR . '/' . $arquivo['caminho'];
if (!is_file($path)) {
    $_SESSION['flash'] = [
        'type' => 'warning',
        'message' => 'Este arquivo ainda não foi enviado ao servidor. Faça upload no painel do usuário.',
    ];

    $fallbackSlug = $_GET['slug'] ?? '';
    if ($fallbackSlug !== '') {
        redirect('folder.php?slug=' . urlencode($fallbackSlug));
    }

    redirect('index.php');
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($arquivo['nome_original']) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
