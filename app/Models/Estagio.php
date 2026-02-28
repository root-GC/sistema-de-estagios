<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estagio extends Model
{
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

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
}
