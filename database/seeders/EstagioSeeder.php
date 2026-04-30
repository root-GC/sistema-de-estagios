<?php

namespace Database\Seeders;

use App\Models\Estagio;
use App\Models\StudentProfile;
use App\Models\Instituicao;
use App\Models\User;
use Illuminate\Database\Seeder;

class EstagioSeeder extends Seeder
{
    public function run(): void
    {
        $instituicoes = Instituicao::all();
        $supervisores = User::whereHas('roles', fn($q) => $q->where('name', 'supervisor'))->get();
        $tutores = User::whereHas('roles', fn($q) => $q->where('name', 'tutor'))->get();
        $coordenadores = User::whereHas('roles', fn($q) => $q->where('name', 'coordenador'))->get();
        $estudantes = StudentProfile::all();

        if ($estudantes->isEmpty() || $supervisores->isEmpty() || $tutores->isEmpty()) {
            return;
        }

        // Criar estágios para estudantes elegíveis
        foreach ($estudantes->where('elegivel_estagio', true)->take(10) as $estudante) {
            $supervisor = $supervisores->random();
            $tutor = $tutores->random();
            $coordenador = $coordenadores->random();
            $instituicao = $instituicoes->where('status', 'ativa')->random();

            Estagio::create([
                'estagiario_id' => $estudante->user_id,
                'supervisor_id' => $supervisor->id,
                'tutor_id' => $tutor->id,
                'coordenador_id' => $coordenador->id,
                'instituicao_id' => $instituicao->id,
                'curso_id' => $estudante->curso_id,
                'estado' => 'EM_ANDAMENTO',
                'data_inicio' => now()->subMonths(2),
                'data_fim' => now()->addMonths(4),
                'nota_final' => null,
            ]);
        }

        // Criar alguns estágios concluídos
        foreach ($estudantes->where('elegivel_estagio', true)->skip(10)->take(5) as $estudante) {
            $supervisor = $supervisores->random();
            $tutor = $tutores->random();
            $coordenador = $coordenadores->random();
            $instituicao = $instituicoes->where('status', 'ativa')->random();

            Estagio::create([
                'estagiario_id' => $estudante->user_id,
                'supervisor_id' => $supervisor->id,
                'tutor_id' => $tutor->id,
                'coordenador_id' => $coordenador->id,
                'instituicao_id' => $instituicao->id,
                'curso_id' => $estudante->curso_id,
                'estado' => 'CONCLUIDO',
                'data_inicio' => now()->subMonths(8),
                'data_fim' => now()->subMonths(2),
                'nota_final' => rand(14, 18),
            ]);
        }
    }
}
