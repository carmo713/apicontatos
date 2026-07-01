<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(version: "1.0.0", title: "API Agenda de Contatos", description: "Documentação da API de Agenda de Contatos")]
#[OA\Server(url: "http://127.0.0.1:8000", description: "Servidor Local")]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'apiKey',
    name: 'Authorization',
    in: 'header',
    description: 'Autenticação via Laravel Sanctum. O frontend usa sessão de SPA '
        . '(cookie + CSRF); integrações externas podem usar um token pessoal no '
        . 'formato: Bearer <token>.'
)]

class OpenApi
{
}