<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContatoController;

Route::middleware('auth:sanctum')->group(function () {
   Route::apiResource('contatos', ContatoController::class);
});