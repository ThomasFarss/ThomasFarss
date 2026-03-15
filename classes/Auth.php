<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

class Auth
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function register(array $data): array
    {
        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'E-mail inválido.'];
        }

        if (strlen($data['password'] ?? '') < 6) {
            return ['success' => false, 'message' => 'A senha deve ter ao menos 6 caracteres.'];
        }

        $stmt = $this->db->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $data['email']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'E-mail já cadastrado.'];
        }

        $insert = $this->db->prepare(
            'INSERT INTO usuarios (nome, email, senha_hash, role, status, criado_em) VALUES (:nome, :email, :senha, :role, :status, NOW())'
        );
        $insert->execute([
            'nome' => trim($data['nome']),
            'email' => strtolower(trim($data['email'])),
            'senha' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'usuario',
            'status' => 'ativo',
        ]);

        return ['success' => true, 'message' => 'Cadastro realizado com sucesso.'];
    }

    public function login(string $login, string $password): array
    {
        $login = trim($login);
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :login OR LOWER(nome) = :login LIMIT 1');
        $stmt->execute(['login' => strtolower($login)]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['senha_hash'])) {
            return ['success' => false, 'message' => 'Credenciais inválidas.'];
        }

        if ($user['status'] !== 'ativo') {
            return ['success' => false, 'message' => 'Conta bloqueada.'];
        }

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'nome' => $user['nome'],
            'email' => $user['email'],
            'role' => $user['role'],
            'permissions' => $this->loadPermissions((int) $user['id']),
        ];

        $this->log('login', 'Usuário autenticado');
        return ['success' => true, 'message' => 'Login efetuado com sucesso.'];
    }

    public function logout(): void
    {
        $this->log('logout', 'Usuário encerrou sessão');
        session_destroy();
    }

    public function forgotPassword(string $email): array
    {
        $stmt = $this->db->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => strtolower(trim($email))]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'E-mail não encontrado.'];
        }

        $token = bin2hex(random_bytes(32));
        $insert = $this->db->prepare('INSERT INTO recuperacao_senha (usuario_id, token, expira_em, criado_em, usado) VALUES (:id, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR), NOW(), 0)');
        $insert->execute(['id' => $user['id'], 'token' => $token]);

        return ['success' => true, 'message' => 'Token gerado. Use para reset local: ' . $token];
    }

    public function resetPassword(string $token, string $newPassword): array
    {
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'A senha deve ter ao menos 6 caracteres.'];
        }

        $stmt = $this->db->prepare('SELECT * FROM recuperacao_senha WHERE token = :token AND usado = 0 AND expira_em > NOW() LIMIT 1');
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();

        if (!$row) {
            return ['success' => false, 'message' => 'Token inválido ou expirado.'];
        }

        $this->db->beginTransaction();
        $updateUser = $this->db->prepare('UPDATE usuarios SET senha_hash = :senha WHERE id = :id');
        $updateToken = $this->db->prepare('UPDATE recuperacao_senha SET usado = 1 WHERE id = :id');

        $updateUser->execute(['senha' => password_hash($newPassword, PASSWORD_DEFAULT), 'id' => $row['usuario_id']]);
        $updateToken->execute(['id' => $row['id']]);
        $this->db->commit();

        return ['success' => true, 'message' => 'Senha redefinida com sucesso.'];
    }

    public function updateProfile(int $id, array $data): array
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id');
        $stmt->execute([
            'nome' => trim($data['nome']),
            'email' => strtolower(trim($data['email'])),
            'id' => $id,
        ]);

        $_SESSION['user']['nome'] = trim($data['nome']);
        $_SESSION['user']['email'] = strtolower(trim($data['email']));
        return ['success' => true, 'message' => 'Perfil atualizado.'];
    }

    public function changePassword(int $id, string $oldPassword, string $newPassword): array
    {
        $stmt = $this->db->prepare('SELECT senha_hash FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($oldPassword, $user['senha_hash'])) {
            return ['success' => false, 'message' => 'Senha atual inválida.'];
        }

        $update = $this->db->prepare('UPDATE usuarios SET senha_hash = :senha WHERE id = :id');
        $update->execute(['senha' => password_hash($newPassword, PASSWORD_DEFAULT), 'id' => $id]);

        return ['success' => true, 'message' => 'Senha alterada com sucesso.'];
    }

    private function loadPermissions(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT manage_users, manage_content, manage_categories, view_logs FROM permissoes_usuario WHERE usuario_id = :id LIMIT 1');
        $stmt->execute(['id' => $userId]);
        $perm = $stmt->fetch();

        return [
            'manage_users' => (bool)($perm['manage_users'] ?? false),
            'manage_content' => (bool)($perm['manage_content'] ?? false),
            'manage_categories' => (bool)($perm['manage_categories'] ?? false),
            'view_logs' => (bool)($perm['view_logs'] ?? false),
        ];
    }

    private function log(string $acao, string $mensagem): void
    {
        if (empty($_SESSION['user']['id'])) {
            return;
        }

        $stmt = $this->db->prepare('INSERT INTO logs (usuario_id, acao, mensagem, ip, criado_em) VALUES (:usuario_id, :acao, :mensagem, :ip, NOW())');
        $stmt->execute([
            'usuario_id' => $_SESSION['user']['id'],
            'acao' => $acao,
            'mensagem' => $mensagem,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'cli',
        ]);
    }
}
