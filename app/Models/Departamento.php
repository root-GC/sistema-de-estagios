<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao'
    ];

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }

    public function staffs()
    {
        return $this->hasMany(StaffProfile::class);
    }
}