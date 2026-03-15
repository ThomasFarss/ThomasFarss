<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

class AdminManager
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function stats(): array
    {
        return [
            'usuarios' => (int) $this->db->query('SELECT COUNT(*) FROM usuarios')->fetchColumn(),
            'pastas' => (int) $this->db->query('SELECT COUNT(*) FROM pastas')->fetchColumn(),
            'arquivos' => (int) $this->db->query('SELECT COUNT(*) FROM arquivos')->fetchColumn(),
            'downloads' => (int) $this->db->query('SELECT COUNT(*) FROM downloads')->fetchColumn(),
        ];
    }

    public function users(): array
    {
        $sql = 'SELECT u.id, u.nome, u.email, u.role, u.status, u.criado_em,
                       p.manage_users, p.manage_content, p.manage_categories, p.view_logs
                FROM usuarios u
                LEFT JOIN permissoes_usuario p ON p.usuario_id = u.id
                ORDER BY u.criado_em DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function folders(): array
    {
        $sql = 'SELECT p.*, u.nome AS autor, c.nome AS categoria FROM pastas p
                INNER JOIN usuarios u ON u.id = p.usuario_id
                LEFT JOIN categorias c ON c.id = p.categoria_id
                ORDER BY p.criado_em DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function files(): array
    {
        $sql = 'SELECT a.*, p.nome AS pasta FROM arquivos a INNER JOIN pastas p ON p.id = a.pasta_id ORDER BY a.criado_em DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function logs(): array
    {
        return $this->db->query('SELECT l.*, u.nome FROM logs l LEFT JOIN usuarios u ON u.id = l.usuario_id ORDER BY l.criado_em DESC LIMIT 100')->fetchAll();
    }

    public function setFolderApproval(int $id, int $approved): void
    {
        $stmt = $this->db->prepare('UPDATE pastas SET aprovado = :aprovado WHERE id = :id');
        $stmt->execute(['id' => $id, 'aprovado' => $approved]);
    }

    public function updateUserStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET status = :status WHERE id = :id');
        $stmt->execute(['id' => $id, 'status' => $status]);
    }

    public function updateUserAccess(int $id, string $role, string $status, array $permissions): void
    {
        $this->db->beginTransaction();

        $stmt = $this->db->prepare('UPDATE usuarios SET role = :role, status = :status WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'role' => $role,
            'status' => $status,
        ]);

        $permStmt = $this->db->prepare(
            'INSERT INTO permissoes_usuario (usuario_id, manage_users, manage_content, manage_categories, view_logs, atualizado_em)
             VALUES (:usuario_id, :manage_users, :manage_content, :manage_categories, :view_logs, NOW())
             ON DUPLICATE KEY UPDATE
             manage_users = VALUES(manage_users),
             manage_content = VALUES(manage_content),
             manage_categories = VALUES(manage_categories),
             view_logs = VALUES(view_logs),
             atualizado_em = NOW()'
        );

        $permStmt->execute([
            'usuario_id' => $id,
            'manage_users' => !empty($permissions['manage_users']) ? 1 : 0,
            'manage_content' => !empty($permissions['manage_content']) ? 1 : 0,
            'manage_categories' => !empty($permissions['manage_categories']) ? 1 : 0,
            'view_logs' => !empty($permissions['view_logs']) ? 1 : 0,
        ]);

        $this->db->commit();
    }

    public function removeFolder(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM pastas WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function categories(): array
    {
        return $this->db->query('SELECT * FROM categorias ORDER BY nome')->fetchAll();
    }

    public function addCategory(string $name): void
    {
        $stmt = $this->db->prepare('INSERT INTO categorias (nome, slug, status, criado_em) VALUES (:nome, :slug, 1, NOW())');
        $stmt->execute(['nome' => $name, 'slug' => slugify($name)]);
    }
}
