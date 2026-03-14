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
// ROTAS PÚBLICAS
// ===============================
Route::prefix('auth')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    // enviar email de recuperação
    Route::post('/recover', [AuthController::class, 'recover']);

    // redefinir senha
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// ===============================
// ROTAS PROTEGIDAS (Autenticação via Sanctum)
// ===============================
Route::middleware('auth:sanctum')->group(function () {

    // --------------------------------
    //  Dados do utilizador autenticado
    // --------------------------------
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users/me', [UserController::class, 'me']);

    // ===============================
    //  ADMIN (RF-010)
    // ===============================
    Route::middleware(['role:ADMIN'])->prefix('admin')->group(function () {

        // criar CHEFE DE REPARTIÇÃO
        Route::apiResource('users', UserController::class)
            ->only(['index','store','update','destroy']);

        Route::post('users/{id}/ativar', [UserController::class, 'ativar']);
        Route::post('users/{id}/desativar', [UserController::class, 'desativar']);

        // cursos
        Route::post('cursos', [CursoController::class, 'store']);

        // auditoria
        Route::get('logs', [AdminController::class, 'logs']);
    });

    // ===============================
    //  CHEFE DE REPARTIÇÃO (RF-011)
    // ===============================
    Route::middleware(['role:CHEFE_REPARTICAO'])->prefix('chefe')->group(function () {

        // ======================
        // utilizadores
        // ======================

        // criar coordenador / estagiario / supervisor / tutor
        Route::post('users', [UserController::class, 'store']);

        // actualizar utilizador
        Route::put('users/{id}', [UserController::class, 'update']);

        // remover utilizador
        Route::delete('users/{id}', [UserController::class, 'destroy']);

        // ver utilizadores da instituição
        //Route::get('instituicao/usuarios', [UserController::class, 'usuariosInstituicao']);


        // ======================
        // instituições
        // ======================

        // criar instituição parceira
        Route::post('instituicoes', [InstituicaoController::class, 'store']);

        // listar instituições
        Route::get('instituicoes', [InstituicaoController::class, 'index']);

        // atualizar instituição
        Route::put('instituicoes/{id}', [InstituicaoController::class, 'update']);

        // remover instituição
        Route::delete('instituicoes/{id}', [InstituicaoController::class, 'destroy']);


        // ======================
        // estágios
        // ======================

        // visualizar estágios de uma instituição especifica
        Route::get('instituicoes/{id}/estagios', [EstagioController::class, 'estagiosInstituicao']);
        // visualizar estágios das instituições
        Route::get('chefe/estagios', [EstagioController::class, 'todosEstagios']);
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
        Route::post('estagios', [EstagioController::class, 'store']); // criar estágio
        Route::post('estagios/{id}/tutor', [EstagioController::class, 'atribuirTutor']);
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