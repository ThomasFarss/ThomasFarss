CREATE DATABASE IF NOT EXISTS gamevault CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamevault;

CREATE TABLE administradores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  nivel ENUM('master','moderador') DEFAULT 'moderador',
  criado_em DATETIME NOT NULL,
  UNIQUE KEY uq_admin_usuario (usuario_id)
);

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  role ENUM('usuario','admin') NOT NULL DEFAULT 'usuario',
  status ENUM('ativo','bloqueado') NOT NULL DEFAULT 'ativo',
  criado_em DATETIME NOT NULL,
  atualizado_em DATETIME NULL,
  INDEX idx_usuarios_status (status),
  INDEX idx_usuarios_role (role)
);

CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL UNIQUE,
  status TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL
);

CREATE TABLE pastas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  categoria_id INT NULL,
  nome VARCHAR(180) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  descricao TEXT NOT NULL,
  capa VARCHAR(255) NULL,
  status ENUM('publica','privada','desenvolvimento') NOT NULL DEFAULT 'desenvolvimento',
  senha_acesso VARCHAR(255) NULL,
  aprovado TINYINT(1) NOT NULL DEFAULT 0,
  criado_em DATETIME NOT NULL,
  atualizado_em DATETIME NULL,
  INDEX idx_pastas_status (status),
  INDEX idx_pastas_usuario (usuario_id),
  INDEX idx_pastas_categoria (categoria_id),
  CONSTRAINT fk_pastas_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_pastas_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

CREATE TABLE arquivos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pasta_id INT NOT NULL,
  nome VARCHAR(180) NOT NULL,
  nome_original VARCHAR(255) NOT NULL,
  caminho VARCHAR(255) NOT NULL,
  tamanho BIGINT NOT NULL,
  extensao VARCHAR(20) NOT NULL,
  downloads INT NOT NULL DEFAULT 0,
  criado_em DATETIME NOT NULL,
  INDEX idx_arquivos_pasta (pasta_id),
  INDEX idx_arquivos_extensao (extensao),
  CONSTRAINT fk_arquivos_pasta FOREIGN KEY (pasta_id) REFERENCES pastas(id) ON DELETE CASCADE
);

CREATE TABLE downloads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  arquivo_id INT NOT NULL,
  pasta_id INT NOT NULL,
  usuario_id INT NULL,
  ip VARCHAR(50) NOT NULL,
  criado_em DATETIME NOT NULL,
  INDEX idx_downloads_arquivo (arquivo_id),
  INDEX idx_downloads_pasta (pasta_id),
  INDEX idx_downloads_usuario (usuario_id),
  CONSTRAINT fk_downloads_arquivo FOREIGN KEY (arquivo_id) REFERENCES arquivos(id) ON DELETE CASCADE,
  CONSTRAINT fk_downloads_pasta FOREIGN KEY (pasta_id) REFERENCES pastas(id) ON DELETE CASCADE,
  CONSTRAINT fk_downloads_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE configuracoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  chave_config VARCHAR(120) NOT NULL UNIQUE,
  valor TEXT NULL,
  atualizado_em DATETIME NULL
);

CREATE TABLE logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NULL,
  acao VARCHAR(100) NOT NULL,
  mensagem VARCHAR(255) NOT NULL,
  ip VARCHAR(50) NOT NULL,
  criado_em DATETIME NOT NULL,
  INDEX idx_logs_usuario (usuario_id),
  INDEX idx_logs_acao (acao),
  CONSTRAINT fk_logs_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE recuperacao_senha (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  token VARCHAR(80) NOT NULL UNIQUE,
  expira_em DATETIME NOT NULL,
  criado_em DATETIME NOT NULL,
  usado TINYINT(1) NOT NULL DEFAULT 0,
  INDEX idx_recuperacao_usuario (usuario_id),
  CONSTRAINT fk_recuperacao_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

INSERT INTO usuarios (nome, email, senha_hash, role, status, criado_em) VALUES
('Admin Principal', 'admin@gamevault.local', '$2y$10$k0P/5mJPzUJxLfRkQzKe6e9HGg50Q5N68vJ.WxMyls07gs2XWwY2u', 'admin', 'ativo', NOW()),
('João Gamer', 'joao@example.com', '$2y$10$k0P/5mJPzUJxLfRkQzKe6e9HGg50Q5N68vJ.WxMyls07gs2XWwY2u', 'usuario', 'ativo', NOW());

INSERT INTO administradores (usuario_id, nivel, criado_em) VALUES (1, 'master', NOW());

INSERT INTO categorias (nome, slug, status, criado_em) VALUES
('Ação', 'acao', 1, NOW()),
('RPG', 'rpg', 1, NOW()),
('Estratégia', 'estrategia', 1, NOW());

INSERT INTO pastas (usuario_id, categoria_id, nome, slug, descricao, capa, status, senha_acesso, aprovado, criado_em) VALUES
(2, 2, 'Coleção RPG Indie', 'colecao-rpg-indie', 'Pacote com títulos indie de RPG para PC.', NULL, 'publica', NULL, 1, NOW()),
(2, 1, 'Builds privadas de ação', 'builds-privadas-acao', 'Conteúdo exclusivo para parceiros.', NULL, 'privada', '$2y$10$k0P/5mJPzUJxLfRkQzKe6e9HGg50Q5N68vJ.WxMyls07gs2XWwY2u', 1, NOW()),
(2, 3, 'Projeto tático 2026', 'projeto-tatico-2026', 'Material ainda em produção.', NULL, 'desenvolvimento', NULL, 1, NOW());

INSERT INTO arquivos (pasta_id, nome, nome_original, caminho, tamanho, extensao, downloads, criado_em) VALUES
(1, 'RPG Indie Vol.1', 'rpg-indie-vol1.zip', 'exemplo1.zip', 124000000, 'zip', 12, NOW()),
(1, 'RPG Indie Vol.2', 'rpg-indie-vol2.zip', 'exemplo2.zip', 91000000, 'zip', 8, NOW()),
(2, 'Build de ação fechada', 'build-acao.rar', 'exemplo3.rar', 240000000, 'rar', 3, NOW());

INSERT INTO configuracoes (chave_config, valor, atualizado_em) VALUES
('site_titulo', 'GameVault Downloads', NOW()),
('itens_por_pagina', '12', NOW());

INSERT INTO logs (usuario_id, acao, mensagem, ip, criado_em) VALUES
(1, 'seed', 'Carga inicial aplicada', '127.0.0.1', NOW()),
(2, 'publicacao', 'Pasta RPG publicada', '127.0.0.1', NOW());
