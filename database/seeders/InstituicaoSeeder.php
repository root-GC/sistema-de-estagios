<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstituicaoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('instituicoes')->insert([
            [
                'nome' => 'Banco de Moçambique',
                'nuit' => '400123456',
                'endereco' => 'Av. 25 de Setembro, Maputo',
                'telefone' => '841234567',
                'email' => 'contacto@bm.co.mz',
                'ponto_focal_nome' => 'Carlos Matola',
                'ponto_focal_contacto' => '842111111',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Vodacom Moçambique',
                'nuit' => '400987654',
                'endereco' => 'Av. Eduardo Mondlane, Maputo',
                'telefone' => '843456789',
                'email' => 'info@vodacom.co.mz',
                'ponto_focal_nome' => 'Ana Chissano',
                'ponto_focal_contacto' => '843222222',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'INCM',
                'nuit' => '401112223',
                'endereco' => 'Av. Vladimir Lenine, Maputo',
                'telefone' => '844567890',
                'email' => 'geral@incm.gov.mz',
                'ponto_focal_nome' => 'João Mabunda',
                'ponto_focal_contacto' => '844333333',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}