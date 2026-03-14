# GameVault Manager (Android Studio / Java)

Aplicativo Android completo em Java para gerenciamento de pastas de jogos com controle de acesso por perfil, senha e grupos.

## Estrutura

- `activities/`: telas principais (login, cadastro, listagem, detalhes, criação/edição, painel admin etc)
- `models/`: entidades do Firestore
- `services/`: autenticação, acesso ao Firestore e regras de permissão
- `adapters/`: RecyclerView para lista de jogos
- `utils/`: validações e sessão local

## Recursos implementados

- Autenticação: login, cadastro, recuperação de senha e logout
- Sessão local persistida com `SharedPreferences`
- Cadastro/listagem de pastas de jogos com metadados
- Tipos de acesso: público, privado por senha, apenas dono e por grupo
- Painel administrativo (usuarios, grupos e monitoramento)
- Monitoramento de downloads
- Interface escura com Material Design

## Banco de dados

O app utiliza Firebase:

- Authentication
- Firestore (coleções: `users`, `folders`, `groups`, `downloads`)
- Storage (pronto para integração de upload de imagens/arquivos)

## Como executar

1. Abra a pasta `android-app` no Android Studio.
2. Crie um projeto Firebase e adicione `google-services.json` em `app/`.
3. Habilite Authentication (Email/Password), Firestore e Storage.
4. Sincronize o Gradle e execute no emulador/dispositivo.

## Exemplo de dados para teste (Firestore)

Coleção `groups`:
- VIP
- Moderadores
- Testadores
- Premium

Coleção `users` (exemplo):
```json
{
  "uid": "uid_teste",
  "name": "Administrador",
  "email": "admin@gamevault.com",
  "role": "ADMIN",
  "blocked": false,
  "groupIds": ["VIP"]
}
```

Coleção `folders` (exemplo):
```json
{
  "gameName": "Cyber Quest",
  "description": "RPG sci-fi com mundo aberto.",
  "category": "RPG",
  "coverImageUrl": "https://.../capa.jpg",
  "fileSize": "18 GB",
  "downloadLink": "https://.../download",
  "accessType": "GROUP_RESTRICTED",
  "ownerName": "Administrador",
  "ownerId": "uid_teste",
  "allowedGroupIds": ["VIP"],
  "downloadCount": 0
}
```

## Observações

- O layout foi preparado para expansão futura (novas entidades, APIs, permissões e monitoramento avançado).
- A Activity de edição reutiliza a tela de criação na versão inicial para acelerar evolução incremental.
