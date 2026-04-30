<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'numero_estudante',
        'curso_id',
        'ano_academico',
        'media',
        'elegivel_estagio'
    ];

    protected $casts = [
        'elegivel_estagio' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function estagios()
    {
        return $this->hasMany(Estagio::class, 'estagiario_id');
    }
}