<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateContactsExport;
use App\Models\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class ExportController extends Controller
{
    #[OA\Get(
        path: "/api/exports",
        summary: "Lista todas as exportações do usuário autenticado",
        security: [["sanctum" => []]],
        tags: ["Exportações"],
    )]
    #[OA\Response(
        response: 200,
        description: "Lista de exportações retornada com sucesso"
    )]


    public function index(Request $request)
    {
        return Export::where(
            'user_id',
            $request->user()->id
        )->latest()->get();
    }

    #[OA\Post(
        path: "/api/exports",
        summary: "Solicita uma nova exportação de contatos",
        security: [["sanctum" => []]],
        tags: ["Exportações"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["formato"],
                properties: [
                    new OA\Property(
                        property: "formato",
                        type: "string",
                        enum: ["csv", "xlsx", "pdf"],
                        example: "csv",
                        description: "Formato do arquivo para exportação"
                    ),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Exportação solicitada com sucesso"
    )]
    #[OA\Response(
        response: 422,
        description: "Formato inválido"
    )]

    public function store(Request $request)
    {
        $export = Export::create([

            'user_id' => $request->user()->id,

            'formato' => $request->formato,

            'status' => 'Pendente'

        ]);

        GenerateContactsExport::dispatch($export);

        return response()->json([

            'message' => 'Exportação iniciada.',

            'export' => $export

        ], 201);
    }

    #[OA\Get(
        path: "/api/exports/{id}",
        summary: "Consulta o status de uma exportação",
        security: [["sanctum" => []]],
        tags: ["Exportações"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID da exportação",
                schema: new OA\Schema(type: "integer")
            ),
        ]
    )]
    #[OA\Response(
        response: 200,
        description: "Status da exportação retornado com sucesso"
    )]
    #[OA\Response(
        response: 404,
        description: "Exportação não encontrada"
    )]

    public function show($id, Request $request)
    {
        return Export::where(
            'user_id',
            $request->user()->id
        )->findOrFail($id);
    }

    #[OA\Get(
        path: "/api/exports/{id}/download",
        summary: "Realiza o download da exportação concluída",
        security: [["sanctum" => []]],
        tags: ["Exportações"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID da exportação",
                schema: new OA\Schema(type: "integer")
            ),
        ]
    )]
    #[OA\Response(
        response: 200,
        description: "Arquivo baixado com sucesso"
    )]
    #[OA\Response(
        response: 400,
        description: "A exportação ainda não foi concluída"
    )]
    #[OA\Response(
        response: 404,
        description: "Exportação não encontrada"
    )]

    public function download($id, Request $request)
    {
        $export = Export::where(
            'user_id',
            $request->user()->id
        )->findOrFail($id);

        if ($export->status != 'Concluído') {
            return response()->json([
                'message' => 'Arquivo ainda não está pronto.'
            ], 400);
        }

        return Storage::download(
            $export->caminho_arquivo
        );
    }
}
