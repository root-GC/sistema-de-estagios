<?php

use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EstagioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\InstituicaoController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// ===============================
// ROTAS PÚBLICAS (Sem autenticação)
// ===============================
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/recover', [AuthController::class, 'recover']);
});

// ===============================
// ROTAS PROTEGIDAS (Autenticação via Sanctum)
// ===============================
Route::middleware('auth:sanctum')->group(function () {

    // --------------------------------
    //  Dados do utilizador autenticado
    // --------------------------------
    Route::get('/users/me', [UserController::class, 'me']);

    // ===============================
    //  ADMIN (RF-010)
    // ===============================
    Route::middleware('role:ADMIN')->prefix('admin')->group(function () {

        // Utilizadores
        Route::apiResource('users', UserController::class)->only(['store','update','destroy']);
        Route::post('users/{id}/ativar', [UserController::class, 'ativar']);
        Route::post('users/{id}/desativar', [UserController::class, 'desativar']);

        // Cursos e Instituições
        Route::post('cursos', [CursoController::class, 'store']);
        Route::post('instituicoes', [InstituicaoController::class, 'store']);

        // Logs / auditoria
        Route::get('logs', [AdminController::class, 'logs']);
    });

    // ===============================
    //  CHEFE DE REPARTIÇÃO (RF-011)
    // ===============================
    Route::middleware('role:CHEFE')->prefix('chefe')->group(function () {
        Route::get('instituicao/estagios', [EstagioController::class, 'estagiosInstituicao']);
        Route::get('instituicao/usuarios', [UserController::class, 'usuariosInstituicao']);
    });

    // ===============================
    //  COORDENADOR (RF-020)
    // ===============================
    Route::middleware('role:COORDENADOR')->prefix('coordenador')->group(function () {

        // Utilizadores
        Route::post('users', [UserController::class, 'store']);
        Route::put('users/{id}', [UserController::class, 'update']);

        // Estágios
        Route::get('estagios', [EstagioController::class, 'index']);
        Route::post('estagios/distribuir', [EstagioController::class, 'distribuir']);
        Route::get('estagios/exportar-notas', [EstagioController::class, 'exportarNotas']);
    });

    // ===============================
    //  ESTAGIÁRIO (RF-017)
    // ===============================
    Route::middleware('role:ESTAGIARIO')->prefix('estagiario')->group(function () {
        Route::get('meu-estagio', [EstagioController::class, 'meuEstagio']);
        Route::post('documentos', [DocumentoController::class, 'store']);
        Route::get('meus-documentos', [DocumentoController::class, 'meusDocumentos']);
    });

    // ===============================
    //  SUPERVISOR (RF-018)
    // ===============================
    Route::middleware('role:SUPERVISOR')->prefix('supervisor')->group(function () {
        Route::get('meus-estagios', [EstagioController::class, 'meusEstagiosSupervisor']);
        Route::put('documentos/{id}/aprovar', [DocumentoController::class, 'aprovar']);
        Route::put('documentos/{id}/rejeitar', [DocumentoController::class, 'rejeitar']);
        Route::post('estagios/{id}/nota-final', [EstagioController::class, 'registarNotaFinal']);
    });

    // ===============================
    //  TUTOR (RF-019)
    // ===============================
    Route::middleware('role:TUTOR')->prefix('tutor')->group(function () {
        Route::get('meus-estagios', [EstagioController::class, 'meusEstagiosTutor']);
        Route::post('avaliacoes', [AvaliacaoController::class, 'store']);
    });
});