<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Notificacao;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'ativo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PERFIS
    |--------------------------------------------------------------------------
    */

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ESTÁGIOS
    |--------------------------------------------------------------------------
    */

    public function estagiosComoEstagiario()
    {
        return $this->hasMany(Estagio::class, 'estagiario_id');
    }

    public function estagiosComoSupervisor()
    {
        return $this->hasMany(Estagio::class, 'supervisor_id');
    }

    public function estagiosComoTutor()
    {
        return $this->hasMany(Estagio::class, 'tutor_id');
    }

    public function estagiosComoCoordenador()
    {
        return $this->hasMany(Estagio::class, 'coordenador_id');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGS
    |--------------------------------------------------------------------------
    */

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
    public function notifications()
{
    return $this->hasMany(Notificacao::class);
}
}