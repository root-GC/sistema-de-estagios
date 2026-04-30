<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Criar admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@pep.com'],
            [
                'name' => 'Admin Sistema',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Criar usuários teste de cada role
        $testUsers = [
            ['name' => 'Estagiário Teste', 'email' => 'estagiario@pep.com', 'role' => 'estudante'],
            ['name' => 'Supervisor Teste', 'email' => 'supervisor@pep.com', 'role' => 'supervisor'],
            ['name' => 'Tutor Teste', 'email' => 'tutor@pep.com', 'role' => 'tutor'],
            ['name' => 'Coordenador Teste', 'email' => 'coordenador@pep.com', 'role' => 'coordenador'],
            ['name' => 'Chefe Teste', 'email' => 'chefe@pep.com', 'role' => 'chefe_repartição'],
        ];

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($userData['role']);
        }
    }
}