# GameVault Downloads (PHP + MySQL)

Sistema web completo para gerenciamento e publicação de pastas/arquivos de download de jogos, com:

- Área pública
- Cadastro/login/recuperação de senha
- Painel do usuário
- Painel administrativo
- Upload seguro e download com contadores
- Pastas públicas, privadas (com senha) e em desenvolvimento

## 1) Estrutura de pastas

```txt
/config
/admin
/usuario
/public
  /assets/css
  /assets/js
/uploads
  /covers
  /files
/includes
/classes
/sql
```

## 2) Requisitos

- PHP 8.1+
- MySQL 5.7+ ou MariaDB equivalente
- Apache com `mod_rewrite`
- phpMyAdmin (opcional)

## 3) Instalação

1. Copie o projeto para seu servidor local (XAMPP/WAMP/LAMP).
2. Crie/importe o banco em `phpMyAdmin` com o script: `sql/gamevault.sql`.
3. Ajuste conexão em `config/config.php` (DB_HOST, DB_NAME, DB_USER, DB_PASS e BASE_URL).
4. Garanta permissão de escrita no diretório `uploads/`.
5. Acesse `http://localhost/ThomasFarss/public`.

## 4) Credenciais de teste

- **Admin**: `admin@gamevault.local` / `123456`
- **Usuário**: `joao@example.com` / `123456`

> Observação: hashes já estão no SQL. Em produção, troque as senhas imediatamente.

## 5) Fluxo principal

### Área pública
- `public/index.php`: busca e filtro por categoria, cards das pastas públicas aprovadas.
- `public/folder.php`: exibe a pasta por slug amigável; trata status privada/desenvolvimento.
- `public/download.php`: registra contagem e serve arquivo.

### Autenticação
- `public/register.php`: cadastro com hash seguro (`password_hash`).
- `public/login.php`: login com `password_verify`.
- `public/forgot_password.php` + `public/reset_password.php`: recuperação por token.
- `public/logout.php`: encerra sessão.

### Painel do usuário
- `usuario/dashboard.php`: resumo e listagem de publicações.
- `usuario/folder_form.php`: criar/editar pasta (capa, categoria, status, senha opcional).
- `usuario/files.php`: upload de arquivos por pasta.
- `usuario/profile.php`, `usuario/change_password.php`.

### Painel admin
- `admin/dashboard.php`: dashboard, usuários, pastas, arquivos, categorias e logs.
- Aprovação de conteúdo, bloqueio/reativação de usuários, remoção de pastas.

## 6) Segurança implementada

- PDO com prepared statements (anti-SQL injection)
- Escape de saída com `htmlspecialchars` (anti-XSS)
- CSRF token nos principais formulários de autenticação/publicação
- Upload com whitelist de extensões
- Senhas com hash `password_hash` e validação com `password_verify`
- Controle de sessão e middlewares (`requireLogin`, `requireAdmin`)

## 7) URLs amigáveis

Arquivo `public/.htaccess`:
- `/p/{slug}` → `folder.php?slug={slug}`
- `/download/{id}` → `download.php?id={id}`

## 8) Observações de produção

- Mover segredos de banco para variáveis de ambiente.
- Limitar tamanho máximo de upload no `php.ini`.
- Implementar exclusão física de arquivos removidos.
- Integrar envio real de e-mail para recuperação de senha.

## Novo módulo Android (Java)

Foi adicionado um projeto Android Studio em `android-app/` com autenticação Firebase, gerenciamento de pastas de jogos e painel administrativo inicial.
