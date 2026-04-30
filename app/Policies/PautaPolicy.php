<?php

namespace App\Policies;

use App\Models\User;

class PautaPolicy
{
    /**
     * Ver pautas
     */
    public function view(User $auth): bool
    {
        return $auth->hasAnyRole(['admin', 'coordenador']);
    }

    /**
     * Gerar pauta
     */
    public function generate(User $auth): bool
    {
        return $auth->hasAnyRole(['admin', 'coordenador']);
    }

    /**
     * Exportar para SIGEUP
     */
    public function exportSigeup(User $auth): bool
    {
        return $auth->hasAnyRole(['admin', 'coordenador']);
    }

    /**
     * Publicar pauta
     */
    public function publish(User $auth): bool
    {
        return $auth->hasRole('admin');
    }
}
