<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


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
            'role' => 'required|string',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return response()->json($user, 201);
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



    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required|min:6',
    //         'role' => 'required|in:ESTAGIARIO,SUPERVISOR,TUTOR,COORDENADOR,CHEFE_REPARTICAO'
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'role' => $request->role
    //     ]);

    //     return response()->json($user, 201);
    // }

    // public function me()
    // {
    //     Log::info(Auth::user());
    //     return response()->json(Auth::user());
    // }

    // public function update(Request $request, $id)
    // {
    //     $user = User::findOrFail($id);

    //     $request->validate([
    //         'name' => 'sometimes|string',
    //         'email' => 'sometimes|email|unique:users,email,' . $id,
    //         'password' => 'sometimes|min:6',
    //         'role' => 'sometimes|in:ESTAGIARIO,SUPERVISOR,TUTOR,COORDENADOR,CHEFE_REPARTICAO',
    //         'ativo' => 'sometimes|boolean'
    //     ]);

    //     if ($request->has('name')) {
    //         $user->name = $request->name;
    //     }

    //     if ($request->has('email')) {
    //         $user->email = $request->email;
    //     }

    //     if ($request->has('password')) {
    //         $user->password = Hash::make($request->password);
    //     }

    //     if ($request->has('role')) {
    //         $user->role = $request->role;
    //     }

    //     if ($request->has('ativo')) {
    //         $user->ativo = $request->ativo;
    //     }

    //     $user->save();

    //     return response()->json($user);
    // }
}
