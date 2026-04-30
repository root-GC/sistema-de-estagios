<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    /**
     * Listar cursos
     */
    public function index()
    {
        $cursos = Curso::with('departamento', 'estudantes', 'estagios')->paginate(15);
        return response()->json($cursos);
    }

    /**
     * Ver curso específico
     */
    public function show(Curso $curso)
    {
        return response()->json(
            $curso->load('departamento', 'estudantes', 'estagios')
        );
    }

    /**
     * Criar curso
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'departamento_id' => 'required|exists:departamentos,id',
            'nome' => 'required|string|unique:cursos',
            'descricao' => 'nullable|string',
            'duracao_anos' => 'required|integer|min:1'
        ]);

        $curso = Curso::create($validated);
        return response()->json($curso, 201);
    }

    /**
     * Atualizar curso
     */
    public function update(Request $request, Curso $curso)
    {
        $validated = $request->validate([
            'departamento_id' => 'exists:departamentos,id',
            'nome' => 'string|unique:cursos,nome,'.$curso->id,
            'descricao' => 'nullable|string',
            'duracao_anos' => 'integer|min:1'
        ]);

        $curso->update($validated);
        return response()->json($curso);
    }

    /**
     * Eliminar curso
     */
    public function destroy(Curso $curso)
    {
        $curso->delete();
        return response()->json(['message' => 'Course deleted']);
    }

    /**
     * Listar coordenadores do curso
     */
    public function coordenadores(Curso $curso)
    {
        $coordenadores = $curso->estagios()
            ->distinct('coordenador_id')
            ->with('coordenador')
            ->get()
            ->pluck('coordenador');

        return response()->json($coordenadores);
    }

    /**
     * Listar estudantes do curso
     */
    public function estudantes(Curso $curso)
    {
        $estudantes = $curso->estudantes()->with('user')->paginate(15);
        return response()->json($estudantes);
    }
}
