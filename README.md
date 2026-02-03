# Helpe Lojas - E-commerce Front-end

Projeto front-end completo para a **Helpe Lojas**, focado em produtos digitais como gift cards, emuladores e assinaturas. A aplicação é 100% em HTML, CSS e JavaScript puro, com dados simulados em `localStorage`.

## Como rodar

1. Abra o arquivo `public/index.html` diretamente no navegador.
2. Na primeira execução, o sistema carrega os dados iniciais de `data/seed.json`.

## Resetar dados

Para reiniciar o banco local:

1. Abra o DevTools do navegador.
2. Vá em **Application** > **Local Storage**.
3. Remova todas as chaves relacionadas à Helpe Lojas (ou execute `localStorage.clear()`).
4. Recarregue a página para restaurar os dados do `seed.json`.

## Estrutura de pastas

```
/public           # Páginas públicas da loja
/assets/css       # Estilos base, componentes, layouts
/assets/js        # Lógica de dados, autenticação e UI
/assets/img       # Imagens e placeholders
/data/seed.json   # Dados iniciais
```

## Observações

- O layout foi ajustado para um visual escuro e premium.
- O checkout é simulado (Pix/Cartão apenas em UI).
- Todo o fluxo funciona sem backend.
