<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RichTransparenciaDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('transparencia')->hasTable('campanias')) {
            $this->command?->warn('Transparencia: ejecuta php artisan db:setup-transparencia');

            return;
        }

        $db = DB::connection('transparencia');
        $creatorId = DB::connection('core')->table('usuarios')->value('usuarioid') ?? 1;

        if ($db->table('estados')->count() === 0) {
            foreach (['Pendiente', 'Confirmada', 'Asignada', 'Entregada'] as $nombre) {
                $db->table('estados')->insert(['nombre' => $nombre, 'descripcion' => 'Estado demo']);
            }
        }

        $estadoId = $db->table('estados')->value('estadoid') ?? 1;
        $now = Carbon::now();

        $campanias = [
            ['titulo' => 'Emergencia Chiquitania 2026', 'meta' => 50000],
            ['titulo' => 'Agua potable zonas afectadas', 'meta' => 25000],
            ['titulo' => 'Alimentos familias evacuadas', 'meta' => 35000],
            ['titulo' => 'Kit médico brigadas', 'meta' => 18000],
            ['titulo' => 'Reforestación post incendio', 'meta' => 42000],
            ['titulo' => 'Mantas y abrigo invierno', 'meta' => 15000],
            ['titulo' => 'Combustible transporte solidario', 'meta' => 22000],
            ['titulo' => 'Herramientas comunarios', 'meta' => 12000],
        ];

        foreach ($campanias as $i => $c) {
            $exists = $db->table('campanias')->where('titulo', $c['titulo'])->exists();
            if ($exists) {
                continue;
            }

            $campaniaId = $db->table('campanias')->insertGetId([
                'titulo' => $c['titulo'],
                'descripcion' => 'Campaña de transparencia demo #'.($i + 1).' para seguimiento público de fondos.',
                'fechainicio' => $now->copy()->subDays(40 - $i * 3)->toDateString(),
                'fechafin' => $now->copy()->addDays(60 + $i * 5)->toDateString(),
                'metarecaudacion' => $c['meta'],
                'montorecaudado' => rand(2000, (int) ($c['meta'] * 0.7)),
                'usuarioidcreador' => $creatorId,
                'activa' => true,
                'imagenurl' => 'https://picsum.photos/seed/camp'.($i + 1).'/800/400',
                'fechacreacion' => $now,
            ], 'campaniaid');

            for ($d = 0; $d < 8; $d++) {
                $db->table('donaciones')->insert([
                    'usuarioid' => $creatorId,
                    'campaniaid' => $campaniaId,
                    'monto' => rand(50, 2500),
                    'tipodonacion' => $d % 3 === 0 ? 'especie' : 'dinero',
                    'descripcion' => 'Donación solidaria registrada en demo.',
                    'fechadonacion' => $now->copy()->subDays(rand(1, 35)),
                    'estadoid' => $estadoId,
                    'esanonima' => $d % 4 === 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if (Schema::connection('transparencia')->hasTable('asignaciones')) {
                $db->table('asignaciones')->insert([
                    'campaniaid' => $campaniaId,
                    'descripcion' => 'Compra de insumos prioritarios zona '.($i + 1),
                    'monto' => rand(800, 5000),
                    'fechaasignacion' => $now->copy()->subDays(rand(2, 20)),
                    'usuarioid' => $creatorId,
                    'imagenurl' => 'https://picsum.photos/seed/asig'.($i + 1).'/600/400',
                ]);
            }
        }

        $this->command?->info('Transparencia: campañas, donaciones y asignaciones demo ampliadas.');
    }
}
