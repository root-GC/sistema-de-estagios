<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Sistema',
            'email' => 'admin@pep.com',
            'password' => Hash::make('123456'),
            'ativo' => true
        ]);
        $admin->assignRole('ADMIN');

        $estagiario = User::create([
            'name' => 'Estagiário Teste',
            'email' => 'estagiario@pep.com',
            'password' => Hash::make('123456'),
            'ativo' => true
        ]);
        $estagiario->assignRole('ESTAGIARIO');

        $supervisor = User::create([
            'name' => 'Supervisor Teste',
            'email' => 'supervisor@pep.com',
            'password' => Hash::make('123456'),
            'ativo' => true
        ]);
        $supervisor->assignRole('SUPERVISOR');

        $tutor = User::create([
            'name' => 'Tutor Teste',
            'email' => 'tutor@pep.com',
            'password' => Hash::make('123456'),
            'ativo' => true
        ]);
        $tutor->assignRole('TUTOR');

        $coordenador = User::create([
            'name' => 'Coordenador Teste',
            'email' => 'coordenador@pep.com',
            'password' => Hash::make('123456'),
            'ativo' => true
        ]);
        $coordenador->assignRole('COORDENADOR');

        $chefe = User::create([
            'name' => 'Chefe Teste',
            'email' => 'chefe@pep.com',
            'password' => Hash::make('123456'),
            'ativo' => true
        ]);
        $chefe->assignRole('CHEFE_REPARTICAO');
    }
}