<?php

namespace Database\Seeders;

use App\Models\StudentProfile;
use App\Models\User;
use App\Models\Curso;
use Illuminate\Database\Seeder;

class StudentProfileSeeder extends Seeder
{
    public function run(): void
    {
        $cursos = Curso::all();

        // Criar 5 estudantes por curso
        foreach ($cursos->take(3) as $curso) {
            for ($i = 1; $i <= 5; $i++) {
                $user = User::factory()->create([
                    'name' => 'Estudante ' . $curso->nome . ' ' . $i
                ]);
                $user->assignRole('estudante');

                StudentProfile::create([
                    'user_id' => $user->id,
                    'numero_estudante' => 'EST-' . $curso->id . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'curso_id' => $curso->id,
                    'ano_academico' => '2025/2026',
                    'media' => rand(12, 18),
                    'elegivel_estagio' => rand(0, 1) === 1,
                ]);
            }
        }
    }
}
