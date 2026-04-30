<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StaffProfile;

class StaffProfilePolicy
{
    /**
     * Ver perfil de staff
     */
    public function view(User $auth, StaffProfile $staffProfile): bool
    {
        // Admin vê todos
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Chefe de repartição vê staff do seu departamento
        if ($auth->hasRole('chefe_repartição')) {
            return $auth->staffProfile?->departamento_id === $staffProfile->departamento_id;
        }

        // Staff vê seu próprio perfil
        if ($auth->staffProfile?->id === $staffProfile->id) {
            return true;
        }

        return false;
    }

    /**
     * Criar perfil de staff
     */
    public function create(User $auth): bool
    {
        return $auth->hasAnyRole(['admin', 'chefe_repartição']);
    }

    /**
     * Atualizar perfil de staff
     */
    public function update(User $auth, StaffProfile $staffProfile): bool
    {
        // Admin pode atualizar qualquer perfil
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Chefe de repartição atualiza staff do seu departamento
        if ($auth->hasRole('chefe_repartição')) {
            return $auth->staffProfile?->departamento_id === $staffProfile->departamento_id;
        }

        // Staff atualiza seu próprio perfil
        if ($auth->staffProfile?->id === $staffProfile->id) {
            return true;
        }

        return false;
    }

    /**
     * Deletar perfil de staff
     */
    public function delete(User $auth, StaffProfile $staffProfile): bool
    {
        return $auth->hasRole('admin');
    }
}
