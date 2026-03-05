<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;

    protected $fillable = ['estagio_id', 'tutor_id', 'nota', 'comentario'];

    public function estagio() {
        return $this->belongsTo(Estagio::class);
    }

    public function tutor() {
        return $this->belongsTo(User::class, 'tutor_id');
    }
}