<?php

namespace App\Http\Controllers;

use App\Models\Estagio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ Importa o Auth

class EstagioController extends Controller
{

    //
    //Listar todos os estágios
    //
    public function index()
    {
        return Estagio::with([
            'estagiario',
            'supervisor',
            'tutor',
            'instituicao',
            'curso'
        ])->get();
    }

    //
    //Criacao de um estagio
    //
    public function store(Request $request)
    {
        $request->validate([
            'estagiario_id' => 'required|exists:users,id',
            'supervisor_id' => 'required|exists:users,id',
            'instituicao_id' => 'required|exists:instituicoes,id',
            'curso_id' => 'required|exists:cursos,id'
        ]);

        // regra: supervisor <= 5 estagiários
        $count = Estagio::where('supervisor_id', $request->supervisor_id)->count();

        if ($count >= 5) {
            return response()->json([
                'erro' => 'Supervisor já atingiu o limite de 5 estagiários'
            ], 400);
        }

        $estagio = Estagio::create([
            'estagiario_id' => $request->estagiario_id,
            'supervisor_id' => $request->supervisor_id,
            'instituicao_id' => $request->instituicao_id,
            'curso_id' => $request->curso_id
        ]);

        return response()->json($estagio);
    }

    //
    //Atribuicao de tutor depois da criacao de um estágio
    //
    public function atribuirTutor(Request $request, $id)
    {
        $request->validate([
            'tutor_id' => 'required|exists:users,id'
        ]);

        // regra: tutor <= 3 estagiários
        $count = Estagio::where('tutor_id', $request->tutor_id)->count();

        if ($count >= 3) {
            return response()->json([
                'erro' => 'Tutor já tem 3 estagiários'
            ], 400);
        }

        $estagio = Estagio::findOrFail($id);

        $estagio->update([
            'tutor_id' => $request->tutor_id
        ]);

        return response()->json([
            'message' => 'Tutor atribuído com sucesso'
        ]);
    }

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

    public function show($id){
        $estagio = Estagio::findOrFail($id);
        return response()->json($estagio);
    }

    public function porTutor($id){
        $estagio = Estagio::where('id_tutor', $id);
        return response()->json($estagio);
    }

   

    // 🔐 Supervisor só vê os seus estágios
    public function meusEstagios()
    {
        $estagios = Estagio::where('supervisor_id', Auth::id())->get(); // ✅ Usando Auth::id()

        return response()->json($estagios);
    }
//     public function meusEstagios()
// {
//     return Estagio::where('supervisor_id', Auth::id())
//         ->with(['estagiario','instituicao','curso'])
//         ->get();
// }
}