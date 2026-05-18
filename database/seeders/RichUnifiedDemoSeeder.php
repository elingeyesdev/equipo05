<?php

namespace Database\Seeders;

use App\Support\UnifiedPostgres;
use Illuminate\Database\Seeder;

class RichUnifiedDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! UnifiedPostgres::enabled()) {
            $this->command?->warn('RichUnifiedDemoSeeder requiere DATABASE_UNIFIED_POSTGRES=true');

            return;
        }

        $this->command?->info('Sembrando datos demo abundantes en los 7 modulos...');

        $this->call([
            RichTransparenciaDemoSeeder::class,
            RichInventarioDemoSeeder::class,
            RichIncendiosDemoSeeder::class,
            RichRescateDemoSeeder::class,
            RichLogisticaDemoSeeder::class,
            RichSeguimientoDemoSeeder::class,
            RichCuadrillasDemoSeeder::class,
        ]);

        $this->command?->info('Datos demo abundantes listos.');
    }
}
