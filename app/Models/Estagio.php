<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estagio extends Model
{
    use HasFactory;

    protected $fillable = [
        'estagiario_id', 'supervisor_id', 'tutor_id', 'instituicao_id',
        'curso_id', 'estado', 'nota_final'
    ];

    public function estagiario() {
        return $this->belongsTo(User::class, 'estagiario_id');
    }

    public function supervisor() {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function tutor() {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function instituicao() {
        return $this->belongsTo(Instituicao::class);
    }

    public function curso() {
        return $this->belongsTo(Curso::class);
    }

    public function documentos() {
        return $this->hasMany(Documento::class);
    }

    public function avaliacoes() {
        return $this->hasMany(Avaliacao::class);
    }
}