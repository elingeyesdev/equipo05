<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FocosIncendio;
use Carbon\Carbon;

class FocosIncendioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $focos = [
            [
                'fecha' => Carbon::now()->subDays(2),
                'ubicacion' => 'Zona Norte - San José de Chiquitos',
                'coordenadas' => [-17.75, -61.45],
                'intensidad' => 7.5,
            ],
            [
                'fecha' => Carbon::now()->subDays(1),
                'ubicacion' => 'Bosque Chiquitano - Sector A',
                'coordenadas' => [-17.82, -61.52],
                'intensidad' => 9.0,
            ],
            [
                'fecha' => Carbon::now()->subHours(12),
                'ubicacion' => 'Pastizal Este',
                'coordenadas' => [-17.78, -61.48],
                'intensidad' => 5.5,
            ],
            [
                'fecha' => Carbon::now()->subHours(6),
                'ubicacion' => 'Zona Sur - Límite Municipal',
                'coordenadas' => [-17.85, -61.55],
                'intensidad' => 6.8,
            ],
            [
                'fecha' => Carbon::now()->subHours(3),
                'ubicacion' => 'Matorral Centro',
                'coordenadas' => [-17.80, -61.50],
                'intensidad' => 4.2,
            ],
        ];

        foreach ($focos as $foco) {
            FocosIncendio::create($foco);
        }

        $this->command->info('5 focos de incendio de ejemplo creados exitosamente!');
    }
}
