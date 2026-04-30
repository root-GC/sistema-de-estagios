<?php

namespace App\Http\Controllers\Internship;

use App\Http\Controllers\Controller;
use App\Models\Avaliacao;
use Illuminate\Http\Request;

class AvaliacaoController extends Controller
{
    /**
     * Listar avaliações
     */
    public function index(Request $request)
    {
        $query = Avaliacao::with('estagio', 'avaliador');

        if ($request->has('estagio_id')) {
            $query->where('estagio_id', $request->estagio_id);
        }

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $avaliacoes = $query->paginate(15);
        return response()->json($avaliacoes);
    }

    /**
     * Ver avaliação específica
     */
    public function show(Avaliacao $avaliacao)
    {
        return response()->json(
            $avaliacao->load('estagio', 'avaliador')
        );
    }

    /**
     * Criar avaliação
     */
    public function store(Request $request)
    {
        $this->authorize('create', Avaliacao::class);

        $validated = $request->validate([
            'estagio_id' => 'required|exists:estagios,id',
            'avaliador_id' => 'required|exists:users,id',
            'tipo' => 'required|in:tutor,supervisor,institucional',
            'nota' => 'required|numeric|min:0|max:20',
            'comentario' => 'nullable|string'
        ]);

        $avaliacao = Avaliacao::create($validated);
        return response()->json($avaliacao, 201);
    }

    /**
     * Atualizar avaliação
     */
    public function update(Request $request, Avaliacao $avaliacao)
    {
        $this->authorize('update', $avaliacao);

        $validated = $request->validate([
            'nota' => 'numeric|min:0|max:20',
            'comentario' => 'nullable|string'
        ]);

        $avaliacao->update($validated);
        return response()->json($avaliacao);
    }

    /**
     * Submeter avaliação do tutor
     */
    public function submitTutorEvaluation(Request $request)
    {
        $this->authorize('submitTutorEvaluation', Avaliacao::class);

        $validated = $request->validate([
            'estagio_id' => 'required|exists:estagios,id',
            'nota' => 'required|numeric|min:0|max:20',
            'comentario' => 'nullable|string'
        ]);

        $estagio = \App\Models\Estagio::findOrFail($validated['estagio_id']);

        // Verificar se o utilizador é o tutor
        if ($estagio->tutor_id !== auth()->id()) {
            return response()->json(
                ['message' => 'Unauthorized'],
                403
            );
        }

        $avaliacao = Avaliacao::firstOrCreate(
            ['estagio_id' => $estagio->id, 'tipo' => 'tutor'],
            [
                'avaliador_id' => auth()->id(),
                'nota' => $validated['nota'],
                'comentario' => $validated['comentario'] ?? null
            ]
        );

        $avaliacao->update([
            'nota' => $validated['nota'],
            'comentario' => $validated['comentario'] ?? null
        ]);

        return response()->json(['message' => 'Tutor evaluation submitted']);
    }

    /**
     * Submeter avaliação do supervisor
     */
    public function submitSupervisorEvaluation(Request $request)
    {
        $this->authorize('submitSupervisorEvaluation', Avaliacao::class);

        $validated = $request->validate([
            'estagio_id' => 'required|exists:estagios,id',
            'nota' => 'required|numeric|min:0|max:20',
            'comentario' => 'nullable|string'
        ]);

        $estagio = \App\Models\Estagio::findOrFail($validated['estagio_id']);

        // Verificar se o utilizador é o supervisor
        if ($estagio->supervisor_id !== auth()->id()) {
            return response()->json(
                ['message' => 'Unauthorized'],
                403
            );
        }

        $avaliacao = Avaliacao::firstOrCreate(
            ['estagio_id' => $estagio->id, 'tipo' => 'supervisor'],
            [
                'avaliador_id' => auth()->id(),
                'nota' => $validated['nota'],
                'comentario' => $validated['comentario'] ?? null
            ]
        );

        $avaliacao->update([
            'nota' => $validated['nota'],
            'comentario' => $validated['comentario'] ?? null
        ]);

        return response()->json(['message' => 'Supervisor evaluation submitted']);
    }

    /**
     * Calcular média de avaliações
     */
    public function calculateAverage(Request $request)
    {
        $validated = $request->validate([
            'estagio_id' => 'required|exists:estagios,id'
        ]);

        $estagio = \App\Models\Estagio::findOrFail($validated['estagio_id']);
        $avaliacoes = $estagio->avaliacoes()->get();

        if ($avaliacoes->isEmpty()) {
            return response()->json([
                'message' => 'No evaluations found',
                'average' => null
            ]);
        }

        $average = $avaliacoes->avg('nota');

        return response()->json([
            'average' => $average,
            'count' => $avaliacoes->count(),
            'evaluations' => $avaliacoes
        ]);
    }
}
