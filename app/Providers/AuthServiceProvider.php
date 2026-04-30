<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Departamento;
use App\Models\Curso;
use App\Models\Instituicao;
use App\Models\StudentProfile;
use App\Models\StaffProfile;
use App\Models\Estagio;
use App\Models\Documento;
use App\Models\Avaliacao;

use App\Policies\UserPolicy;
use App\Policies\DepartamentoPolicy;
use App\Policies\CursoPolicy;
use App\Policies\InstituicaoPolicy;
use App\Policies\StudentProfilePolicy;
use App\Policies\StaffProfilePolicy;
use App\Policies\EstagioPolicy;
use App\Policies\DocumentoPolicy;
use App\Policies\AvaliacaoPolicy;
use App\Policies\PautaPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Departamento::class => DepartamentoPolicy::class,
        Curso::class => CursoPolicy::class,
        Instituicao::class => InstituicaoPolicy::class,
        StudentProfile::class => StudentProfilePolicy::class,
        StaffProfile::class => StaffProfilePolicy::class,
        Estagio::class => EstagioPolicy::class,
        Documento::class => DocumentoPolicy::class,
        Avaliacao::class => AvaliacaoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gates customizados podem ser adicionados aqui se necessário
        Gate::define('view-pauta', function (User $user) {
            return $user->hasAnyRole(['admin', 'coordenador']);
        });

        Gate::define('publish-pauta', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('view-audit-logs', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-roles', function (User $user) {
            return $user->hasRole('admin');
        });
    }
}
