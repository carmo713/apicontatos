<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

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
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
           return response()->json('Authorized', 200, [
           'token' => $request->user()->createToken('auth_token')->plainTextToken,  
            ]);
        }
        return response()->json('Not Authorized', 401);
    }

    #[OA\Post(
    path: "/api/register",
    summary: "Registra um novo usuário",
    parameters: [
       new OA\Parameter(
            name: "name",
            in: "query",
            required: true,
            description: "Nome do usuário",
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json('User registered', 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json('Logged out', 200);
    }

    
}
