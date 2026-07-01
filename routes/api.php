<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContatoController;


Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register'])->name('register');
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');


Route::middleware('auth:sanctum')->group(function () {
   Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
   Route::apiResource('contatos', ContatoController::class);
   Route::patch('contatos/{contato}/favorito', [ContatoController::class, 'favorito']);
});