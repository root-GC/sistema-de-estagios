<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AdminController;

class UserController extends Controller
{

    public function me(Request $request) {
        return $request->user();
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('ADMIN')) {
            $users = User::role('CHEFE_REPARTICAO')->get();
        } 
        elseif ($user->hasRole('COORDENADOR')) {
            $users = User::role([
                'ESTAGIARIO',
                'SUPERVISOR',
                'TUTOR'
            ])->get();
        } 
        else {
            $users = collect();
        }

        return response()->json($users);
    }

    public function store(Request $request) {

        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
            'role' => 'required|in:ADMIN,COORDENADOR,SUPERVISOR,TUTOR,ESTAGIARIO,CHEFE_REPARTICAO',
        ]);

        $userAuth = Auth::user();

        // definir roles permitidas
        if ($userAuth->hasRole('ADMIN')) {

            $rolesPermitidas = ['ADMIN','CHEFE_REPARTICAO'];

        } elseif ($userAuth->hasRole('CHEFE_REPARTICAO')) {

            $rolesPermitidas = ['COORDENADOR','TUTOR'];

        } else if($userAuth->hasRole('COORDENADOR')){

            $rolesPermitidas = ['SUPERVISOR','ESTAGIARIO'];
        }
        else {

            return response()->json([
                'message' => 'Sem permissão para criar utilizadores'
            ], 403);
        }

        // verificar se a role solicitada é permitida
        if (!in_array($data['role'], $rolesPermitidas)) {

            return response()->json([
                'message' => 'Não tem permissão para criar utilizador com essa role'
            ], 403);
        }

        // Hash da password
        $data['password'] = Hash::make($data['password']);

        // Criar utilizador
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'ativo' => true,
        ]);

        // atribuir role
        $user->assignRole($data['role']);

        // registrar log
        $createdBy = Auth::check() ? Auth::user()->name : 'Sistema';
        (new AdminController)->criarLog("Usuário {$user->name} criado por " . $createdBy);

        return response()->json($user->load('roles'), 201);
    }


    public function update(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return response()->json($user);
    }

    public function destroy($id) {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    public function ativar($id) {
        $user = User::findOrFail($id);
        $user->ativo = true;
        $user->save();
        return response()->json($user);
    }

    public function desativar($id) {
        $user = User::findOrFail($id);
        $user->ativo = false;
        $user->save();
        return response()->json($user);
    }

    public function usuariosInstituicao()
    {
        $instituicaoId = Auth::user()->instituicao_id;

        return User::where('instituicao_id', $instituicaoId)
            ->with('roles')
            ->get();
    }

}
