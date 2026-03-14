<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user']);
}

function isAdmin(): bool
{
    return !empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Faça login para continuar.'];
        redirect('login.php');
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Acesso restrito para administradores.'];
        redirect('index.php');
    }
}

function flash(?string $type = null): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);

    if ($type !== null && $message['type'] !== $type) {
        return null;
    }

    return $message;
}

function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $text));
    $text = trim($text, '-');
    return $text ?: uniqid('pasta-');
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(?string $token): bool
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
}

function formatBytes(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
    return number_format($bytes / (1024 ** $power), 2) . ' ' . $units[(int)$power];
}
