<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Listar utilizadores
     */
    public function index()
    {
        $users = User::paginate(15);
        return response()->json($users);
    }

    /**
     * Ver utilizador específico
     */
    public function show(User $user)
    {
        return response()->json($user->load('roles', 'permissions'));
    }

    /**
     * Criar utilizador
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'ativo' => 'boolean'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        return response()->json($user, 201);
    }

    /**
     * Atualizar utilizador
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,'.$user->id,
            'ativo' => 'boolean'
        ]);

        $user->update($validated);
        return response()->json($user);
    }

    /**
     * Eliminar utilizador
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    /**
     * Ativar utilizador
     */
    public function activate(User $user)
    {
        $user->update(['ativo' => true]);
        return response()->json(['message' => 'User activated']);
    }

    /**
     * Desativar utilizador
     */
    public function deactivate(User $user)
    {
        $user->update(['ativo' => false]);
        return response()->json(['message' => 'User deactivated']);
    }

    /**
     * Atribuir papel
     */
    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate(['role' => 'required|string']);
        $user->assignRole($validated['role']);
        return response()->json(['message' => 'Role assigned']);
    }

    /**
     * Remover papel
     */
    public function removeRole(Request $request, User $user)
    {
        $validated = $request->validate(['role' => 'required|string']);
        $user->removeRole($validated['role']);
        return response()->json(['message' => 'Role removed']);
    }

    /**
     * Reset password do utilizador
     */
    public function resetPassword(Request $request, User $user)
    {
        $newPassword = $request->validate(['password' => 'required|min:8'])['password'];
        $user->update(['password' => bcrypt($newPassword)]);
        return response()->json(['message' => 'Password reset']);
    }

    /**
     * Ver logs do utilizador
     */
    public function logs(User $user)
    {
        $logs = $user->logs()->paginate(15);
        return response()->json($logs);
    }
}
