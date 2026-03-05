<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instituicao extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'nuit', 'endereco', 'telefone', 'email',
        'ponto_focal_nome', 'ponto_focal_contacto'
    ];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function estagios() {
        return $this->hasMany(Estagio::class);
    }
}