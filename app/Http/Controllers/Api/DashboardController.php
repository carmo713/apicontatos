<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContatoResource;
use App\Models\Contato;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;


class DashboardController extends Controller
{
    #[OA\Get(
        path: "/api/dashboard",
        operationId: "dashboard",
        summary: "Dashboard da agenda",
        description: "Retorna um resumo da agenda do usuário autenticado.",
        tags: ["Dashboard"],
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Dashboard retornado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "total_contatos",
                            type: "integer",
                            example: 15
                        ),
                        new OA\Property(
                            property: "total_favoritos",
                            type: "integer",
                            example: 6
                        ),
                        new OA\Property(
                            property: "contatos_mes",
                            type: "integer",
                            example: 3
                        ),
                        new OA\Property(
                            property: "ultimos_contatos",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(
                                        property: "id",
                                        type: "integer",
                                        example: 20
                                    ),
                                    new OA\Property(
                                        property: "nome",
                                        type: "string",
                                        example: "Carlos"
                                    ),
                                    new OA\Property(
                                        property: "telefone",
                                        type: "string",
                                        example: "(38)99999-9999"
                                    ),
                                    new OA\Property(
                                        property: "email",
                                        type: "string",
                                        example: "carlos@email.com"
                                    ),
                                    new OA\Property(
                                        property: "empresa",
                                        type: "string",
                                        example: "Empresa X"
                                    ),
                                    new OA\Property(
                                        property: "favorito",
                                        type: "boolean",
                                        example: true
                                    ),
                                    new OA\Property(
                                        property: "created_at",
                                        type: "string",
                                        format: "date-time",
                                        example: "2026-07-14T14:30:00.000000Z"
                                    )
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Usuário não autenticado"
            )
        ]
    )]
    public function index(Request $request)
    {
        $user = $request->user();

        $totalContatos = Contato::where('user_id', $user->id)
            ->count();

        $totalFavoritos = Contato::where('user_id', $user->id)
            ->where('favorito', true)
            ->count();

        $contatosMes = Contato::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $ultimosContatos = Contato::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'total_contatos' => $totalContatos,
            'total_favoritos' => $totalFavoritos,
            'contatos_mes' => $contatosMes,
            'ultimos_contatos' => ContatoResource::collection($ultimosContatos),
        ]);
    }
}
