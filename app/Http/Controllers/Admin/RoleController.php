<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Listar papéis
     */
    public function index()
    {
        $roles = Role::with('permissions')->paginate(15);
        return response()->json($roles);
    }

    /**
     * Criar papel
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles',
            'description' => 'nullable|string'
        ]);

        $role = Role::create($validated);
        return response()->json($role, 201);
    }

    /**
     * Atualizar papel
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'string|unique:roles,name,'.$role->id,
            'description' => 'nullable|string'
        ]);

        $role->update($validated);
        return response()->json($role);
    }

    /**
     * Eliminar papel
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Role deleted']);
    }

    /**
     * Atribuir permissão ao papel
     */
    public function assignPermission(Request $request, Role $role)
    {
        $validated = $request->validate(['permission' => 'required|string']);
        $role->givePermissionTo($validated['permission']);
        return response()->json(['message' => 'Permission assigned']);
    }

    /**
     * Remover permissão do papel
     */
    public function removePermission(Request $request, Role $role)
    {
        $validated = $request->validate(['permission' => 'required|string']);
        $role->revokePermissionTo($validated['permission']);
        return response()->json(['message' => 'Permission removed']);
    }
}
