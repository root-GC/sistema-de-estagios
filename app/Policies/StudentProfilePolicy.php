<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StudentProfile;

class StudentProfilePolicy
{
    /**
     * Ver perfil de estudante
     */
    public function view(User $auth, StudentProfile $studentProfile): bool
    {
        // Admin vê todos
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Coordenador vê estudantes do seu departamento
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $studentProfile->curso?->departamento_id;
        }

        // Estudante vê seu próprio perfil
        if ($auth->hasRole('estudante')) {
            return $auth->studentProfile?->id === $studentProfile->id;
        }

        // Supervisor e tutor veem apenas se têm estágios com o estudante
        if ($auth->hasAnyRole(['supervisor', 'tutor'])) {
            return $studentProfile->estagios()
                ->where(function ($q) use ($auth) {
                    $q->where('supervisor_id', $auth->id)
                      ->orWhere('tutor_id', $auth->id);
                })
                ->exists();
        }

        return false;
    }

    /**
     * Criar perfil de estudante
     */
    public function create(User $auth): bool
    {
        return $auth->hasAnyRole(['admin', 'coordenador']);
    }

    /**
     * Atualizar perfil de estudante
     */
    public function update(User $auth, StudentProfile $studentProfile): bool
    {
        // Admin pode atualizar qualquer perfil
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Coordenador atualiza estudantes do seu departamento
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $studentProfile->curso?->departamento_id;
        }

        // Estudante atualiza seu próprio perfil
        if ($auth->hasRole('estudante')) {
            return $auth->studentProfile?->id === $studentProfile->id;
        }

        return false;
    }

    /**
     * Deletar perfil de estudante
     */
    public function delete(User $auth, StudentProfile $studentProfile): bool
    {
        return $auth->hasRole('admin');
    }
}
