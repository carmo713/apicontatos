Coleção de exportação para Postman

- Arquivo: postman_apicontatos.collection.json

Como usar

- Postman: abra Postman → Import → selecione este arquivo JSON.
- Insomnia: abra Insomnia → Import/Export → Import Data → selecione "From File" e escolha este arquivo (Insomnia aceita importação de coleções Postman).

Variáveis

- `baseUrl`: URL base da API (padrão: http://localhost:8000)
- `token`: token Bearer do usuário autenticado (para endpoints protegidos por Sanctum)

Notas

- Endpoints contemplados: GET /api/contatos, POST /api/contatos, GET/PUT/DELETE /api/contatos/:id, PATCH /api/contatos/:id/favorito
- Ajuste `baseUrl` e preencha `token` antes de testar.
