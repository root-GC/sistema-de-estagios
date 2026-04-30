<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'estagio_id',
        'tipo',
        'ficheiro',
        'estado',
        'comentario_supervisor',
        'submetido_em'
    ];

    protected $casts = [
        'submetido_em' => 'datetime'
    ];

    public function estagio()
    {
        return $this->belongsTo(Estagio::class);
    }
}