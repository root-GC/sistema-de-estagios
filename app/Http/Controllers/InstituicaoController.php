<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instituicao;

class InstituicaoController extends Controller
{
   public function store(Request $request)
    {
        return Instituicao::create($request->all());
    }

    public function index()
    {
        return Instituicao::all();
    }

    public function update(Request $request, $id)
    {
        $inst = Instituicao::findOrFail($id);

        $inst->update($request->all());

        return $inst;
    }

    public function destroy($id)
    {
        Instituicao::destroy($id);

        return response()->json(['message'=>'Instituição removida']);
    }
}
