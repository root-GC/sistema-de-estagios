<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Instituicao;
use Illuminate\Http\Request;

class InstituicaoController extends Controller
{
    /**
     * Listar instituições
     */
    public function index()
    {
        $instituicoes = Instituicao::with('estagios')->paginate(15);
        return response()->json($instituicoes);
    }

    /**
     * Ver instituição específica
     */
    public function show(Instituicao $instituicao)
    {
        return response()->json(
            $instituicao->load('estagios')
        );
    }

    /**
     * Criar instituição
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|unique:instituicoes',
            'nuit' => 'required|string|unique:instituicoes',
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string',
            'email' => 'nullable|email',
            'ponto_focal_nome' => 'nullable|string',
            'ponto_focal_contacto' => 'nullable|string',
            'status' => 'nullable|in:ativa,suspensa,inativa',
            'validade_parceria' => 'nullable|date'
        ]);

        $instituicao = Instituicao::create($validated);
        return response()->json($instituicao, 201);
    }

    /**
     * Atualizar instituição
     */
    public function update(Request $request, Instituicao $instituico)
    {
        $validated = $request->validate([
            'nome' => 'string|unique:instituicoes,nome,'.$instituico->id,
            'nuit' => 'string|unique:instituicoes,nuit,'.$instituico->id,
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string',
            'email' => 'nullable|email',
            'ponto_focal_nome' => 'nullable|string',
            'ponto_focal_contacto' => 'nullable|string',
            'status' => 'in:ativa,suspensa,inativa',
            'validade_parceria' => 'nullable|date'
        ]);

        $instituico->update($validated);
        return response()->json($instituico);
    }

    /**
     * Eliminar instituição
     */
    public function destroy(Instituicao $instituico)
    {
        $instituico->delete();
        return response()->json(['message' => 'Institution deleted']);
    }

    /**
     * Aprovar instituição
     */
    public function approve(Instituicao $instituicao)
    {
        $instituicao->update(['status' => 'ativa']);
        return response()->json(['message' => 'Institution approved']);
    }

    /**
     * Rejeitar instituição
     */
    public function reject(Instituicao $instituicao)
    {
        $instituicao->update(['status' => 'inativa']);
        return response()->json(['message' => 'Institution rejected']);
    }

    /**
     * Suspender instituição
     */
    public function suspend(Instituicao $instituicao)
    {
        $instituicao->update(['status' => 'suspensa']);
        return response()->json(['message' => 'Institution suspended']);
    }

    /**
     * Listar estágios da instituição
     */
    public function estagios(Instituicao $instituicao)
    {
        $estagios = $instituicao->estagios()
            ->with('estagiario', 'supervisor', 'tutor', 'coordenador', 'curso')
            ->paginate(15);

        return response()->json($estagios);
    }
}
