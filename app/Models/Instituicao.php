<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instituicao extends Model
{
    use HasFactory;

    protected $table = 'instituicoes';

    protected $fillable = [
        'nome',
        'nuit',
        'endereco',
        'telefone',
        'email',

        'ponto_focal_nome',
        'ponto_focal_contacto',

        'status',
        'validade_parceria'
    ];

    protected $casts = [
        'validade_parceria' => 'date'
    ];

    public function estagios()
    {
        return $this->hasMany(Estagio::class);
    }
}