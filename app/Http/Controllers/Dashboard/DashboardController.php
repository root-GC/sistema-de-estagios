<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Estagio;
use App\Models\StudentProfile;
use App\Models\StaffProfile;
use App\Models\Documento;
use App\Models\Avaliacao;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard do administrador
     */
    public function adminDashboard()
    {
        $totalUsers = \App\Models\User::count();
        $totalInternships = Estagio::count();
        $activeInternships = Estagio::where('estado', 'em_andamento')->count();
        $totalInstitutions = \App\Models\Instituicao::count();
        $pendingDocuments = Documento::where('estado', 'pendente_revisao')->count();

        return response()->json([
            'total_users' => $totalUsers,
            'total_internships' => $totalInternships,
            'active_internships' => $activeInternships,
            'completed_internships' => Estagio::where('estado', 'concluido')->count(),
            'total_institutions' => $totalInstitutions,
            'pending_documents' => $pendingDocuments,
            'average_grade' => Estagio::whereNotNull('nota_final')->avg('nota_final'),
            'recent_internships' => Estagio::latest()->take(5)->with('estagiario', 'curso')->get()
        ]);
    }

    

    /**
     * Dashboard do coordenador
     */
    public function coordenadorDashboard()
    {
        $user = auth()->user();
        $staffProfile = $user->staffProfile;

        $cursos = \App\Models\Curso::where('departamento_id', $staffProfile->departamento_id)->get();
        $cursosIds = $cursos->pluck('id');

        $totalEstudantes = StudentProfile::whereIn('curso_id', $cursosIds)->count();
        $estagiosEmAndamento = Estagio::whereIn('curso_id', $cursosIds)
            ->where('estado', 'em_andamento')
            ->count();
        $estagiosConcluidos = Estagio::whereIn('curso_id', $cursosIds)
            ->where('estado', 'concluido')
            ->count();
        $documentosPendentes = Documento::whereHas('estagio', function ($q) use ($cursosIds) {
            $q->whereIn('curso_id', $cursosIds);
        })
            ->where('estado', 'pendente_revisao')
            ->count();

        return response()->json([
            'departamento' => $staffProfile->departamento,
            'total_cursos' => $cursos->count(),
            'total_estudantes' => $totalEstudantes,
            'estagios_em_andamento' => $estagiosEmAndamento,
            'estagios_concluidos' => $estagiosConcluidos,
            'documentos_pendentes' => $documentosPendentes,
            'cursos' => $cursos
        ]);
    }

    /**
     * Dashboard do supervisor
     */
    public function supervisorDashboard()
    {
        $user = auth()->user();

        $estagiosAtivos = $user->estagiosComoSupervisor()
            ->where('estado', 'em_andamento')
            ->with('estagiario', 'instituicao', 'curso')
            ->get();

        $estagiosConcluidos = $user->estagiosComoSupervisor()
            ->where('estado', 'concluido')
            ->count();

        $documentosPendentes = Documento::whereIn('estagio_id', $user->estagiosComoSupervisor()->pluck('id'))
            ->where('estado', 'pendente_revisao')
            ->count();

        $avaliacoesPendentes = $user->estagiosComoSupervisor()
            ->where('estado', 'em_andamento')
            ->whereDoesntHave('avaliacoes', function ($q) {
                $q->where('tipo', 'supervisor')->where('avaliador_id', auth()->id());
            })
            ->count();

        return response()->json([
            'estagios_ativos' => $estagiosAtivos->count(),
            'estagios_concluidos' => $estagiosConcluidos,
            'documentos_pendentes' => $documentosPendentes,
            'avaliacoes_pendentes' => $avaliacoesPendentes,
            'estagios' => $estagiosAtivos
        ]);
    }

    /**
     * Dashboard do tutor
     */
    public function tutorDashboard()
    {
        $user = auth()->user();

        $estagiosAtivos = $user->estagiosComoTutor()
            ->where('estado', 'em_andamento')
            ->with('estagiario', 'instituicao', 'curso')
            ->get();

        $avaliacoesPendentes = $user->estagiosComoTutor()
            ->where('estado', 'em_andamento')
            ->whereDoesntHave('avaliacoes', function ($q) {
                $q->where('tipo', 'tutor')->where('avaliador_id', auth()->id());
            })
            ->count();

        return response()->json([
            'estagios_ativos' => $estagiosAtivos->count(),
            'avaliacoes_pendentes' => $avaliacoesPendentes,
            'estagios' => $estagiosAtivos
        ]);
    }

    /**
     * Dashboard do estudante
     */
    public function estudanteDashboard()
    {
        $user = auth()->user();
        $studentProfile = $user->studentProfile;

        if (!$studentProfile) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        $estagioAtivo = $user->estagiosComoEstagiario()
            ->where('estado', 'em_andamento')
            ->with('instituicao', 'supervisor', 'tutor', 'coordenador', 'documentos', 'avaliacoes')
            ->first();

        $documentosPendentes = $user->estagiosComoEstagiario()
            ->where('estado', 'em_andamento')
            ->with('documentos')
            ->get()
            ->flatMap(function ($estagio) {
                return $estagio->documentos->where('estado', '!=', 'aprovado');
            })
            ->count();

        $avaliacoes = $estagioAtivo ? $estagioAtivo->avaliacoes : collect();

        return response()->json([
            'student_profile' => $studentProfile,
            'elegivel_estagio' => $studentProfile->elegivel_estagio,
            'estagio_ativo' => $estagioAtivo,
            'documentos_pendentes' => $documentosPendentes,
            'avaliacoes' => $avaliacoes,
            'estagios_passados' => $user->estagiosComoEstagiario()
                ->where('estado', 'concluido')
                ->with('instituicao', 'curso')
                ->orderBy('data_fim', 'desc')
                ->get()
        ]);
    }
}
