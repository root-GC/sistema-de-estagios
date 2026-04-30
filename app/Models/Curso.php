<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Curso extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_id',
        'nome',
        'descricao',
        'duracao_anos'
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function estudantes()
    {
        return $this->hasMany(StudentProfile::class);
    }

    public function estagios()
    {
        return $this->hasMany(Estagio::class);
    }
}