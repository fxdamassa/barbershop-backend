<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicosSeeder extends Seeder
{
    public function run(): void
    {
        $servicos = [
            ['codigo' => 'CMQ', 'servico' => 'Corte de Máquina'],
            ['codigo' => 'CTS', 'servico' => 'Corte de Tesoura'],
            ['codigo' => 'BAR', 'servico' => 'Barba'],
            ['codigo' => 'SOB', 'servico' => 'Sobrancelha'],
            ['codigo' => 'NAV', 'servico' => 'Corte Navalhado'],
            ['codigo' => 'PEC', 'servico' => 'Pé de Cabelo'],
            ['codigo' => 'PIG', 'servico' => 'Pigmentação'],
        ];

        DB::table('servicos')->insert($servicos);
    }
}
