<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Instituicao;

class InstituicaoPolicy
{
    /**
     * Ver instituição
     */
    public function view(User $auth, Instituicao $instituicao): bool
    {
        // Todos autenticados podem ver instituições
        return true;
    }

    /**
     * Criar instituição
     */
    public function create(User $auth): bool
    {
        return $auth->hasAnyRole(['admin', 'chefe_repartição']);
    }

    /**
     * Atualizar instituição
     */
    public function update(User $auth, Instituicao $instituicao): bool
    {
        return $auth->hasAnyRole(['admin', 'chefe_repartição']);
    }

    /**
     * Deletar instituição
     */
    public function delete(User $auth, Instituicao $instituicao): bool
    {
        return $auth->hasRole('admin');
    }

    /**
     * Aprovar instituição
     */
    public function approve(User $auth, Instituicao $instituicao): bool
    {
        return $auth->hasRole('admin');
    }

    /**
     * Rejeitar instituição
     */
    public function reject(User $auth, Instituicao $instituicao): bool
    {
        return $auth->hasRole('admin');
    }

    /**
     * Suspender instituição
     */
    public function suspend(User $auth, Instituicao $instituicao): bool
    {
        return $auth->hasRole('admin');
    }
}
