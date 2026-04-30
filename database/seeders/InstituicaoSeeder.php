<?php

namespace Database\Seeders;

use App\Models\Instituicao;
use Illuminate\Database\Seeder;

class InstituicaoSeeder extends Seeder
{
    public function run(): void
    {
        $instituicoes = [
            [
                'nome' => 'Banco de Moçambique',
                'nuit' => '400123456',
                'endereco' => 'Av. 25 de Setembro, Maputo',
                'telefone' => '841234567',
                'email' => 'contacto@bm.co.mz',
                'ponto_focal_nome' => 'Carlos Matola',
                'ponto_focal_contacto' => '842111111',
                'status' => 'ativa',
                'validade_parceria' => now()->addYear(),
            ],
            [
                'nome' => 'Vodacom Moçambique',
                'nuit' => '400987654',
                'endereco' => 'Av. Eduardo Mondlane, Maputo',
                'telefone' => '843456789',
                'email' => 'info@vodacom.co.mz',
                'ponto_focal_nome' => 'Ana Chissano',
                'ponto_focal_contacto' => '843222222',
                'status' => 'ativa',
                'validade_parceria' => now()->addYears(2),
            ],
            [
                'nome' => 'INCM',
                'nuit' => '401112223',
                'endereco' => 'Av. Vladimir Lenine, Maputo',
                'telefone' => '844567890',
                'email' => 'geral@incm.gov.mz',
                'ponto_focal_nome' => 'João Mabunda',
                'ponto_focal_contacto' => '844333333',
                'status' => 'ativa',
                'validade_parceria' => now()->addMonths(6),
            ],
            [
                'nome' => 'Empresa Consultoria & Auditoria Lda',
                'nuit' => '401234567',
                'endereco' => 'Rua Samora Machel, 1500, Maputo',
                'telefone' => '845678901',
                'email' => 'estagiarios@eca.co.mz',
                'ponto_focal_nome' => 'Sofia Nkomo',
                'ponto_focal_contacto' => '845444444',
                'status' => 'ativa',
                'validade_parceria' => now()->addYears(3),
            ],
            [
                'nome' => 'Câmara Municipal de Maputo',
                'nuit' => '401345678',
                'endereco' => 'Av. 24 de Julho, Maputo',
                'telefone' => '216666666',
                'email' => 'cmaputo@gmail.com',
                'ponto_focal_nome' => 'Manuel Mulhovo',
                'ponto_focal_contacto' => '846555555',
                'status' => 'suspensa',
                'validade_parceria' => now()->subMonths(2),
            ],
        ];

        foreach ($instituicoes as $instituicao) {
            Instituicao::firstOrCreate(['nuit' => $instituicao['nuit']], $instituicao);
        }
    }
}