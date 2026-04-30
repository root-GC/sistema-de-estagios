<?php

namespace App\Http\Controllers\Internship;

use App\Http\Controllers\Controller;
use App\Models\Estagio;
use Illuminate\Http\Request;

class PautaController extends Controller
{
    /**
     * Listar pautas
     */
    public function index(Request $request)
    {
        $query = Estagio::with('estagiario', 'curso')
            ->whereNotNull('nota_final');

        if ($request->has('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        $pautas = $query->paginate(20);
        return response()->json($pautas);
    }

    /**
     * Ver pauta específica
     */
    public function show(Request $request)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'ano_academico' => 'nullable|string'
        ]);

        $estagios = Estagio::where('curso_id', $validated['curso_id'])
            ->with('estagiario', 'curso')
            ->whereNotNull('nota_final')
            ->get();

        return response()->json([
            'curso_id' => $validated['curso_id'],
            'ano_academico' => $validated['ano_academico'] ?? null,
            'estagios' => $estagios
        ]);
    }

    /**
     * Gerar pauta
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'ano_academico' => 'nullable|string'
        ]);

        $estagios = Estagio::where('curso_id', $validated['curso_id'])
            ->with('estagiario', 'curso')
            ->where('estado', 'concluido')
            ->whereNotNull('nota_final')
            ->orderBy('nota_final', 'desc')
            ->get();

        return response()->json([
            'message' => 'Grade sheet generated',
            'curso_id' => $validated['curso_id'],
            'ano_academico' => $validated['ano_academico'] ?? null,
            'total_estudantes' => $estagios->count(),
            'media' => $estagios->avg('nota_final'),
            'estagios' => $estagios
        ]);
    }

    /**
     * Exportar para SIGEUP
     */
    public function exportSigeup(Request $request)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id'
        ]);

        $estagios = Estagio::where('curso_id', $validated['curso_id'])
            ->with('estagiario', 'curso')
            ->where('estado', 'concluido')
            ->whereNotNull('nota_final')
            ->get();

        // TODO: Implementar formato de exportação SIGEUP
        $csv = "numero_estudante,nome,nota_final\n";

        foreach ($estagios as $estagio) {
            $csv .= $estagio->estagiario->studentProfile->numero_estudante . ",";
            $csv .= $estagio->estagiario->name . ",";
            $csv .= $estagio->nota_final . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pauta_sigeup_'.now()->format('Y-m-d_His').'.csv"'
        ]);
    }

    /**
     * Publicar pauta
     */
    public function publish(Request $request)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id'
        ]);

        // TODO: Implementar lógica de publicação (notificações aos estudantes)
        return response()->json([
            'message' => 'Grade sheet published',
            'curso_id' => $validated['curso_id']
        ]);
    }
}
