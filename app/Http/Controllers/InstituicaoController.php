<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instituicao;

class InstituicaoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string',
            'nuit' => 'nullable|string',
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string',
            'email' => 'nullable|email',
            'ponto_focal_nome' => 'required|string',
            'ponto_focal_contacto' => 'required|string',
            'cursos' => 'array'
        ]);

        $instituicao = Instituicao::create($data);

        if(isset($data['cursos'])){
            $instituicao->cursos()->sync($data['cursos']);
        }

        return response()->json($instituicao->load('cursos'),201);
    }

    public function index()
    {
        return Instituicao::with('cursos')->latest()->get();
    }

   public function update(Request $request, $id)
    {
        $instituicao = Instituicao::findOrFail($id);

        $data = $request->validate([
            'nome' => 'sometimes|string',
            'nuit' => 'nullable|string',
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string',
            'email' => 'nullable|email',
            'ponto_focal_nome' => 'nullable|string',
            'ponto_focal_contacto' => 'nullable|string',
            'cursos' => 'array'
        ]);

        $instituicao->update($data);

        if(isset($data['cursos'])){
            $instituicao->cursos()->sync($data['cursos']);
        }

        return response()->json($instituicao->load('cursos'));
    }

    public function destroy($id)
    {
        Instituicao::destroy($id);

        return response()->json(['message'=>'Instituição removida']);
    }
}
