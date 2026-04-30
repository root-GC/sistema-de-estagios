<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Departamento;

class DepartamentoPolicy
{
    /**
     * Ver departamento
     */
    public function view(User $auth, Departamento $departamento): bool
    {
        // Todos podem ver departamentos
        return true;
    }

    /**
     * Criar departamento
     */
    public function create(User $auth): bool
    {
        return $auth->hasRole('admin');
    }

    /**
     * Atualizar departamento
     */
    public function update(User $auth, Departamento $departamento): bool
    {
        // Admin pode atualizar qualquer departamento
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Chefe de repartição atualiza o seu departamento
        if ($auth->hasRole('chefe_repartição')) {
            return $auth->staffProfile?->departamento_id === $departamento->id;
        }

        return false;
    }

    /**
     * Deletar departamento
     */
    public function delete(User $auth, Departamento $departamento): bool
    {
        return $auth->hasRole('admin');
    }
}
