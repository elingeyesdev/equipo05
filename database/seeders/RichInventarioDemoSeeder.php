<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RichInventarioDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('inventario')->hasTable('donaciones')) {
            $this->command?->warn('Inventario: ejecuta php artisan db:setup-inventario --fresh');

            return;
        }

        $db = DB::connection('inventario');
        $now = Carbon::now();

        $this->seedCatalogos($db, $now);
        $this->seedDonantesYDonaciones($db, $now);
        $this->seedAlmacenYPaquetes($db, $now);

        $this->command?->info('Inventario: catálogos, donaciones, almacén y paquetes demo ampliados.');
    }

    private function seedCatalogos($db, Carbon $now): void
    {
        $categorias = ['Alimentos', 'Higiene', 'Ropa', 'Medicamentos', 'Herramientas', 'Agua'];
        $catIds = [];
        foreach ($categorias as $nombre) {
            $row = $db->table('categorias_productos')->where('nombre', $nombre)->first();
            if ($row) {
                $catIds[] = $row->id_categoria;
            } else {
                $catIds[] = $db->table('categorias_productos')->insertGetId(['nombre' => $nombre], 'id_categoria');
            }
        }

        $productos = [
            ['Arroz', 'kg'], ['Fideo', 'kg'], ['Aceite', 'litros'], ['Azúcar', 'kg'],
            ['Agua', 'litros'], ['Leche', 'litros'], ['Atún', 'unidades'], ['Jabón', 'unidades'],
            ['Pasta dental', 'unidades'], ['Mantas', 'unidades'], ['Botiquín', 'unidades'], ['Martillo', 'unidades'],
        ];

        foreach ($productos as $i => [$nombre, $unidad]) {
            if ($db->table('productos')->where('nombre', $nombre)->exists()) {
                continue;
            }
            $db->table('productos')->insert([
                'id_categoria' => $catIds[$i % count($catIds)],
                'nombre' => $nombre,
                'descripcion' => 'Producto demo '.$nombre,
                'unidad_medida' => $unidad,
            ]);
        }

        $campanas = [
            'Solidaridad Santa Cruz 2026',
            'Chiquitos en emergencia',
            'Invierno sin frío',
            'Agua para comunidades',
            'Kit escolar evacuados',
        ];

        foreach ($campanas as $nombre) {
            if ($db->table('campanas')->where('nombre', $nombre)->exists()) {
                continue;
            }
            $db->table('campanas')->insert([
                'nombre' => $nombre,
                'descripcion' => 'Campaña inventario '.$nombre,
                'fecha_inicio' => $now->copy()->subDays(rand(10, 60))->toDateString(),
                'fecha_fin' => $now->copy()->addDays(rand(30, 120))->toDateString(),
            ]);
        }
    }

    private function seedDonantesYDonaciones($db, Carbon $now): void
    {
        $campId = $db->table('campanas')->value('id_campana');
        if (! $campId) {
            return;
        }

        $nombres = [
            'María López', 'Juan Pérez', 'Empresa AgroSur', 'Cooperativa El Trigal',
            'Ana Martínez', 'Carlos Roca', 'ONG Vida Verde', 'Iglesia San Roque',
            'Luis Fernández', 'Patricia Soliz', 'Grupo Scout 45', 'Universidad UAGRM',
        ];

        foreach ($nombres as $i => $nombre) {
            $donante = $db->table('donantes')->where('nombre', $nombre)->first();
            if ($donante) {
                $donanteId = $donante->id_donante;
            } else {
                $donanteId = $db->table('donantes')->insertGetId([
                    'nombre' => $nombre,
                    'tipo' => str_contains($nombre, 'Empresa') || str_contains($nombre, 'ONG') ? 'empresa' : 'persona',
                    'email' => 'donante'.($i + 1).'@demo.local',
                    'telefono' => '7'.str_pad((string) (1000000 + $i), 7, '0'),
                    'fecha_registro' => $now->copy()->subDays(rand(5, 90)),
                ], 'id_donante');
            }

            for ($j = 0; $j < 2; $j++) {
                $tipo = ($i + $j) % 2 === 0 ? 'especie' : 'dinero';
                $donId = $db->table('donaciones')->insertGetId([
                    'id_donante' => $donanteId,
                    'tipo' => $tipo,
                    'fecha' => $now->copy()->subDays(rand(1, 45)),
                    'id_campana' => $campId,
                    'observaciones' => 'Donación demo '.$nombre.' #'.($j + 1),
                ], 'id_donacion');

                if ($tipo === 'dinero' && Schema::connection('inventario')->hasTable('donaciones_dinero')) {
                    $db->table('donaciones_dinero')->insert([
                        'id_donacion' => $donId,
                        'monto' => rand(100, 5000),
                        'metodo_pago' => ['efectivo', 'transferencia', 'qr'][rand(0, 2)],
                    ]);
                }

                if ($tipo === 'especie') {
                    $prodId = $db->table('productos')->inRandomOrder()->value('id_producto');
                    if ($prodId && Schema::connection('inventario')->hasTable('donacion_detalles')) {
                        $db->table('donacion_detalles')->insert([
                            'id_donacion' => $donId,
                            'id_producto' => $prodId,
                            'cantidad' => rand(5, 200),
                        ]);
                    }
                }
            }
        }

        if (Schema::connection('inventario')->hasTable('solicitudes_recoleccion')) {
            $donanteId = $db->table('donantes')->value('id_donante');
            for ($k = 1; $k <= 6; $k++) {
                $codigo = 'SOL-REC-'.str_pad((string) $k, 3, '0', STR_PAD_LEFT);
                if ($db->table('solicitudes_recoleccion')->where('observaciones', 'like', '%'.$codigo.'%')->exists()) {
                    continue;
                }
                $db->table('solicitudes_recoleccion')->insert([
                    'id_donante' => $donanteId,
                    'direccion_recoleccion' => 'Av. Demo '.$k.', Santa Cruz',
                    'fecha_programada' => $now->copy()->addDays($k),
                    'observaciones' => 'Recolección programada '.$codigo,
                    'estado' => ['pendiente', 'en_camino', 'completada'][rand(0, 2)],
                ]);
            }
        }
    }

    private function seedAlmacenYPaquetes($db, Carbon $now): void
    {
        if (Schema::connection('inventario')->hasTable('almacenes')) {
            foreach (['Central SCZ', 'Depósito Norte', 'Bodega Cotoca', 'Punto Plan 3000'] as $nombre) {
                if ($db->table('almacenes')->where('nombre', $nombre)->exists()) {
                    continue;
                }
                $almId = $db->table('almacenes')->insertGetId([
                    'nombre' => $nombre,
                    'direccion' => 'Dirección demo '.$nombre,
                ], 'id_almacen');

                if (Schema::connection('inventario')->hasTable('estantes')) {
                    for ($e = 1; $e <= 3; $e++) {
                        $estId = $db->table('estantes')->insertGetId([
                            'id_almacen' => $almId,
                            'codigo_estante' => 'E'.$e,
                        ], 'id_estante');
                        if (Schema::connection('inventario')->hasTable('espacios')) {
                            $db->table('espacios')->insert([
                                'id_estante' => $estId,
                                'codigo_espacio' => 'ESP-'.$e,
                            ]);
                        }
                    }
                }
            }
        }

        if (Schema::connection('inventario')->hasTable('paquetes')) {
            for ($p = 1; $p <= 15; $p++) {
                $codigo = 'PKG-RICH-'.str_pad((string) $p, 3, '0', STR_PAD_LEFT);
                if ($db->table('paquetes')->where('codigo_paquete', $codigo)->exists()) {
                    continue;
                }
                $db->table('paquetes')->insert([
                    'codigo_paquete' => $codigo,
                    'estado' => ['pendiente', 'empacado', 'en_transito', 'entregado'][rand(0, 3)],
                    'fecha_creacion' => $now->copy()->subDays(rand(0, 20)),
                ]);
            }
        }
    }
}
