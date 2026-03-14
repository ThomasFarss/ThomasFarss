<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/FolderManager.php';

$manager = new FolderManager();
$id = (int) ($_GET['id'] ?? 0);
$arquivo = $manager->registerDownload($id);

if (!$arquivo) {
    http_response_code(404);
    exit('Arquivo não encontrado.');
}

$path = FILE_DIR . '/' . $arquivo['caminho'];
if (!is_file($path)) {
    http_response_code(404);
    exit('Arquivo indisponível.');
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($arquivo['nome_original']) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
