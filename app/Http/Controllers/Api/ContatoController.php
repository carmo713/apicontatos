<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contato;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ContatoController extends Controller
{
    #[OA\Get(
        path: "/api/contatos",
        summary: "Lista todos os contatos",
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "search",
                in: "query",
                required: false,
                description: "Termo de pesquisa para filtrar contatos pelo nome",
                schema: new OA\Schema(type: "string")
            )
            
        ],
        tags: ["Contatos"],
    )]
    #[OA\Response(response: 200, description: "Lista de contatos retornada com sucesso")]

    public function index(Request $request)
    {
        $query = auth()->user()->contacts();

        if ($request->has('search')) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        }

        return $query
            ->orderBy('nome')
            ->get();
    }

    #[OA\Post(
        path: "/api/contatos",
        summary: "Cria um novo contato",
        parameters: [
            new OA\Parameter(
                name: "nome",
                in: "query",
                required: true,
                description: "Nome do contato",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "telefone",
                in: "query",
                required: true,
                description: "Telefone do contato",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "email",
                in: "query",
                required: true,
                description: "Email do contato",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "favorito",
                in: "query",
                required: false,
                description: "Indica se o contato é favorito",
                schema: new OA\Schema(type: "boolean")
            )
        ],
        security: [["sanctum" => []]],
        tags: ["Contatos"],
    )]
    #[OA\Response(response: 201, description: "Contato criado com sucesso")]

    public function store(Request $request)
    {
        $request->merge([
            'favorito' => $request->has('favorito')
                ? $request->boolean('favorito')
                : false,
        ]);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'favorito' => 'boolean',
        ]);

        $contato = auth()->user()->contacts()->create($validated);

        return response()->json($contato, 201);
    }

    #[OA\Get(
        path: "/api/contatos/{contato}",
        summary: "Exibe um contato",
        parameters: [
            new OA\Parameter(
                name: "contato",
                in: "path",
                required: true,
                description: "ID do contato",
                schema: new OA\Schema(type: "integer")
            )
        ]
        ,
        security: [["sanctum" => []]],
        tags: ["Contatos"],
    )]
    #[OA\Response(response: 200, description: "Contato retornado com sucesso")]


    public function show(Contato $contato)
    {
        if (
            $contato->user_id
            != auth()->id()
        ) {
            abort(403);
        }

        return $contato;
    }


    #[OA\Put(
        path: "/api/contatos/{contato}",
        summary: "Atualiza um contato",
        parameters: [
            new OA\Parameter(
                name: "contato",
                in: "path",
                required: true,
                description: "ID do contato",
                schema: new OA\Schema(type: "integer")
            ),

            new OA\Parameter(
                name: "nome",
                in: "query",
                required: false,
                description: "Nome do contato",
                schema: new OA\Schema(type: "string")
            ),

            new OA\Parameter(
                name: "telefone",
                in: "query",
                required: false,
                description: "Telefone do contato",
                schema: new OA\Schema(type: "string")
            ),

            new OA\Parameter(
                name: "email",
                in: "query",
                required: false,
                description: "Email do contato",
                schema: new OA\Schema(type: "string")
            ),

            new OA\Parameter(
                name: "favorito",
                in: "query",
                required: false,
                description: "Indica se o contato é favorito",
                schema: new OA\Schema(type: "boolean")
            )
        ],
        security: [["sanctum" => []]],
        tags: ["Contatos"],
    )]
    #[OA\Response(response: 200, description: "Contato atualizado com sucesso")]

    public function update(Request $request, Contato $contato)
    {

        $request->merge([
            'favorito' => $request->has('favorito')
                ? $request->boolean('favorito')
                : false,
        ]);

        $validated = $request->validate([
            'nome' => 'string|max:255',
            'telefone' => 'string|max:20',
            'email' => 'string|email|max:255',
            'favorito' => 'boolean',
        ]);

        if ($contato->user_id != auth()->id()) {
            abort(403);
        }

        $contato->update(
            $validated
        );

        return $contato;
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: "/api/contatos/{contato}",
        summary: "Remove um contato",
        parameters: [
            new OA\Parameter(
                name: "contato",
                in: "path",
                required: true,
                description: "ID do contato",
                schema: new OA\Schema(type: "integer")
            )
        ],
        security: [["sanctum" => []]],
        tags: ["Contatos"],
    )]
    #[OA\Response(response: 200, description: "Contato removido com sucesso")]
    public function destroy(Contato $contato)
    {
        if ($contato->user_id != auth()->id()) {
            abort(403);
        }

        $contato->delete();

        return response()->json([
            'message' => 'Removido'
        ]);
    }

    #[OA\Patch(
        path: "/api/contatos/{contato}/favorito",
        summary: "Atualiza o status de favorito de um contato",
        parameters: [
            new OA\Parameter(
                name: "contato",
                in: "path",
                required: true,
                description: "ID do contato",
                schema: new OA\Schema(type: "integer")
            ), 
            new OA\Parameter(
                name: "favorito",
                in: "query",
                required: true,
                description: "Indica se o contato é favorito",
                schema: new OA\Schema(type: "boolean")
            )
        ],
        security: [["sanctum" => []]],
        tags: ["Contatos"],
    )]
    #[OA\Response(response: 200, description: "Status de favorito atualizado com sucesso")]
    public function favorito(Contato $contato)
    {

        if ($contato->user_id != auth()->id()) {
            abort(403);
        }

        $contato->favorito = !$contato->favorito;
        $contato->save();


        return response()->json([
            'message' => 'Status de favorito atualizado.',
            'favorito' => $contato->favorito
        ], 200);
    }
}
