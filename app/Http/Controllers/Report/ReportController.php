<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Estagio;
use App\Models\StudentProfile;
use App\Models\StaffProfile;
use App\Models\Documento;
use App\Models\Instituicao;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Estatísticas de estágios
     */
    public function internshipStatistics(Request $request)
    {
        $query = Estagio::query();

        if ($request->has('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }

        if ($request->has('ano_academico')) {
            $query->whereYear('data_inicio', $request->ano_academico);
        }

        $total = $query->count();
        $emAndamento = (clone $query)->where('estado', 'em_andamento')->count();
        $concluidos = (clone $query)->where('estado', 'concluido')->count();
        $cancelados = (clone $query)->where('estado', 'cancelado')->count();

        $mediaNotas = (clone $query)
            ->whereNotNull('nota_final')
            ->avg('nota_final');

        return response()->json([
            'total_internships' => $total,
            'em_andamento' => $emAndamento,
            'concluidos' => $concluidos,
            'cancelados' => $cancelados,
            'taxa_conclusao' => $total > 0 ? round(($concluidos / $total) * 100, 2) : 0,
            'media_notas' => $mediaNotas ? round($mediaNotas, 2) : null
        ]);
    }

    /**
     * Taxa de conclusão por curso
     */
    public function completionRates(Request $request)
    {
        $cursos = \App\Models\Curso::with('estagios')->get();

        $rates = $cursos->map(function ($curso) {
            $total = $curso->estagios()->count();
            $concluidos = $curso->estagios()->where('estado', 'concluido')->count();

            return [
                'curso' => $curso->nome,
                'total' => $total,
                'concluidos' => $concluidos,
                'taxa' => $total > 0 ? round(($concluidos / $total) * 100, 2) : 0
            ];
        });

        return response()->json($rates);
    }

    /**
     * Documentos pendentes
     */
    public function pendingDocuments(Request $request)
    {
        $documentos = Documento::where('estado', 'pendente_revisao')
            ->with('estagio.estagiario', 'estagio.curso', 'estagio.instituicao')
            ->orderBy('submetido_em', 'asc')
            ->paginate(20);

        $total = Documento::where('estado', 'pendente_revisao')->count();
        $rejeitados = Documento::where('estado', 'rejeitado')->count();

        return response()->json([
            'total_pendentes' => $total,
            'total_rejeitados' => $rejeitados,
            'documentos' => $documentos
        ]);
    }

    /**
     * Carga de trabalho dos supervisores
     */
    public function supervisorLoads()
    {
        $supervisores = StaffProfile::whereHas('user', function ($q) {
            $q->whereHas('roles', function ($r) {
                $r->where('name', 'supervisor');
            });
        })
            ->with('user')
            ->get();

        $loads = $supervisores->map(function ($supervisor) {
            $estagios = $supervisor->user->estagiosComoSupervisor()
                ->whereNotIn('estado', ['concluido', 'cancelado'])
                ->count();

            return [
                'supervisor_id' => $supervisor->user->id,
                'nome' => $supervisor->user->name,
                'departamento' => $supervisor->departamento->nome ?? 'N/A',
                'estagios_ativos' => $estagios,
                'capacidade' => 5,
                'disponivel' => $estagios < 5
            ];
        });

        return response()->json($loads);
    }

    /**
     * Relatórios por instituição
     */
    public function institutionReports(Request $request)
    {
        $instituicoes = Instituicao::with('estagios')->get();

        $reports = $instituicoes->map(function ($instituicao) {
            $estagios = $instituicao->estagios;
            $concluidos = $estagios->where('estado', 'concluido')->count();
            $emAndamento = $estagios->where('estado', 'em_andamento')->count();

            return [
                'instituicao_id' => $instituicao->id,
                'nome' => $instituicao->nome,
                'nuit' => $instituicao->nuit,
                'status' => $instituicao->status,
                'total_estagios' => $estagios->count(),
                'estagios_concluidos' => $concluidos,
                'estagios_em_andamento' => $emAndamento,
                'estagios_cancelados' => $estagios->where('estado', 'cancelado')->count(),
                'media_notas' => $estagios->whereNotNull('nota_final')->avg('nota_final')
                    ? round($estagios->whereNotNull('nota_final')->avg('nota_final'), 2)
                    : null
            ];
        });

        return response()->json($reports);
    }
}
