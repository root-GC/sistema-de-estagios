<?php

namespace App\Http\Controllers\Internship;

use App\Http\Controllers\Controller;
use App\Models\Estagio;
use Illuminate\Http\Request;

class EstagioController extends Controller
{
    /**
     * Listar estágios
     */
    public function index(Request $request)
    {
        $query = Estagio::with(
            'estagiario',
            'supervisor',
            'tutor',
            'coordenador',
            'instituicao',
            'curso',
            'documentos',
            'avaliacoes'
        );

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }

        if ($request->has('instituicao_id')) {
            $query->where('instituicao_id', $request->instituicao_id);
        }

        $estagios = $query->paginate(15);
        return response()->json($estagios);
    }

    /**
     * Ver estágio específico
     */
    public function show(Estagio $estagio)
    {
        $this->authorize('view', $estagio);

        return response()->json(
            $estagio->load(
                'estagiario',
                'supervisor',
                'tutor',
                'coordenador',
                'instituicao',
                'curso',
                'documentos',
                'avaliacoes'
            )
        );
    }

    /**
     * Criar estágio
     */
    public function store(Request $request)
    {
        $this->authorize('create', Estagio::class);

        $validated = $request->validate([
            'estagiario_id' => 'required|exists:users,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'tutor_id' => 'nullable|exists:users,id',
            'coordenador_id' => 'nullable|exists:users,id',
            'instituicao_id' => 'required|exists:instituicoes,id',
            'curso_id' => 'required|exists:cursos,id',
            'estado' => 'required|in:proposta,em_andamento,concluido,cancelado',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
            'nota_final' => 'nullable|numeric|min:0|max:20'
        ]);

        $estagio = Estagio::create($validated);
        return response()->json($estagio, 201);
    }

    /**
     * Atualizar estágio
     */
    public function update(Request $request, Estagio $estagio)
    {
        $this->authorize('update', $estagio);

        $validated = $request->validate([
            'supervisor_id' => 'nullable|exists:users,id',
            'tutor_id' => 'nullable|exists:users,id',
            'coordenador_id' => 'nullable|exists:users,id',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
            'nota_final' => 'nullable|numeric|min:0|max:20'
        ]);

        $estagio->update($validated);
        return response()->json($estagio);
    }

    /**
     * Alocar supervisor
     */
    public function allocateSupervisor(Request $request, Estagio $estagio)
    {
        $this->authorize('allocateSupervisor', $estagio);

        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id'
        ]);

        // Verificar limite de 5 estagiários
        $supervisorInternshipCount = Estagio::where('supervisor_id', $validated['supervisor_id'])
            ->whereNotIn('estado', ['concluido', 'cancelado'])
            ->count();

        if ($supervisorInternshipCount >= 5) {
            return response()->json(
                ['message' => 'Supervisor already has 5 active internships'],
                422
            );
        }

        $estagio->update(['supervisor_id' => $validated['supervisor_id']]);
        return response()->json(['message' => 'Supervisor allocated']);
    }

    /**
     * Atribuir tutor
     */
    public function assignTutor(Request $request, Estagio $estagio)
    {
        $this->authorize('assignTutor', $estagio);

        $validated = $request->validate([
            'tutor_id' => 'required|exists:users,id'
        ]);

        $estagio->update(['tutor_id' => $validated['tutor_id']]);
        return response()->json(['message' => 'Tutor assigned']);
    }

    /**
     * Mudar estado do estágio
     */
    public function changeStatus(Request $request, Estagio $estagio)
    {
        $this->authorize('changeStatus', $estagio);

        $validated = $request->validate([
            'estado' => 'required|in:proposta,em_andamento,concluido,cancelado'
        ]);

        $estagio->update(['estado' => $validated['estado']]);
        return response()->json(['message' => 'Status updated']);
    }

    /**
     * Gerar carta de credencial
     */
    public function generateCredentialLetter(Estagio $estagio)
    {
        // TODO: Implementar geração de documento
        return response()->json([
            'message' => 'Credential letter generated',
            'file' => 'credential-letter-'.$estagio->id.'.pdf'
        ]);
    }

    /**
     * Calcular nota final
     */
    public function calculateFinalGrade(Estagio $estagio)
    {
        $this->authorize('calculateFinalGrade', $estagio);

        $avaliacoes = $estagio->avaliacoes()->get();

        if ($avaliacoes->isEmpty()) {
            return response()->json([
                'message' => 'No evaluations found',
                'grade' => null
            ], 422);
        }

        $mediaNotas = $avaliacoes->avg('nota');
        $estagio->update(['nota_final' => $mediaNotas]);

        return response()->json([
            'message' => 'Final grade calculated',
            'grade' => $mediaNotas
        ]);
    }

    /**
     * Encerrar estágio
     */
    public function closeInternship(Request $request, Estagio $estagio)
    {
        $this->authorize('closeInternship', $estagio);

        $validated = $request->validate([
            'nota_final' => 'nullable|numeric|min:0|max:20'
        ]);

        if (isset($validated['nota_final'])) {
            $estagio->update(['nota_final' => $validated['nota_final']]);
        }

        $estagio->update(['estado' => 'concluido']);
        return response()->json(['message' => 'Internship closed']);
    }
}
