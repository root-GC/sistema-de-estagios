<?php

namespace Database\Seeders;

use App\Models\StaffProfile;
use App\Models\User;
use App\Models\Departamento;
use Illuminate\Database\Seeder;

class StaffProfileSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = Departamento::all();

        // Criar chefes de repartição
        foreach ($departamentos as $departamento) {
            $user = User::factory()->create(['name' => 'Chefe ' . $departamento->nome]);
            $user->assignRole('chefe_repartição');
            
            StaffProfile::create([
                'user_id' => $user->id,
                'departamento_id' => $departamento->id,
                'cargo' => 'Chefe de Repartição',
                'telefone' => '8' . rand(10000000, 99999999),
            ]);
        }

        // Criar coordenadores (2 por departamento)
        foreach ($departamentos as $departamento) {
            for ($i = 1; $i <= 2; $i++) {
                $user = User::factory()->create(['name' => 'Coordenador ' . $departamento->nome . ' ' . $i]);
                $user->assignRole('coordenador');
                
                StaffProfile::create([
                    'user_id' => $user->id,
                    'departamento_id' => $departamento->id,
                    'cargo' => 'Coordenador de Curso',
                    'telefone' => '8' . rand(10000000, 99999999),
                ]);
            }
        }

        // Criar tutores (3 por departamento)
        foreach ($departamentos as $departamento) {
            for ($i = 1; $i <= 3; $i++) {
                $user = User::factory()->create(['name' => 'Tutor ' . $departamento->nome . ' ' . $i]);
                $user->assignRole('tutor');
                
                StaffProfile::create([
                    'user_id' => $user->id,
                    'departamento_id' => $departamento->id,
                    'cargo' => 'Tutor Académico',
                    'telefone' => '8' . rand(10000000, 99999999),
                ]);
            }
        }

        // Criar supervisores externos (não tem departamento)
        for ($i = 1; $i <= 5; $i++) {
            $user = User::factory()->create(['name' => 'Supervisor Externo ' . $i]);
            $user->assignRole('supervisor');
            
            StaffProfile::create([
                'user_id' => $user->id,
                'departamento_id' => null,
                'cargo' => 'Supervisor de Estágio (Externo)',
                'telefone' => '8' . rand(10000000, 99999999),
            ]);
        }
    }
}
