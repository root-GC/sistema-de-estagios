<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notificacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titulo',
        'mensagem',
        'lida_em'
    ];

    protected $casts = [
        'lida_em' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}