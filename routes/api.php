<?php

use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EstagioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvaliacaoController;
use Illuminate\Support\Facades\Route;

// 🔑 Rotas públicas de autenticação
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/recover', [AuthController::class, 'recover']);
Route::middleware('auth:sanctum')->get('/users/me', [AuthController::class, 'me']);

// 🔐 Rotas protegidas com Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // 👤 Rotas de utilizador
    //Route::get('/users/me', [UserController::class, 'me']);
    Route::post('/users', [UserController::class, 'store'])->middleware('role:COORDENADOR'); // só coordenador cria
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware('role:COORDENADOR');

    // 📝 Rotas de estágios
    Route::get('/estagios', [EstagioController::class, 'index'])->middleware('role:COORDENADOR, SUPERVISOR'); // todos podem ver
    Route::post('/estagios/distribuir', [EstagioController::class, 'distribuir'])->middleware('role:COORDENADOR');

    // 🔐 Supervisor aprova/rejeita documentos
    Route::middleware('role:SUPERVISOR')->group(function () {
        Route::post('/documentos', [DocumentoController::class, 'store']); // criar documento
        Route::put('/documentos/{id}/aprovar', [DocumentoController::class, 'aprovar']);
        Route::put('/documentos/{id}/rejeitar', [DocumentoController::class, 'rejeitar']);

        Route::get('/meus-estagios', [EstagioController::class, 'meusEstagios']);
    });

    // 📝 Avaliações
    Route::post('/avaliacoes', [AvaliacaoController::class, 'store']);
});