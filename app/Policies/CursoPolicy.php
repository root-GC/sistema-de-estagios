<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Curso;

class CursoPolicy
{
    /**
     * Ver curso
     */
    public function view(User $auth, Curso $curso): bool
    {
        // Admin e tutores veem todos os cursos
        if ($auth->hasAnyRole(['admin', 'tutor'])) {
            return true;
        }

        // Coordenador vê os cursos do seu departamento
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $curso->departamento_id;
        }

        // Chefe de repartição vê os cursos do seu departamento
        if ($auth->hasRole('chefe_repartição')) {
            return $auth->staffProfile?->departamento_id === $curso->departamento_id;
        }

        // Estudante vê o seu curso
        if ($auth->hasRole('estudante')) {
            return $auth->studentProfile?->curso_id === $curso->id;
        }

        return false;
    }

    /**
     * Criar curso
     */
    public function create(User $auth): bool
    {
        return $auth->hasAnyRole(['admin', 'chefe_repartição']);
    }

    /**
     * Atualizar curso
     */
    public function update(User $auth, Curso $curso): bool
    {
        // Admin pode atualizar qualquer curso
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Chefe de repartição atualiza cursos do seu departamento
        if ($auth->hasRole('chefe_repartição')) {
            return $auth->staffProfile?->departamento_id === $curso->departamento_id;
        }

        // Coordenador atualiza cursos do seu departamento
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $curso->departamento_id;
        }

        return false;
    }

    /**
     * Deletar curso
     */
    public function delete(User $auth, Curso $curso): bool
    {
        return $auth->hasRole('admin');
    }
}
