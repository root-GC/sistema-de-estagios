<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class StudentProfileController extends Controller
{
    /**
     * Listar perfis de estudante
     */
    public function index()
    {
        $profiles = StudentProfile::with('user', 'curso', 'estagios')->paginate(15);
        return response()->json($profiles);
    }

    /**
     * Ver perfil de estudante
     */
    public function show(StudentProfile $studentProfile)
    {
        return response()->json(
            $studentProfile->load('user', 'curso', 'estagios')
        );
    }

    /**
     * Criar perfil de estudante
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:student_profiles',
            'numero_estudante' => 'required|string|unique:student_profiles',
            'curso_id' => 'required|exists:cursos,id',
            'ano_academico' => 'nullable|string',
            'media' => 'nullable|numeric|min:0|max:20',
            'elegivel_estagio' => 'boolean'
        ]);

        $profile = StudentProfile::create($validated);
        return response()->json($profile, 201);
    }

    /**
     * Atualizar perfil de estudante
     */
    public function update(Request $request, StudentProfile $studentProfile)
    {
        $validated = $request->validate([
            'numero_estudante' => 'string|unique:student_profiles,numero_estudante,'.$studentProfile->id,
            'curso_id' => 'exists:cursos,id',
            'ano_academico' => 'nullable|string',
            'media' => 'nullable|numeric|min:0|max:20',
            'elegivel_estagio' => 'boolean'
        ]);

        $studentProfile->update($validated);
        return response()->json($studentProfile);
    }

    /**
     * Verificar elegibilidade de estágio
     */
    public function checkEligibility(StudentProfile $studentProfile)
    {
        $eligible = $studentProfile->elegivel_estagio;
        $media = $studentProfile->media;
        $hasActiveInternship = $studentProfile->estagios()
            ->whereNotIn('estado', ['concluido', 'cancelado'])
            ->exists();

        return response()->json([
            'eligible' => $eligible,
            'media' => $media,
            'has_active_internship' => $hasActiveInternship,
            'can_apply' => $eligible && !$hasActiveInternship
        ]);
    }
}
