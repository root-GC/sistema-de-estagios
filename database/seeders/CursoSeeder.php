<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cursos')->insert([
            [
                'nome' => 'Licenciatura em Informática',
                'descricao' => 'Curso focado em desenvolvimento de software e sistemas de informação.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Engenharia de Redes',
                'descricao' => 'Curso focado em redes de computadores e infraestrutura.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Sistemas de Informação',
                'descricao' => 'Curso focado em gestão e desenvolvimento de sistemas empresariais.',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}