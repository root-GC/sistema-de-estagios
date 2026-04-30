<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\StaffProfile;
use Illuminate\Http\Request;

class StaffProfileController extends Controller
{
    /**
     * Listar perfis de staff
     */
    public function index()
    {
        $profiles = StaffProfile::with('user', 'departamento')->paginate(15);
        return response()->json($profiles);
    }

    /**
     * Ver perfil de staff
     */
    public function show(StaffProfile $staffProfile)
    {
        return response()->json(
            $staffProfile->load('user', 'departamento')
        );
    }

    /**
     * Criar perfil de staff
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:staff_profiles',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'cargo' => 'required|string',
            'telefone' => 'nullable|string'
        ]);

        $profile = StaffProfile::create($validated);
        return response()->json($profile, 201);
    }

    /**
     * Atualizar perfil de staff
     */
    public function update(Request $request, StaffProfile $staffProfile)
    {
        $validated = $request->validate([
            'departamento_id' => 'nullable|exists:departamentos,id',
            'cargo' => 'string',
            'telefone' => 'nullable|string'
        ]);

        $staffProfile->update($validated);
        return response()->json($staffProfile);
    }
}
