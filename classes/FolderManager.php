<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

class FolderManager
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function categories(): array
    {
        return $this->db->query('SELECT * FROM categorias WHERE status = 1 ORDER BY nome')->fetchAll();
    }

    public function createFolder(int $userId, array $data, array $cover): array
    {
        $slug = slugify($data['nome'] ?? '');
        $coverPath = $this->uploadCover($cover);
        if (isset($coverPath['error'])) {
            return ['success' => false, 'message' => $coverPath['error']];
        }

        $stmt = $this->db->prepare('INSERT INTO pastas (usuario_id, categoria_id, nome, slug, descricao, capa, status, senha_acesso, aprovado, criado_em) VALUES (:usuario_id, :categoria_id, :nome, :slug, :descricao, :capa, :status, :senha, :aprovado, NOW())');
        $stmt->execute([
            'usuario_id' => $userId,
            'categoria_id' => (int) $data['categoria_id'],
            'nome' => trim($data['nome']),
            'slug' => $slug,
            'descricao' => trim($data['descricao']),
            'capa' => $coverPath['path'] ?? null,
            'status' => $data['status'],
            'senha' => $data['status'] === 'privada' && !empty($data['senha_acesso']) ? password_hash($data['senha_acesso'], PASSWORD_DEFAULT) : null,
            'aprovado' => 0,
        ]);

        return ['success' => true, 'message' => 'Pasta criada e enviada para aprovação.'];
    }

    public function updateFolder(int $folderId, int $userId, array $data, array $cover): array
    {
        $folder = $this->findFolder($folderId, $userId);
        if (!$folder) {
            return ['success' => false, 'message' => 'Pasta não encontrada.'];
        }

        $coverPath = $folder['capa'];
        if (!empty($cover['name'])) {
            $uploaded = $this->uploadCover($cover);
            if (isset($uploaded['error'])) {
                return ['success' => false, 'message' => $uploaded['error']];
            }
            $coverPath = $uploaded['path'];
        }

        $stmt = $this->db->prepare('UPDATE pastas SET categoria_id=:categoria_id, nome=:nome, slug=:slug, descricao=:descricao, capa=:capa, status=:status, senha_acesso=:senha, aprovado=0, atualizado_em=NOW() WHERE id=:id AND usuario_id=:usuario_id');
        $stmt->execute([
            'categoria_id' => (int) $data['categoria_id'],
            'nome' => trim($data['nome']),
            'slug' => slugify($data['nome']),
            'descricao' => trim($data['descricao']),
            'capa' => $coverPath,
            'status' => $data['status'],
            'senha' => $data['status'] === 'privada' && !empty($data['senha_acesso']) ? password_hash($data['senha_acesso'], PASSWORD_DEFAULT) : $folder['senha_acesso'],
            'id' => $folderId,
            'usuario_id' => $userId,
        ]);

        return ['success' => true, 'message' => 'Pasta atualizada e reenviada para aprovação.'];
    }

    public function deleteFolder(int $folderId, int $userId): void
    {
        $stmt = $this->db->prepare('DELETE FROM pastas WHERE id = :id AND usuario_id = :usuario_id');
        $stmt->execute(['id' => $folderId, 'usuario_id' => $userId]);
    }

    public function userFolders(int $userId): array
    {
        $sql = 'SELECT p.*, c.nome AS categoria,
                (SELECT COUNT(*) FROM arquivos a WHERE a.pasta_id = p.id) AS total_arquivos,
                (SELECT COALESCE(SUM(a.downloads),0) FROM arquivos a WHERE a.pasta_id = p.id) AS total_downloads
                FROM pastas p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE p.usuario_id = :usuario_id
                ORDER BY p.criado_em DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findFolder(int $folderId, int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM pastas WHERE id = :id AND usuario_id = :usuario_id');
        $stmt->execute(['id' => $folderId, 'usuario_id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function addFile(int $folderId, int $userId, array $file, string $nome): array
    {
        $folder = $this->findFolder($folderId, $userId);
        if (!$folder) {
            return ['success' => false, 'message' => 'Pasta inválida.'];
        }

        if (empty($file['name'])) {
            return ['success' => false, 'message' => 'Selecione um arquivo.'];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS, true)) {
            return ['success' => false, 'message' => 'Extensão não permitida.'];
        }

        $filename = uniqid('dl_') . '.' . $extension;
        $target = FILE_DIR . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['success' => false, 'message' => 'Falha no upload.'];
        }

        $stmt = $this->db->prepare('INSERT INTO arquivos (pasta_id, nome, nome_original, caminho, tamanho, extensao, criado_em, downloads) VALUES (:pasta_id,:nome,:nome_original,:caminho,:tamanho,:extensao,NOW(),0)');
        $stmt->execute([
            'pasta_id' => $folderId,
            'nome' => trim($nome) ?: $file['name'],
            'nome_original' => $file['name'],
            'caminho' => $filename,
            'tamanho' => (int) $file['size'],
            'extensao' => $extension,
        ]);

        return ['success' => true, 'message' => 'Arquivo enviado com sucesso.'];
    }


    public function deleteFile(int $fileId, int $folderId, int $userId): void
    {
        $folder = $this->findFolder($folderId, $userId);
        if (!$folder) {
            return;
        }

        $stmt = $this->db->prepare('SELECT id, caminho FROM arquivos WHERE id = :id AND pasta_id = :pasta_id LIMIT 1');
        $stmt->execute(['id' => $fileId, 'pasta_id' => $folderId]);
        $file = $stmt->fetch();
        if (!$file) {
            return;
        }

        $this->db->prepare('DELETE FROM arquivos WHERE id = :id')->execute(['id' => $fileId]);

        $path = FILE_DIR . '/' . $file['caminho'];
        if (is_file($path)) {
            @unlink($path);
        }
    }

    public function publicFolders(string $search = '', int $category = 0): array
    {
        $sql = 'SELECT p.*, u.nome AS autor, c.nome AS categoria FROM pastas p
                INNER JOIN usuarios u ON u.id = p.usuario_id
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE p.status = "publica" AND p.aprovado = 1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (p.nome LIKE :search OR p.descricao LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if ($category > 0) {
            $sql .= ' AND p.categoria_id = :categoria';
            $params['categoria'] = $category;
        }

        $sql .= ' ORDER BY p.criado_em DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getFolderBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT p.*, u.nome AS autor, c.nome AS categoria FROM pastas p INNER JOIN usuarios u ON u.id = p.usuario_id LEFT JOIN categorias c ON c.id = p.categoria_id WHERE p.slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $folder = $stmt->fetch();
        if (!$folder) {
            return null;
        }

        $filesStmt = $this->db->prepare('SELECT * FROM arquivos WHERE pasta_id = :pasta_id ORDER BY criado_em DESC');
        $filesStmt->execute(['pasta_id' => $folder['id']]);
        $folder['arquivos'] = $filesStmt->fetchAll();

        return $folder;
    }

    public function registerDownload(int $arquivoId): ?array
    {
        $stmt = $this->db->prepare('SELECT a.*, p.id AS pasta_id FROM arquivos a INNER JOIN pastas p ON p.id = a.pasta_id WHERE a.id = :id');
        $stmt->execute(['id' => $arquivoId]);
        $arquivo = $stmt->fetch();
        if (!$arquivo) {
            return null;
        }

        $this->db->prepare('UPDATE arquivos SET downloads = downloads + 1 WHERE id = :id')->execute(['id' => $arquivoId]);
        $this->db->prepare('INSERT INTO downloads (arquivo_id, pasta_id, usuario_id, ip, criado_em) VALUES (:arquivo_id, :pasta_id, :usuario_id, :ip, NOW())')
            ->execute([
                'arquivo_id' => $arquivoId,
                'pasta_id' => $arquivo['pasta_id'],
                'usuario_id' => $_SESSION['user']['id'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'cli',
            ]);

        return $arquivo;
    }

    private function uploadCover(array $cover): array
    {
        if (empty($cover['name'])) {
            return ['path' => null];
        }

        $extension = strtolower(pathinfo($cover['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $allowed, true)) {
            return ['error' => 'Imagem de capa inválida.'];
        }

        $filename = uniqid('cover_') . '.' . $extension;
        $target = COVER_DIR . '/' . $filename;

        if (!move_uploaded_file($cover['tmp_name'], $target)) {
            return ['error' => 'Falha ao enviar imagem de capa.'];
        }

        return ['path' => $filename];
    }
}
