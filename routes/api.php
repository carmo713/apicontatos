<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContatoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExportController;

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register'])->name('register');
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');


Route::middleware('auth:sanctum')->group(function () {
   Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
   Route::apiResource('contatos', ContatoController::class);
   Route::patch('contatos/{contato}/favorito', [ContatoController::class, 'favorito']);
   Route::get('/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'index']);
   Route::post(
      '/exports',
      [ExportController::class, 'store']
   );

   Route::get(
      '/exports',
      [ExportController::class, 'index']
   );

   Route::get(
      '/exports/{id}',
      [ExportController::class, 'show']
   );

   Route::get(
      '/exports/{id}/download',
      [ExportController::class, 'download']
   );
});
