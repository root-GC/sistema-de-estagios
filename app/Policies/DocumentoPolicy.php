<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Documento;

class DocumentoPolicy
{
    /**
     * Ver documento
     */
    public function view(User $auth, Documento $documento): bool
    {
        // Admin vê todos
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Coordenador vê documentos do seu departamento
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $documento->estagio->curso?->departamento_id;
        }

        // Estudante vê seus documentos
        if ($auth->hasRole('estudante')) {
            return $auth->id === $documento->estagio->estagiario_id;
        }

        // Supervisor vê documentos de seus estágios
        if ($auth->hasRole('supervisor')) {
            return $auth->id === $documento->estagio->supervisor_id;
        }

        // Tutor vê documentos de seus estágios
        if ($auth->hasRole('tutor')) {
            return $auth->id === $documento->estagio->tutor_id;
        }

        return false;
    }

    /**
     * Criar/upload documento
     */
    public function create(User $auth, Documento $documento = null): bool
    {
        // Estudante pode fazer upload dos seus documentos
        if ($auth->hasRole('estudante')) {
            return true;
        }

        // Admin e coordenador podem fazer upload
        return $auth->hasAnyRole(['admin', 'coordenador']);
    }

    /**
     * Deletar documento
     */
    public function delete(User $auth, Documento $documento): bool
    {
        // Admin pode deletar
        if ($auth->hasRole('admin')) {
            return true;
        }

        // Coordenador deleta documentos do seu departamento
        if ($auth->hasRole('coordenador')) {
            return $auth->staffProfile?->departamento_id === $documento->estagio->curso?->departamento_id;
        }

        // Estudante deleta seus próprios documentos (se ainda não foram revistos)
        if ($auth->hasRole('estudante')) {
            return $auth->id === $documento->estagio->estagiario_id && 
                   $documento->estado === 'pendente_revisao';
        }

        return false;
    }

    /**
     * Aprovar documento
     */
    public function approve(User $auth, Documento $documento): bool
    {
        // Supervisor aprova documentos de seus estágios
        if ($auth->hasRole('supervisor')) {
            return $auth->id === $documento->estagio->supervisor_id;
        }

        // Admin e coordenador podem aprovar
        return $auth->hasRole('admin') || 
               ($auth->hasRole('coordenador') && 
                $auth->staffProfile?->departamento_id === $documento->estagio->curso?->departamento_id);
    }

    /**
     * Rejeitar documento
     */
    public function reject(User $auth, Documento $documento): bool
    {
        // Supervisor rejeita documentos de seus estágios
        if ($auth->hasRole('supervisor')) {
            return $auth->id === $documento->estagio->supervisor_id;
        }

        // Admin e coordenador podem rejeitar
        return $auth->hasRole('admin') || 
               ($auth->hasRole('coordenador') && 
                $auth->staffProfile?->departamento_id === $documento->estagio->curso?->departamento_id);
    }

    /**
     * Comentar documento
     */
    public function comment(User $auth, Documento $documento): bool
    {
        // Supervisor pode comentar seus documentos
        if ($auth->hasRole('supervisor')) {
            return $auth->id === $documento->estagio->supervisor_id;
        }

        // Admin e coordenador podem comentar
        return $auth->hasRole('admin') || 
               ($auth->hasRole('coordenador') && 
                $auth->staffProfile?->departamento_id === $documento->estagio->curso?->departamento_id);
    }

    /**
     * Download documento
     */
    public function download(User $auth, Documento $documento): bool
    {
        // Todos os intervenientes no estágio podem fazer download
        return $this->view($auth, $documento);
    }
}
