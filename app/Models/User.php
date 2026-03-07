<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // ✅ Import Spatie

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles; // ✅ Adiciona HasRoles

    // =========================
    // RELAÇÕES
    // =========================
    public function estagio()
    {
        return $this->hasOne(Estagio::class, 'estagiario_id');
    }

    public function estagiosSupervisionados()
    {
        return $this->hasMany(Estagio::class, 'supervisor_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function instituicao()
    {
        return $this->belongsTo(Instituicao::class);
    }

    public function estagios()
    {
        return $this->hasMany(Estagio::class, 'estagiario_id');
    }

    // =========================
    // MASS ASSIGNABLE
    // =========================
    protected $fillable = [
        'name', 'email', 'password', 'curso_id', 'instituicao_id', 'ativo'
    ]; // ✅ remove 'role'

    // =========================
    // HIDDEN
    // =========================
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // =========================
    // CASTS
    // =========================
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}