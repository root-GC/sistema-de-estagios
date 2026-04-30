<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
class CursoController extends Controller
{
   public function store(Request $request)
    {
        return Curso::create($request->all());
    }

    public function index()
    {
        return Curso::all();
    }

    public function update(Request $request, $id)
    {
        $curso = Curso::findOrFail($id);

        $curso->update($request->all());

        return $curso;
    }

    public function show($id){
         $curso = Curso::findOrFail($id);
        return response()->json($curso);
    }

    public function ativar($id){
        $curso = Curso::findOrFail($id);

         if ($curso->ativo) {
        return response()->json([
            'message' => 'Curso já está ativo'
        ], 200);
    }
        $curso->ativo= true;
        $curso->save();
        return response()->json($curso);
    }

    public function desativar($id){
         $curso = Curso::findOrFail($id);
        $curso->ativo= false;
          if (!$curso->ativo) {
        return response()->json([
            'message' => 'Curso não está activo'
        ], 200);
    }
        $curso->save();
        return response()->json($curso);
    }

    public function destroy($id)
    {
        Curso::destroy($id);

        return response()->json(['message'=>'Curso removido']);
    }
}
