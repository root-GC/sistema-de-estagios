<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = [
            ['nome' => 'Engenharia Informática', 'descricao' => 'Departamento de Engenharia Informática'],
            ['nome' => 'Gestão', 'descricao' => 'Departamento de Gestão'],
            ['nome' => 'Engenharia Civil', 'descricao' => 'Departamento de Engenharia Civil'],
            ['nome' => 'Ciências Naturais', 'descricao' => 'Departamento de Ciências Naturais'],
            ['nome' => 'Humanas', 'descricao' => 'Departamento de Ciências Humanas e Sociais'],
        ];

        foreach ($departamentos as $d) {
            Departamento::firstOrCreate(['nome' => $d['nome']], $d);
        }
    }
}
