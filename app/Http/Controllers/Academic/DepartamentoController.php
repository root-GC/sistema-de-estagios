<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    /**
     * Listar departamentos
     */
    public function index()
    {
        $departamentos = Departamento::with('cursos', 'staffs')->paginate(15);
        return response()->json($departamentos);
    }

    /**
     * Ver departamento específico
     */
    public function show(Departamento $departamento)
    {
        return response()->json(
            $departamento->load('cursos', 'staffs')
        );
    }

    /**
     * Criar departamento
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|unique:departamentos',
            'descricao' => 'nullable|string'
        ]);

        $departamento = Departamento::create($validated);
        return response()->json($departamento, 201);
    }

    /**
     * Atualizar departamento
     */
    public function update(Request $request, Departamento $departamento)
    {
        $validated = $request->validate([
            'nome' => 'string|unique:departamentos,nome,'.$departamento->id,
            'descricao' => 'nullable|string'
        ]);

        $departamento->update($validated);
        return response()->json($departamento);
    }

    /**
     * Eliminar departamento
     */
    public function destroy(Departamento $departamento)
    {
        $departamento->delete();
        return response()->json(['message' => 'Department deleted']);
    }
}
