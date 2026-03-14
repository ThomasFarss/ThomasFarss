<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

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
        return $this->db->query('SELECT id, nome, email, role, status, criado_em FROM usuarios ORDER BY criado_em DESC')->fetchAll();
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
