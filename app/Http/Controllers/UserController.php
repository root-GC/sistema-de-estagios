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

  public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
            'role' => 'required|in:ADMIN,COORDENADOR,SUPERVISOR,TUTOR,ESTAGIARIO,CHEFE_REPARTICAO',
        ]);

        // Hash da password
        $data['password'] = Hash::make($data['password']);

        // Cria o user (sem campo role no banco!)
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'ativo' => true, // podes adicionar se quiser default
        ]);

        // Atribui a role com Spatie
        $user->assignRole($data['role']);

    // registrar log
    $createdBy = Auth::check() ? Auth::user()->name : 'Sistema';
    (new AdminController)->criarLog("Usuário {$user->name} criado por " . $createdBy);

        return response()->json($user->load('roles'), 201); // opcional: carrega roles para retorno
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

}
