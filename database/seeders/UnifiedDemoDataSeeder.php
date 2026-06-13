<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UnifiedDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (filter_var(env('DATABASE_UNIFIED_POSTGRES', false), FILTER_VALIDATE_BOOL)) {
            $this->seedInventario();
            $this->seedIncendios();
            $this->seedRescate();
            $this->seedLogistica();
            $this->seedSeguimiento();
            $this->seedCuadrillas();
        }

        $this->command?->info('Datos demo insertados (conexiones unificadas PostgreSQL).');
    }

    private function seedInventario(): void
    {
        if (! Schema::connection('inventario')->hasTable('donaciones')) {
            $this->command?->warn('Inventario: ejecuta primero php artisan db:setup-inventario --fresh');

            return;
        }

        $db = DB::connection('inventario');
        if ($db->table('donaciones')->count() > 0) {
            $this->seedInventarioExtras($db);

            return;
        }

        $now = Carbon::now();
        $catId = $db->table('categorias_productos')->insertGetId([
            'nombre' => 'Alimentos no perecederos',
        ], 'id_categoria');

        $prodId = $db->table('productos')->insertGetId([
            'id_categoria' => $catId,
            'nombre' => 'Arroz 1kg',
            'descripcion' => 'Bolsa arroz',
            'unidad_medida' => 'kg',
        ], 'id_producto');

        $campId = $db->table('campanas')->insertGetId([
            'nombre' => 'Emergencia Chiquitania 2026',
            'descripcion' => 'Campaña demo integración',
            'fecha_inicio' => $now->copy()->subMonths(2)->toDateString(),
            'fecha_fin' => $now->copy()->addMonths(4)->toDateString(),
        ], 'id_campana');

        $donanteId = $db->table('donantes')->insertGetId([
            'nombre' => 'María Lopez',
            'tipo' => 'persona',
            'email' => 'maria.lopez@demo.local',
            'telefono' => '70000001',
            'fecha_registro' => $now,
        ], 'id_donante');

        $donId = $db->table('donaciones')->insertGetId([
            'id_donante' => $donanteId,
            'tipo' => 'especie',
            'fecha' => $now,
            'id_campana' => $campId,
            'observaciones' => 'Donación demo',
        ], 'id_donacion');

        $db->table('donacion_detalles')->insert([
            'id_donacion' => $donId,
            'id_producto' => $prodId,
            'cantidad' => 50,
        ]);

        $db->table('donaciones_dinero')->insert([
            'id_donacion' => $db->table('donaciones')->insertGetId([
                'id_donante' => $donanteId,
                'tipo' => 'dinero',
                'fecha' => $now->copy()->subDays(3),
                'id_campana' => $campId,
            ], 'id_donacion'),
            'monto' => 1500.00,
            'metodo_pago' => 'transferencia',
        ]);

        $db->table('paquetes')->insert([
            'codigo_paquete' => 'PKG-DEMO-001',
            'estado' => 'pendiente',
            'fecha_creacion' => $now,
        ]);

        $db->table('solicitudes_recoleccion')->insert([
            'id_donante' => $donanteId,
            'direccion_recoleccion' => 'Av. Demo 123, Santa Cruz',
            'fecha_programada' => $now->copy()->addDays(2),
            'observaciones' => 'Recolección demo',
            'estado' => 'pendiente',
        ]);
    }

    private function seedInventarioExtras($db): void
    {
        if ($db->table('paquetes')->where('codigo_paquete', 'PKG-DEMO-001')->doesntExist()) {
            $db->table('paquetes')->insert([
                'codigo_paquete' => 'PKG-DEMO-001',
                'estado' => 'pendiente',
                'fecha_creacion' => Carbon::now(),
            ]);
        }

        if ($db->table('solicitudes_recoleccion')->where('estado', 'pendiente')->count() === 0
            && $db->table('donantes')->exists()) {
            $donanteId = $db->table('donantes')->value('id_donante');
            $db->table('solicitudes_recoleccion')->insert([
                'id_donante' => $donanteId,
                'direccion_recoleccion' => 'Av. Demo 123, Santa Cruz',
                'fecha_programada' => Carbon::now()->addDays(2),
                'observaciones' => 'Recolección demo',
                'estado' => 'pendiente',
            ]);
        }
    }

    private function seedIncendios(): void
    {
        $db = DB::connection('incendios');
        if (! Schema::connection('incendios')->hasTable('tipo_biomasa')) {
            return;
        }
        if ($db->table('tipo_biomasa')->count() > 0) {
            return;
        }

        foreach (['Bosque seco', 'Sabana', 'Pastizal', 'Chaco'] as $nombre) {
            $db->table('tipo_biomasa')->insert([
                'tipo_biomasa' => $nombre,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedRescate(): void
    {
        if (! Schema::connection('rescate')->hasTable('animals')) {
            $this->command?->warn('Rescate: ejecuta migraciones del modulo (rescate) antes del seed.');

            return;
        }

        if (DB::connection('rescate')->table('animals')->count() >= 3) {
            return;
        }

        $previousDefault = config('database.default');
        config(['database.default' => \App\Support\UnifiedPostgres::coreAuthConnection()]);

        try {
            $this->resetRescatePgSequences();

            require_once base_path('modulos/rescate-animales-silvestres-main/database/seeders/RolesAndPermissionsSeeder.php');
            (new \Modules\Rescate\Database\Seeders\RolesAndPermissionsSeeder)->run();

            $showcase = new \Modules\Rescate\Seeders\ShowcaseDataSeeder;
            if ($this->command) {
                $showcase->setCommand($this->command);
            }
            $showcase->run();

            $this->command?->info('Rescate: catalogos, usuarios demo y flujo completo (reportes, animales, cuidados) cargados.');
        } finally {
            config(['database.default' => $previousDefault]);
        }
    }

    private function resetRescatePgSequences(): void
    {
        if (DB::connection('rescate')->getDriverName() !== 'pgsql') {
            return;
        }

        foreach (['people', 'centers', 'species', 'reports', 'animals', 'animal_files'] as $table) {
            $row = DB::connection('rescate')->selectOne(
                "SELECT pg_get_serial_sequence(?, 'id') AS seq",
                [$table]
            );
            if (! empty($row?->seq)) {
                DB::connection('rescate')->statement(
                    "SELECT setval(?, (SELECT COALESCE(MAX(id), 1) FROM {$table}), true)",
                    [$row->seq]
                );
            }
        }
    }

    private function seedLogistica(): void
    {
        $db = DB::connection('logistica');
        if (! Schema::connection('logistica')->hasTable('estado')) {
            return;
        }
        if ($db->table('estado')->count() > 0) {
            return;
        }

        $db->table('estado')->insert([
            ['nombre_estado' => 'Pendiente', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_estado' => 'En tránsito', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_estado' => 'Entregado', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedSeguimiento(): void
    {
        $db = DB::connection('seguimiento');
        if (! Schema::connection('seguimiento')->hasTable('usuario')) {
            return;
        }
        if ($db->table('usuario')->count() > 0) {
            return;
        }

        $db->table('usuario')->insert([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan.voluntario@demo.local',
            'activo' => true,
            'administrador' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $db->table('capacitacion')->insert([
            'nombre' => 'Primeros auxilios',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedCuadrillas(): void
    {
        if (! Schema::connection('cuadrillas')->hasTable('curso')) {
            return;
        }
        if (DB::connection('cuadrillas')->table('curso')->count() > 0) {
            return;
        }

        $seeder = new RichCuadrillasDemoSeeder();
        if ($this->command) {
            $seeder->setCommand($this->command);
        }
        $seeder->run();
    }
}
