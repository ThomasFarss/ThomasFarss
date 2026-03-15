<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'GameVault Downloads');
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost/ThomasFarss/public');
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'gamevault');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads');
define('COVER_DIR', UPLOAD_DIR . '/covers');
define('FILE_DIR', UPLOAD_DIR . '/files');
define('ALLOWED_EXTENSIONS', ['zip', 'rar', '7z', 'exe', 'iso', 'pdf', 'txt', 'apk']);

date_default_timezone_set('America/Sao_Paulo');
