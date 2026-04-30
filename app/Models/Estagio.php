<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estagio extends Model
{
    use HasFactory;

    protected $fillable = [

        'estagiario_id',
        'supervisor_id',
        'tutor_id',
        'coordenador_id',

        'instituicao_id',
        'curso_id',

        'estado',

        'data_inicio',
        'data_fim',

        'nota_final'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'nota_final' => 'float'
    ];

    /*
    |--------------------------------------------------------------------------
    | PARTICIPANTES
    |--------------------------------------------------------------------------
    */

    public function estagiario()
    {
        return $this->belongsTo(User::class, 'estagiario_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function coordenador()
    {
        return $this->belongsTo(User::class, 'coordenador_id');
    }

    /*
    |--------------------------------------------------------------------------
    | CONTEXTO
    |--------------------------------------------------------------------------
    */

    public function instituicao()
    {
        return $this->belongsTo(Instituicao::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS
    |--------------------------------------------------------------------------
    */

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    /*
    |--------------------------------------------------------------------------
    | AVALIAÇÕES
    |--------------------------------------------------------------------------
    */

    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class);
    }
}