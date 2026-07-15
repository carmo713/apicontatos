<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use App\Models\User;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/login",
        summary: "Faz login do usuário",
        parameters: [
            new OA\RequestBody(
                required: true,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "email", type: "string", example: "user@example.com"),
                        new OA\Property(property: "password", type: "string", example: "password"),
                    ]
                )
            )
        ],
        tags: ["Auth"],
    )]
    #[OA\Response(response: 200, description: "Login realizado com sucesso")]

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    #[OA\Post(
        path: "/api/register",
        summary: "Registra um novo usuário",
        parameters: [
            new OA\Parameter(
                name: "name",
                in: "query",
                required: true,
                description: "name do usuário",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "email",
                in: "query",
                required: true,
                description: "Email do usuário",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "password",
                in: "query",
                required: true,
                description: "Senha do usuário",
                schema: new OA\Schema(type: "string")
            ),
        ],
        security: [["sanctum" => []]],
        tags: ["Auth"],
    )]
    #[OA\Response(response: 201, description: "Usuário registrado com sucesso")]

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Realiza logout do usuário autenticado",
        security: [["sanctum" => []]],
        tags: ["Auth"],
    )]
    #[OA\Response(response: 200, description: "Logout realizado com sucesso")]
    #[OA\Response(response: 401, description: "Usuário não autenticado")]

    public function logout(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }
}
