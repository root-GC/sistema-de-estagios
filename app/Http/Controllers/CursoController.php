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

    public function destroy($id)
    {
        Curso::destroy($id);

        return response()->json(['message'=>'Curso removido']);
    }
}
