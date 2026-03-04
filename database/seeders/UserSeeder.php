<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Estagiário Teste',
            'email' => 'estagiario@pep.com',
            'password' => Hash::make('123456'),
            'role' => 'ESTAGIARIO',
            'ativo' => true
        ]);

        User::create([
            'name' => 'Supervisor Teste',
            'email' => 'supervisor@pep.com',
            'password' => Hash::make('123456'),
            'role' => 'SUPERVISOR',
            'ativo' => true
        ]);

        User::create([
            'name' => 'Tutor Teste',
            'email' => 'tutor@pep.com',
            'password' => Hash::make('123456'),
            'role' => 'TUTOR',
            'ativo' => true
        ]);

        User::create([
            'name' => 'Coordenador Teste',
            'email' => 'coordenador@pep.com',
            'password' => Hash::make('123456'),
            'role' => 'COORDENADOR',
            'ativo' => true
        ]);

        User::create([
            'name' => 'Chefe Teste',
            'email' => 'chefe@pep.com',
            'password' => Hash::make('123456'),
            'role' => 'CHEFE_REPARTICAO',
            'ativo' => true
        ]);
    }
}