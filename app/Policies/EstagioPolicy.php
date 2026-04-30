<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Estagio;

class EstagioPolicy
{
    /**
     * Ver estágio
     */
    public function view(User $auth, Estagio $estagio): bool
    {
        // Admin vê todos
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Coordenador vê estágios do seu departamento
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $estagio->curso?->departamento_id;
        }

        // Chefe de repartição vê estágios da sua instituição
        if ($auth->hasRole('chefe_repartição')) {
            return $auth->staffProfile?->departamento_id === $estagio->curso?->departamento_id;
        }

        // Estudante vê seus próprios estágios
        if ($auth->hasRole('estudante')) {
            return $auth->id === $estagio->estagiario_id;
        }

        // Supervisor vê seus estágios
        if ($auth->hasRole('supervisor')) {
            return $auth->id === $estagio->supervisor_id;
        }

        // Tutor vê seus estágios
        if ($auth->hasRole('tutor')) {
            return $auth->id === $estagio->tutor_id;
        }

        return false;
    }

    /**
     * Criar estágio
     */
    public function create(User $auth): bool
    {
        return $auth->hasAnyRole(['admin', 'coordenador']);
    }

    /**
     * Atualizar estágio
     */
    public function update(User $auth, Estagio $estagio): bool
    {
        // Admin pode atualizar qualquer estágio
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Coordenador atualiza estágios do seu departamento
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $estagio->curso?->departamento_id;
        }

        return false;
    }

    /**
     * Deletar estágio
     */
    public function delete(User $auth, Estagio $estagio): bool
    {
        return $auth->hasRole('admin');
    }

    /**
     * Alocar supervisor
     */
    public function allocateSupervisor(User $auth, Estagio $estagio): bool
    {
        // Admin pode alocar
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Coordenador aloca supervisores para seus estágios
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $estagio->curso?->departamento_id;
        }

        return false;
    }

    /**
     * Atribuir tutor
     */
    public function assignTutor(User $auth, Estagio $estagio): bool
    {
        // Admin pode atribuir
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Coordenador atribui tutores para seus estágios
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $estagio->curso?->departamento_id;
        }

        return false;
    }

    /**
     * Mudar status
     */
    public function changeStatus(User $auth, Estagio $estagio): bool
    {
        // Admin pode mudar
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Coordenador pode mudar status de seus estágios
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $estagio->curso?->departamento_id;
        }

        return false;
    }

    /**
     * Calcular nota final
     */
    public function calculateFinalGrade(User $auth, Estagio $estagio): bool
    {
        // Admin e coordenador podem calcular
        if ($auth->hasAnyRole(['admin', 'coordenador'])) {
            return $auth->hasRole('admin') || 
                   ($auth->hasRole('coordenador') && $auth->staffProfile?->departamento_id === $estagio->curso?->departamento_id);
        }

        return false;
    }

    /**
     * Encerrar estágio
     */
    public function closeInternship(User $auth, Estagio $estagio): bool
    {
        // Admin e coordenador podem encerrar
        if ($auth->hasAnyRole(['admin', 'coordenador'])) {
            return $auth->hasRole('admin') || 
                   ($auth->hasRole('coordenador') && $auth->staffProfile?->departamento_id === $estagio->curso?->departamento_id);
        }

        return false;
    }
}
