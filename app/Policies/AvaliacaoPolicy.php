<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Avaliacao;

class AvaliacaoPolicy
{
    /**
     * Ver avaliação
     */
    public function view(User $auth, Avaliacao $avaliacao): bool
    {
        // Admin vê todas
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Coordenador vê avaliações do seu departamento
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $avaliacao->estagio->curso?->departamento_id;
        }

        // Estudante vê suas avaliações
        if ($auth->hasRole('estudante')) {
            return $auth->id === $avaliacao->estagio->estagiario_id;
        }

        // Supervisor vê avaliações de seus estágios
        if ($auth->hasRole('supervisor')) {
            return $auth->id === $avaliacao->estagio->supervisor_id;
        }

        // Tutor vê avaliações de seus estágios
        if ($auth->hasRole('tutor')) {
            return $auth->id === $avaliacao->estagio->tutor_id;
        }

        return false;
    }

    /**
     * Criar avaliação
     */
    public function create(User $auth): bool
    {
        // Supervisor e tutor podem criar avaliações
        return $auth->hasAnyRole(['supervisor', 'tutor', 'admin', 'coordenador']);
    }

    /**
     * Atualizar avaliação
     */
    public function update(User $auth, Avaliacao $avaliacao): bool
    {
        // Admin pode atualizar qualquer avaliação
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Supervisor atualiza suas avaliações
        if ($auth->hasRole('supervisor')) {
            return $auth->id === $avaliacao->avaliador_id && 
                   $avaliacao->tipo === 'supervisor';
        }

        // Tutor atualiza suas avaliações
        if ($auth->hasRole('tutor')) {
            return $auth->id === $avaliacao->avaliador_id && 
                   $avaliacao->tipo === 'tutor';
        }

        // Coordenador atualiza avaliações do seu departamento
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $avaliacao->estagio->curso?->departamento_id;
        }

        return false;
    }

    /**
     * Deletar avaliação
     */
    public function delete(User $auth, Avaliacao $avaliacao): bool
    {
        // Admin pode deletar
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Coordenador deleta avaliações do seu departamento
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $avaliacao->estagio->curso?->departamento_id;
        }

        return false;
    }

    /**
     * Submeter avaliação de tutor
     */
    public function submitTutorEvaluation(User $auth): bool
    {
        return $auth->hasRole('tutor');
    }

    /**
     * Submeter avaliação de supervisor
     */
    public function submitSupervisorEvaluation(User $auth): bool
    {
        return $auth->hasRole('supervisor');
    }

    /**
     * Calcular média
     */
    public function calculateAverage(User $auth, Avaliacao $avaliacao = null): bool
    {
        return $auth->hasAnyRole(['admin', 'coordenador']);
    }
}
