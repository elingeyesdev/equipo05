<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RichIncendiosDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('incendios')->hasTable('tipo_biomasa')) {
            $this->command?->warn('Incendios: esquema no disponible.');

            return;
        }

        $previous = config('database.default');
        $cacheDriver = config('cache.default');
        config(['database.default' => 'incendios', 'cache.default' => 'array']);

        try {
            foreach ([
                \Modules\Incendios\Seeders\ShowcaseDataSeeder::class,
                \Modules\Incendios\Database\Seeders\DemoDataSeeder::class,
            ] as $seederClass) {
                if (! class_exists($seederClass)) {
                    continue;
                }
                try {
                    $instance = new $seederClass;
                    if ($this->command && method_exists($instance, 'setCommand')) {
                        $instance->setCommand($this->command);
                    }
                    $instance->run();
                } catch (\Throwable $e) {
                    $this->command?->warn("Incendios {$seederClass} omitido: ".$e->getMessage());
                }
            }

            if (class_exists(\Modules\Incendios\Database\Seeders\FocosIncendioSeeder::class)
                && DB::connection('incendios')->table('focos_incendios')->count() < 5) {
                try {
                    $this->call(\Modules\Incendios\Database\Seeders\FocosIncendioSeeder::class);
                } catch (\Throwable $e) {
                    $this->command?->warn('FocosIncendioSeeder omitido: '.$e->getMessage());
                }
            }

            $this->seedFocosExtra();
            $this->seedBiomasaExtra();
        } finally {
            config(['database.default' => $previous, 'cache.default' => $cacheDriver]);
        }

        $this->command?->info('Incendios: usuarios, biomasa, focos y simulaciones demo ampliados.');
    }

    private function seedFocosExtra(): void
    {
        $db = DB::connection('incendios');
        if (! Schema::connection('incendios')->hasTable('focos_incendios')) {
            return;
        }

        $zonas = [
            'San José de Chiquitos', 'Concepción', 'San Ignacio', 'San Rafael',
            'Villa Montes', 'Yacuiba', 'Roboré', 'Puerto Suárez', 'Ascensión',
            'Charagua', 'Camiri', 'San Matías', 'Lagunillas', 'Pailón', 'Montero',
        ];

        $now = Carbon::now();
        foreach ($zonas as $i => $zona) {
            $ubicacion = 'Foco demo '.$zona;
            if ($db->table('focos_incendios')->where('ubicacion', $ubicacion)->exists()) {
                continue;
            }

            $db->table('focos_incendios')->insert([
                'fecha' => $now->copy()->subHours(rand(2, 240)),
                'ubicacion' => $ubicacion,
                'coordenadas' => json_encode([-17.5 - ($i * 0.02), -61.4 - ($i * 0.015)]),
                'intensidad' => round(rand(30, 95) / 10, 1),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedBiomasaExtra(): void
    {
        $db = DB::connection('incendios');
        if (! Schema::connection('incendios')->hasTable('biomasas')) {
            return;
        }

        $tipoId = $db->table('tipo_biomasa')->value('id');
        if (! $tipoId) {
            return;
        }

        $userId = DB::connection('core')->table('usuarios')->value('usuarioid');

        for ($b = 1; $b <= 12; $b++) {
            $ubicacion = 'Polígono biomasa demo '.$b;
            if ($db->table('biomasas')->where('ubicacion', $ubicacion)->exists()) {
                continue;
            }

            $db->table('biomasas')->insert([
                'ubicacion' => $ubicacion,
                'descripcion' => 'Registro de biomasa demo para mapa y validación.',
                'densidad' => ['baja', 'media', 'alta'][rand(0, 2)],
                'tipo_biomasa_id' => $tipoId,
                'user_id' => $userId,
                'coordenadas' => json_encode([
                    [-17.7 + $b * 0.01, -63.1 + $b * 0.01],
                    [-17.71 + $b * 0.01, -63.09 + $b * 0.01],
                    [-17.72 + $b * 0.01, -63.11 + $b * 0.01],
                ]),
                'estado' => ['pendiente', 'aprobada', 'rechazada'][rand(0, 2)],
                'fecha_reporte' => now()->subDays(rand(1, 30))->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
