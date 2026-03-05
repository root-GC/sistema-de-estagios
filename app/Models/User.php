<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;


    public function estagio()
    {
        return $this->hasOne(Estagio::class, 'estagiario_id');
    }

    public function estagiosSupervisionados()
    {
        return $this->hasMany(Estagio::class, 'supervisor_id');
    }

     public function curso() {
        return $this->belongsTo(Curso::class);
    }

    public function instituicao() {
        return $this->belongsTo(Instituicao::class);
    }

    public function estagios() {
        return $this->hasMany(Estagio::class, 'estagiario_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
   protected $fillable = [
        'name', 'email', 'password', 'role', 'curso_id', 'instituicao_id', 'ativo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
