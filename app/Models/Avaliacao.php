<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Avaliacao extends Model
{
    use HasFactory;

    protected $fillable = [

        'estagio_id',

        'avaliador_id',

        'tipo',

        'nota',

        'comentario'
    ];

    public function estagio()
    {
        return $this->belongsTo(Estagio::class);
    }

    public function avaliador()
    {
        return $this->belongsTo(User::class, 'avaliador_id');
    }
}