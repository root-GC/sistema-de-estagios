<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Ver utilizador
     */
    public function view(User $auth, User $user): bool
    {
        // Admin pode ver qualquer utilizador
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Chefe de repartição vê os seus staff
        if ($auth->hasRole('chefe_repartição')) {
            return $auth->staffProfile?->departamento_id === $user->staffProfile?->departamento_id;
        }

        // Utilizador vê a si mesmo
        return $auth->id === $user->id;
    }

    /**
     * Criar utilizador
     */
    public function create(User $auth): bool
    {
        return $auth->hasAnyRole(['admin', 'chefe_repartição', 'coordenador']);
    }

    /**
     * Atualizar utilizador
     */
    public function update(User $auth, User $user): bool
    {
        // Admin pode atualizar qualquer um
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Chefe de repartição atualiza staff do seu departamento
        if ($auth->hasRole('chefe_repartição')) {
            return $auth->staffProfile?->departamento_id === $user->staffProfile?->departamento_id;
        }

        // Coordenador atualiza os seus tutores
        if ($auth->hasRole('coordenador')) {
            $authCursos = $auth->staffProfile?->departamento?->cursos()->pluck('id');
            $userCursos = $user->staffProfile?->departamento?->cursos()->pluck('id');
            return $authCursos->intersect($userCursos)->count() > 0;
        }

        // Utilizador atualiza a si mesmo
        return $auth->id === $user->id;
    }

    /**
     * Deletar utilizador
     */
    public function delete(User $auth, User $user): bool
    {
        // Admin pode deletar
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Chefe de repartição deleta staff do seu departamento
        if ($auth->hasRole('chefe_repartição')) {
            return $auth->staffProfile?->departamento_id === $user->staffProfile?->departamento_id;
        }

        return false;
    }

    /**
     * Atribuir/remover papéis
     */
    public function assignRole(User $auth, User $user): bool
    {
        return $auth->hasRole('admin');
    }

    /**
     * Ver logs do utilizador
     */
    public function viewLogs(User $auth, User $user): bool
    {
        // Admin vê os logs de qualquer um
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Utilizador vê os seus próprios logs
        return $auth->id === $user->id;
    }
}
