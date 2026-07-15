Coleção de exportação para Postman

- Arquivo: postman_apiContacts.collection.json

Como usar

- Postman: abra Postman → Import → selecione este arquivo JSON.
- Insomnia: abra Insomnia → Import/Export → Import Data → selecione "From File" e escolha este arquivo (Insomnia aceita importação de coleções Postman).

Variáveis

- `baseUrl`: URL base da API (padrão: http://localhost:8000)
- `token`: token Bearer do usuário autenticado (para endpoints protegidos por Sanctum)

Notas

- Endpoints contemplados: GET /api/Contacts, POST /api/Contacts, GET/PUT/DELETE /api/Contacts/:id, PATCH /api/Contacts/:id/favorite
- Ajuste `baseUrl` e preencha `token` antes de testar.
