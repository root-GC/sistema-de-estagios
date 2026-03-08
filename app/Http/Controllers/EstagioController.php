<?php

namespace App\Http\Controllers;

use App\Models\Estagio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ Importa o Auth

class EstagioController extends Controller
{

    // public function estagiosInstituicao()
    // {
    //     $instituicaoId = Auth::user()->instituicao_id;

    //     return Estagio::where('instituicao_id', $instituicaoId)
    //         ->with(['estagiario','supervisor','tutor'])
    //         ->get();
    // }


    public function estagiosInstituicao($id)
    {
        return Estagio::where('instituicao_id', $id)
            ->with(['estagiario','supervisor','tutor'])
            ->get();
    }

    public function todosEstagios()
    {
        // Trás todos os estágios com os relacionamentos
        return Estagio::with(['estagiario','supervisor','tutor', 'instituicao'])->get();
    }

    // 🔥 Atribuir supervisor com regra dos 5 alunos
    public function atribuirSupervisor(Request $request)
    {
        $supervisorId = $request->supervisor_id;
        $estagioId = $request->estagio_id;

        $count = Estagio::where('supervisor_id', $supervisorId)->count();

        if ($count >= 5) {
            return response()->json([
                'erro' => 'Supervisor já tem 5 estagiários'
            ], 400);
        }

        $estagio = Estagio::findOrFail($estagioId);
        $estagio->supervisor_id = $supervisorId;
        $estagio->save();

        return response()->json(['message' => 'Supervisor atribuído com sucesso']);
    }

    // 🔐 Supervisor só vê os seus estágios
    public function meusEstagios()
    {
        $estagios = Estagio::where('supervisor_id', Auth::id())->get(); // ✅ Usando Auth::id()

        return response()->json($estagios);
    }
}