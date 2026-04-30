<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Departamento;
use Illuminate\Database\Seeder;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = Departamento::all();

        if ($departamentos->isEmpty()) {
            $this->call(DepartamentoSeeder::class);
            $departamentos = Departamento::all();
        }

        $cursos = [
            ['nome' => 'Licenciatura em Engenharia Informática', 'departamento' => 'Engenharia Informática', 'duracao_anos' => 4],
            ['nome' => 'Mestrado em Engenharia Informática', 'departamento' => 'Engenharia Informática', 'duracao_anos' => 2],
            ['nome' => 'Licenciatura em Gestão', 'departamento' => 'Gestão', 'duracao_anos' => 3],
            ['nome' => 'Mestrado em Gestão Empresarial', 'departamento' => 'Gestão', 'duracao_anos' => 2],
            ['nome' => 'Licenciatura em Engenharia Civil', 'departamento' => 'Engenharia Civil', 'duracao_anos' => 4],
            ['nome' => 'Licenciatura em Biologia', 'departamento' => 'Ciências Naturais', 'duracao_anos' => 3],
            ['nome' => 'Licenciatura em Sociologia', 'departamento' => 'Humanas', 'duracao_anos' => 3],
        ];

        foreach ($cursos as $curso) {
            $departamento = $departamentos->firstWhere('nome', $curso['departamento']);
            
            if ($departamento) {
                Curso::firstOrCreate(
                    ['nome' => $curso['nome']],
                    [
                        'departamento_id' => $departamento->id,
                        'duracao_anos' => $curso['duracao_anos'],
                        'descricao' => "Curso de {$curso['nome']}",
                    ]
                );
            }
        }
    }
}