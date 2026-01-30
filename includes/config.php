<?php
session_start();

define('APP_DB_PATH', __DIR__ . '/../data/app.db');
define('APP_BASE_URL', (function () {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $scriptDir = rtrim($scriptDir, '/');
    if ($scriptDir === '/') {
        $scriptDir = '';
    }
    if (str_ends_with($scriptDir, '/admin')) {
        $scriptDir = substr($scriptDir, 0, -6);
    }
    return $scriptDir;
})());

define('APP_DEFAULT_USER', 'admin');
define('APP_DEFAULT_PASS', 'admin123');

if (!file_exists(dirname(APP_DB_PATH))) {
    mkdir(dirname(APP_DB_PATH), 0755, true);
}

try {
    $db = new PDO('sqlite:' . APP_DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro ao conectar ao banco de dados.');
}

$db->exec(
    'CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password_hash TEXT NOT NULL
    )'
);

$db->exec(
    'CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price REAL NOT NULL,
        category TEXT NOT NULL,
        description TEXT NOT NULL,
        image TEXT NOT NULL,
        tag TEXT,
        discount INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )'
);

$existingUser = $db->prepare('SELECT COUNT(*) FROM users');
$existingUser->execute();
if ($existingUser->fetchColumn() == 0) {
    $hash = password_hash(APP_DEFAULT_PASS, PASSWORD_DEFAULT);
    $insert = $db->prepare('INSERT INTO users (username, password_hash) VALUES (:username, :hash)');
    $insert->execute([
        ':username' => APP_DEFAULT_USER,
        ':hash' => $hash,
    ]);
}

$existingProducts = $db->prepare('SELECT COUNT(*) FROM products');
$existingProducts->execute();
if ($existingProducts->fetchColumn() == 0) {
    $seed = $db->prepare(
        'INSERT INTO products (name, price, category, description, image, tag, discount)
         VALUES (:name, :price, :category, :description, :image, :tag, :discount)'
    );
    $samples = [
        [
            'name' => 'Gift Card Xbox R$ 50',
            'price' => 50,
            'category' => 'Gift Cards',
            'description' => 'Entrega instantânea',
            'image' => 'https://images.unsplash.com/photo-1606813907291-d86efa86ca65?auto=format&fit=crop&w=400&q=80',
            'tag' => 'Destaque',
            'discount' => 0,
        ],
        [
            'name' => 'Steam Gift Card R$ 200',
            'price' => 200,
            'category' => 'Gift Cards',
            'description' => 'Entrega rápida',
            'image' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=400&q=80',
            'tag' => 'Destaque',
            'discount' => 10,
        ],
        [
            'name' => 'Nintendo eShop R$ 100',
            'price' => 100,
            'category' => 'Jogos Digitais',
            'description' => 'Oficial e seguro',
            'image' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=400&q=80',
            'tag' => 'Destaque',
            'discount' => 0,
        ],
        [
            'name' => 'Pacote de Jogos Indie',
            'price' => 79.9,
            'category' => 'Jogos Digitais',
            'description' => 'Seleção premium',
            'image' => 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=400&q=80',
            'tag' => 'Destaque',
            'discount' => 40,
        ],
    ];
    foreach ($samples as $sample) {
        $seed->execute([
            ':name' => $sample['name'],
            ':price' => $sample['price'],
            ':category' => $sample['category'],
            ':description' => $sample['description'],
            ':image' => $sample['image'],
            ':tag' => $sample['tag'],
            ':discount' => $sample['discount'],
        ]);
    }
}

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . base_url('admin/login.php'));
        exit();
    }
}

function current_user(PDO $db): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $stmt = $db->prepare('SELECT id, username FROM users WHERE id = :id');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

function base_url(string $path = ''): string
{
    $base = APP_BASE_URL;
    $path = ltrim($path, '/');
    if ($base === '') {
        return '/' . $path;
    }
    if ($path === '') {
        return $base;
    }
    return $base . '/' . $path;
}
