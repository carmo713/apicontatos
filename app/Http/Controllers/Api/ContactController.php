<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class ContactController extends Controller
{
    #[OA\Get(
        path: "/api/contacts",
        summary: "Lista todos os Contacts",
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "search",
                in: "query",
                required: false,
                description: "Termo de pesquisa para filtrar Contacts pelo name",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "phone",
                in: "query",
                required: false,
                description: "Buscar pelo phone",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "favorite",
                in: "query",
                required: false,
                description: "Filtrar favorites",
                schema: new OA\Schema(type: "boolean")
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                description: "Número da página",
                schema: new OA\Schema(type: "integer", default: 1)
            ),

            new OA\Parameter(
                name: "per_page",
                in: "query",
                required: false,
                description: "Quantidade de contatos por página",
                schema: new OA\Schema(type: "integer", default: 10)
            ),

        ],
        tags: ["Contacts"],
    )]
    #[OA\Response(response: 200, description: "Lista de Contacts retornada com sucesso")]

    public function index(Request $request)
    {
        $query = auth()->user()->contacts();

        // Buscar por name
        $query->when($request->name, function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->name . '%');
        });

        // Buscar por phone
        $query->when($request->phone, function ($q) use ($request) {
            $q->where('phone', 'like', '%' . $request->phone . '%');
        });


        // Filtrar favorites
        $query->when($request->filled('favorite'), function ($q) use ($request) {
            $q->where('favorite', $request->boolean('favorite'));
        });

        $perPage = min($request->query('per_page', 10), 100);



        return $query->latest()->paginate($perPage);
    }
    #[OA\Post(
        path: "/api/contacts",
        summary: "Cria um novo Contact",
        parameters: [
            new OA\Parameter(
                name: "name",
                in: "query",
                required: true,
                description: "name do Contact",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "phone",
                in: "query",
                required: true,
                description: "phone do Contact",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "email",
                in: "query",
                required: true,
                description: "Email do Contact",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "favorite",
                in: "query",
                required: false,
                description: "Indica se o Contact é favorite",
                schema: new OA\Schema(type: "boolean")
            )
        ],
        security: [["sanctum" => []]],
        tags: ["Contacts"],
    )]
    #[OA\Response(response: 201, description: "Contact criado com sucesso")]

    public function store(Request $request)
    {
        $request->merge([
            'favorite' => $request->has('favorite')
                ? $request->boolean('favorite')
                : false,
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'nullable',
                'required_without:email',
                Rule::unique('contacts')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id);
                }),
            ],
            'email' => [
                'nullable',
                'email',
                'required_without:phone',
                Rule::unique('contacts')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id);
                }),
            ],
            'favorite' => 'boolean',
        ]);

        $contact = auth()->user()->contacts()->create($validated);

        return response()->json($contact, 201);
    }

    #[OA\Get(
        path: "/api/contacts/{Contact}",
        summary: "Exibe um Contact",
        parameters: [
            new OA\Parameter(
                name: "Contact",
                in: "path",
                required: true,
                description: "ID do Contact",
                schema: new OA\Schema(type: "integer")
            )
        ],
        security: [["sanctum" => []]],
        tags: ["Contacts"],
    )]
    #[OA\Response(response: 200, description: "Contact retornado com sucesso")]


    public function show(Contact $contact)
    {
        if (
            $contact->user_id
            != auth()->id()
        ) {
            abort(404);
        }

        return $contact;
    }


    #[OA\Put(
        path: "/api/contacts/{Contact}",
        summary: "Atualiza um Contact",
        parameters: [
            new OA\Parameter(
                name: "Contact",
                in: "path",
                required: true,
                description: "ID do Contact",
                schema: new OA\Schema(type: "integer")
            ),

            new OA\Parameter(
                name: "name",
                in: "query",
                required: false,
                description: "name do Contact",
                schema: new OA\Schema(type: "string")
            ),

            new OA\Parameter(
                name: "phone",
                in: "query",
                required: false,
                description: "phone do Contact",
                schema: new OA\Schema(type: "string")
            ),

            new OA\Parameter(
                name: "email",
                in: "query",
                required: false,
                description: "Email do Contact",
                schema: new OA\Schema(type: "string")
            ),

            new OA\Parameter(
                name: "favorite",
                in: "query",
                required: false,
                description: "Indica se o Contact é favorite",
                schema: new OA\Schema(type: "boolean")
            )
        ],
        security: [["sanctum" => []]],
        tags: ["Contacts"],
    )]
    #[OA\Response(response: 200, description: "Contact atualizado com sucesso")]

    public function update(Request $request, Contact $contact)
    {

        $request->merge([
            'favorite' => $request->has('favorite')
                ? $request->boolean('favorite')
                : false,
        ]);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'phone' => [
                'nullable',
                'required_without:email',
                Rule::unique('contacts')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id);
                }),
            ],
            'email' => [
                'nullable',
                'email',
                'required_without:phone',
                Rule::unique('contacts')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id);
                }),
            ],
            'favorite' => 'boolean',
        ]);

        if ($contact->user_id != auth()->id()) {
            abort(404);
        }

        $contact->update(
            $validated
        );

        return $contact;
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: "/api/contacts/{Contact}",
        summary: "Remove um Contact",
        parameters: [
            new OA\Parameter(
                name: "Contact",
                in: "path",
                required: true,
                description: "ID do Contact",
                schema: new OA\Schema(type: "integer")
            )
        ],
        security: [["sanctum" => []]],
        tags: ["Contacts"],
    )]
    #[OA\Response(response: 204, description: "Contact removido com sucesso")]
    public function destroy(Contact $contact)
    {
        if ($contact->user_id != auth()->id()) {
            abort(404);
        }

        $contact->delete();

        return response()->noContent();
    }

    #[OA\Patch(
        path: "/api/contacts/{Contact}/favorite",
        summary: "Atualiza o status de favorite de um Contact",
        parameters: [
            new OA\Parameter(
                name: "Contact",
                in: "path",
                required: true,
                description: "ID do Contact",
                schema: new OA\Schema(type: "integer")
            ),
        ],
        security: [["sanctum" => []]],
        tags: ["Contacts"],
    )]
    #[OA\Response(response: 200, description: "Status de favorite atualizado com sucesso")]
    public function favorite(Contact $contact)
    {
        if ($contact->user_id != auth()->id()) {
            abort(404);
        }

        $contact->favorite = !$contact->favorite;
        $contact->save();

        return response()->json([
            'message' => 'Status de favorite atualizado.',
            'favorite' => $contact->favorite
        ], 200);
    }
}
