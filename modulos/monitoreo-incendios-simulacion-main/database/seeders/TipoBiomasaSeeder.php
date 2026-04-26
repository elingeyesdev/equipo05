<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoBiomasa;

class TipoBiomasaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'tipo_biomasa' => 'Pastizal',
                'color' => '#90EE90',
                'modificador_intensidad' => 1.0
            ],
            [
                'tipo_biomasa' => 'Arbustal',
                'color' => '#228B22',
                'modificador_intensidad' => 1.2
            ],
            [
                'tipo_biomasa' => 'Bosque',
                'color' => '#006400',
                'modificador_intensidad' => 1.5
            ],
            [
                'tipo_biomasa' => 'Agrícola',
                'color' => '#FFD700',
                'modificador_intensidad' => 0.8
            ],
            [
                'tipo_biomasa' => 'Urbano',
                'color' => '#808080',
                'modificador_intensidad' => 0.5
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoBiomasa::firstOrCreate(
                ['tipo_biomasa' => $tipo['tipo_biomasa']],
                $tipo
            );
        }

        $this->command->info('Tipos de biomasa creados exitosamente!');
    }
}
