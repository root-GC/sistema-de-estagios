<?php

use Illuminate\Support\Facades\Route;

// ===============================
// CONTROLLERS - NEW STRUCTURE
// ===============================
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Academic\DepartamentoController;
use App\Http\Controllers\Academic\CursoController;
use App\Http\Controllers\Academic\InstituicaoController;
use App\Http\Controllers\Profile\StudentProfileController;
use App\Http\Controllers\Profile\StaffProfileController;
use App\Http\Controllers\Internship\EstagioController;
use App\Http\Controllers\Internship\DocumentoController;
use App\Http\Controllers\Internship\AvaliacaoController;
use App\Http\Controllers\Internship\PautaController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Report\ReportController;

// ===============================
// ROTAS PÚBLICAS
// ===============================
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// ===============================
// ROTAS PROTEGIDAS (Autenticação via Sanctum)
// ===============================
Route::middleware('auth:sanctum')->group(function () {

    // ===============================
    // AUTH - Utilizador Autenticado
    // ===============================
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // ===============================
    // ADMIN - Gestão de Utilizadores
    // ===============================
    Route::middleware('role:admin')->prefix('admin')->group(function () {

        // Utilizadores
        Route::apiResource('users', AdminUserController::class);
        Route::post('users/{user}/activate', [AdminUserController::class, 'activate']);
        Route::post('users/{user}/deactivate', [AdminUserController::class, 'deactivate']);
        Route::post('users/{user}/assign-role', [AdminUserController::class, 'assignRole']);
        Route::post('users/{user}/remove-role', [AdminUserController::class, 'removeRole']);
        Route::post('users/{user}/reset-password', [AdminUserController::class, 'resetPassword']);
        Route::get('users/{user}/logs', [AdminUserController::class, 'logs']);

        // Papéis (RBAC)
        Route::apiResource('roles', RoleController::class);
        Route::post('roles/{role}/assign-permission', [RoleController::class, 'assignPermission']);
        Route::post('roles/{role}/remove-permission', [RoleController::class, 'removePermission']);

        // Auditoria
        Route::get('audit-logs', [AuditLogController::class, 'index']);
        Route::get('audit-logs/{log}', [AuditLogController::class, 'show']);

        // Notificações
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::get('notifications/{notificacao}', [NotificationController::class, 'show']);
        Route::post('notifications/{notificacao}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('notifications/{notificacao}', [NotificationController::class, 'destroy']);
    });

    // ===============================
    // ACADÉMICO - Departamentos, Cursos, Instituições
    // ===============================
    Route::middleware('role:admin|coordenador|chefe_repartição|tutor')->prefix('academic')->group(function () {

        // Departamentos
        Route::apiResource('departamentos', DepartamentoController::class)->only(['index', 'show']);

        // Cursos
        Route::apiResource('cursos', CursoController::class);
        Route::get('cursos/{curso}/coordenadores', [CursoController::class, 'coordenadores']);
        Route::get('cursos/{curso}/estudantes', [CursoController::class, 'estudantes']);

        // Instituições
        Route::apiResource('instituicoes', InstituicaoController::class);
        Route::post('instituicoes/{instituicao}/approve', [InstituicaoController::class, 'approve']);
        Route::post('instituicoes/{instituicao}/reject', [InstituicaoController::class, 'reject']);
        Route::post('instituicoes/{instituicao}/suspend', [InstituicaoController::class, 'suspend']);
        Route::get('instituicoes/{instituicao}/estagios', [InstituicaoController::class, 'estagios']);
    });


    Route::middleware('role:chefe_repartição')->prefix('chefe')->group(function (){
        // Route::get('users',[AdminUserController::class, 'index' ]);
        Route::apiResource('users', AdminUserController::class);
        Route::get('estagios', [EstagioController::class, 'index']);
        // Route::get('instituicoes', [InstituicaoController::class, 'index']);
        Route::apiResource('instituicoes', InstituicaoController::class);

        // Route::post('instituicoes', [InstituicaoController::class, 'store']);
    });

    Route::middleware('role:coordenador')->prefix('coordenador')->group(function (){
        // Route::apiResource('tutores', TutoresController::class);
        Route::apiResource('estagios', EstagioController::class);
        Route::apiResource('tutores', TutoresController::class);
        Route::apiResource('users', AdminUserController::class);

    });

    // Apenas admin e coordenador podem criar/editar
    Route::middleware('role:admin|coordenador')->prefix('academic')->group(function () {
        Route::post('departamentos', [DepartamentoController::class, 'store']);
        Route::put('departamentos/{departamento}', [DepartamentoController::class, 'update']);
        Route::delete('departamentos/{departamento}', [DepartamentoController::class, 'destroy']);
    });

    // ===============================
    // PERFIS - Student e Staff
    // ===============================
    Route::middleware('role:admin|coordenador|supervisor')->prefix('profiles')->group(function () {

        // Perfis de Estudante
        Route::apiResource('student-profiles', StudentProfileController::class);
        Route::get('student-profiles/{studentProfile}/check-eligibility', [StudentProfileController::class, 'checkEligibility']);

        // Perfis de Staff
        Route::apiResource('staff-profiles', StaffProfileController::class);
    });

    // ===============================
    // ESTÁGIOS - Core do Sistema
    // ===============================
    Route::middleware('role:admin|coordenador|supervisor|tutor|estudante')->prefix('internships')->group(function () {

        // Estágios (Leitura para todos os papéis)
        Route::get('estagios', [EstagioController::class, 'index']);
        Route::get('estagios/{estagio}', [EstagioController::class, 'show']);

        // Operações de escrita (apenas admin, coordenador, supervisor)
        Route::middleware('role:admin|coordenador|supervisor')->group(function () {
            Route::post('estagios', [EstagioController::class, 'store']);
            Route::put('estagios/{estagio}', [EstagioController::class, 'update']);
            Route::post('estagios/{estagio}/allocate-supervisor', [EstagioController::class, 'allocateSupervisor']);
            Route::post('estagios/{estagio}/assign-tutor', [EstagioController::class, 'assignTutor']);
            Route::post('estagios/{estagio}/change-status', [EstagioController::class, 'changeStatus']);
            Route::post('estagios/{estagio}/generate-credential-letter', [EstagioController::class, 'generateCredentialLetter']);
            Route::post('estagios/{estagio}/calculate-final-grade', [EstagioController::class, 'calculateFinalGrade']);
            Route::post('estagios/{estagio}/close-internship', [EstagioController::class, 'closeInternship']);
        });

        // Documentos
        Route::get('documentos', [DocumentoController::class, 'index']);
        Route::get('documentos/{documento}', [DocumentoController::class, 'show']);
        Route::post('documentos/upload', [DocumentoController::class, 'upload']);
        Route::get('documentos/{documento}/download', [DocumentoController::class, 'download']);

        // Documentos - Aprovação (apenas supervisor)
        Route::middleware('role:supervisor')->group(function () {
            Route::post('documentos/{documento}/approve', [DocumentoController::class, 'approve']);
            Route::post('documentos/{documento}/reject', [DocumentoController::class, 'reject']);
            Route::post('documentos/{documento}/comment', [DocumentoController::class, 'comment']);
        });

        // Documentos - Eliminação (apenas admin e coordenador)
        Route::middleware('role:admin|coordenador')->delete('documentos/{documento}', [DocumentoController::class, 'destroy']);

        // Avaliações
        Route::get('avaliacoes', [AvaliacaoController::class, 'index']);
        Route::get('avaliacoes/{avaliacao}', [AvaliacaoController::class, 'show']);

        // Avaliações - Submissão (tutor e supervisor)
        Route::middleware('role:tutor|supervisor')->group(function () {
            Route::post('avaliacoes', [AvaliacaoController::class, 'store']);
            Route::put('avaliacoes/{avaliacao}', [AvaliacaoController::class, 'update']);
            Route::post('avaliacoes/submit-tutor-evaluation', [AvaliacaoController::class, 'submitTutorEvaluation']);
            Route::post('avaliacoes/submit-supervisor-evaluation', [AvaliacaoController::class, 'submitSupervisorEvaluation']);
        });

        // Avaliações - Cálculo (admin e coordenador)
        Route::middleware('role:admin|coordenador')->post('avaliacoes/calculate-average', [AvaliacaoController::class, 'calculateAverage']);

        // Pautas
        Route::middleware('role:admin|coordenador')->group(function () {
            Route::get('pautas', [PautaController::class, 'index']);
            Route::post('pautas/show', [PautaController::class, 'show']);
            Route::post('pautas/generate', [PautaController::class, 'generate']);
            Route::post('pautas/export-sigeup', [PautaController::class, 'exportSigeup']);
            Route::post('pautas/publish', [PautaController::class, 'publish']);
        });
    });

    // ===============================
    // DASHBOARDS - Role-Specific
    // ===============================
    Route::prefix('dashboard')->group(function () {
        Route::middleware('role:admin')->get('/admin', [DashboardController::class, 'adminDashboard']);
        Route::middleware('role:coordenador')->get('/coordenador', [DashboardController::class, 'coordenadorDashboard']);
        Route::middleware('role:supervisor')->get('/supervisor', [DashboardController::class, 'supervisorDashboard']);
        Route::middleware('role:tutor')->get('/tutor', [DashboardController::class, 'tutorDashboard']);
        Route::middleware('role:estudante')->get('/student', [DashboardController::class, 'estudanteDashboard']);
    });

    // ===============================
    // RELATÓRIOS
    // ===============================
    Route::middleware('role:admin|coordenador|supervisor')->prefix('reports')->group(function () {
        Route::get('/internship-statistics', [ReportController::class, 'internshipStatistics']);
        Route::get('/completion-rates', [ReportController::class, 'completionRates']);
        Route::get('/pending-documents', [ReportController::class, 'pendingDocuments']);
        Route::get('/supervisor-loads', [ReportController::class, 'supervisorLoads']);
        Route::get('/institution-reports', [ReportController::class, 'institutionReports']);
    });
});