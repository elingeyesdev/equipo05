<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class RichLogisticaDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('logistica')->hasTable('estado')) {
            return;
        }

        $db = DB::connection('logistica');
        $now = Carbon::now();

        // 1. Estados
        foreach (['Pendiente', 'En tránsito', 'En almacén', 'Entregado', 'Cancelado', 'Retrasado', 'Rechazado', 'Dañado', 'En espera', 'Prioritario'] as $nombre) {
            if ($db->table('estado')->where('nombre_estado', $nombre)->exists()) {
                continue;
            }
            $db->table('estado')->insert([
                'nombre_estado' => $nombre,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $estadoId = $db->table('estado')->value('id_estado') ?? 1;

        // 2. Solicitantes
        for ($s = 1; $s <= 15; $s++) {
            $ci = 'LOG'.str_pad((string) $s, 6, '0', STR_PAD_LEFT);
            if ($db->table('solicitante')->where('ci', $ci)->exists()) {
                continue;
            }
            $db->table('solicitante')->insert([
                'nombre' => ['Juan', 'Maria', 'Pedro', 'Ana', 'Luis', 'Sofia', 'Carlos', 'Elena', 'Diego', 'Lucia'][rand(0, 9)],
                'apellido' => ['Gomez', 'Quispe', 'Flores', 'Vargas', 'Ramos', 'Mamani', 'Suarez', 'Daza', 'Sosa', 'Toro'][rand(0, 9)],
                'ci' => $ci,
                'email' => 'solicitante'.$s.'@logistica.demo',
                'telefono' => '71'.str_pad((string) (200000 + $s), 7, '0'),
                'created_at' => $now->subDays(rand(1, 30)),
                'updated_at' => $now,
            ]);
        }

        // 3. Destinos
        for ($d = 1; $d <= 12; $d++) {
            $comunidad = ['San Ignacio', 'Robore', 'San Jose', 'Concepcion', 'Ascencion', 'San Matias', 'Puerto Suarez', 'Pailon', 'Cuatro Cañadas', 'El Torno', 'Montero', 'Warnes'][$d - 1];
            if ($db->table('destino')->where('comunidad', $comunidad)->exists()) {
                continue;
            }
            $db->table('destino')->insert([
                'comunidad' => $comunidad,
                'provincia' => ['Chiquitos', 'Velasco', 'Sandoval', 'German Busch', 'Ñuflo de Chavez', 'Cordillera'][rand(0, 5)],
                'direccion' => 'Zona '.rand(1, 5).' - Calle '.rand(10, 99),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $solicitanteIds = $db->table('solicitante')->pluck('id_solicitante')->toArray();
        $destinoIds = $db->table('destino')->pluck('id_destino')->toArray();

        // 4. Solicitudes y Paquetes
        if (!empty($solicitanteIds) && !empty($destinoIds) && Schema::connection('logistica')->hasTable('solicitud')) {
            for ($i = 1; $i <= 20; $i++) {
                $codigo = 'LOG-DEMO-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT);
                if ($db->table('solicitud')->where('codigo_seguimiento', $codigo)->exists()) {
                    continue;
                }

                $solId = $db->table('solicitud')->insertGetId([
                    'estado' => ['pendiente', 'aprobada', 'en_ruta', 'entregada', 'rechazada'][rand(0, 4)],
                    'codigo_seguimiento' => $codigo,
                    'cantidad_personas' => rand(5, 150),
                    'fecha_inicio' => $now->copy()->subDays(rand(1, 15))->toDateString(),
                    'tipo_emergencia' => ['Incendio Forestal', 'Inundación', 'Sequía Severa', 'Sismo', 'Helada'][rand(0, 4)],
                    'insumos_necesarios' => 'Agua potable, herramientas, raciones secas, medicamentos básicos.',
                    'id_solicitante' => $solicitanteIds[array_rand($solicitanteIds)],
                    'id_destino' => $destinoIds[array_rand($destinoIds)],
                    'fecha_solicitud' => $now->copy()->subDays(rand(5, 20))->toDateString(),
                    'aprobada' => (bool) rand(0, 1),
                    'apoyoaceptado' => (bool) rand(0, 1),
                    'created_at' => $now->subHours(rand(10, 500)),
                    'updated_at' => $now,
                ], 'id_solicitud');

                if (Schema::connection('logistica')->hasTable('paquete')) {
                    $pkgId = $db->table('paquete')->insertGetId([
                        'id_solicitud' => $solId,
                        'codigo' => 'PKG-'.$codigo,
                        'ubicacion_actual' => ['Almacén Central', 'En camión', 'Centro de acopio local', 'Destino terminal'][rand(0, 3)],
                        'fecha_creacion' => $now->subDays(rand(1, 5)),
                        'estado_id' => $db->table('estado')->pluck('id_estado')->random(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ], 'id_paquete');

                    // 5. Historial Seguimiento (Nuevos datos)
                    if (Schema::connection('logistica')->hasTable('historial_seguimiento_donaciones')) {
                        for($h = 1; $h <= 3; $h++) {
                            $db->table('historial_seguimiento_donaciones')->insert([
                                'id_paquete' => $pkgId,
                                'estado' => ['Cargado', 'En tránsito', 'Llegada intermedia', 'Entregado'][rand(0,3)],
                                'fecha_actualizacion' => $now->copy()->subHours(rand(1, 48)),
                                'vehiculo_placa' => rand(1000, 9999).strtoupper(str_repeat(chr(rand(65, 90)), 3)),
                                'conductor_nombre' => 'Conductor '.rand(1, 20),
                                'conductor_ci' => rand(1000000, 9999999),
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }
                    }
                }
            }
        }

        // 6. Vehículos
        if (Schema::connection('logistica')->hasTable('vehiculo')) {
            $placas = ['4521ABC', '7890XYZ', '1234SCZ', '5678EMG', '9988HJK', '1020PLK', '3344ASD', '5566TTR', '4411QWE', '2233RTY', '4455UIO', '6677PAS'];
            foreach ($placas as $placa) {
                if ($db->table('vehiculo')->where('placa', $placa)->exists()) {
                    continue;
                }
                $db->table('vehiculo')->insert([
                    'placa' => $placa,
                    'modelo' => ['Volvo FMX', 'Toyota Hilux', 'Mercedes Atego', 'Nissan Patrol', 'Fuso Canter'][rand(0, 4)],
                    'capacidad' => rand(2, 20).' Ton',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 7. Conductores
        if (Schema::connection('logistica')->hasTable('conductor')) {
             for ($c = 1; $c <= 12; $c++) {
                $nombre = ['Ricardo', 'Mario', 'Hugo', 'Javier', 'Marcelo', 'Marcos'][rand(0, 5)];
                $apellido = ['Cabrera', 'Villca', 'Tapia', 'Siles', 'Mercado', 'Rojas'][rand(0, 5)];
                if ($db->table('conductor')->where('nombre', $nombre)->where('apellido', $apellido)->exists()) continue;
                $db->table('conductor')->insert([
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
             }
        }

        // 8. Marcas
        if (Schema::connection('logistica')->hasTable('marca')) {
            foreach (['Toyota', 'Volvo', 'Nissan', 'Mercedes-Benz', 'Scania', 'Dongfeng', 'Ford', 'Chevrolet', 'Isuzu', 'Hino', 'Mazda', 'Mitsubishi'] as $m) {
                if ($db->table('marca')->where('nombre', $m)->exists()) continue;
                $db->table('marca')->insert(['nombre' => $m, 'created_at' => $now, 'updated_at' => $now]);
            }
        }

        // 9. Tipos de Emergencia
        if (Schema::connection('logistica')->hasTable('tipo_emergencia')) {
            foreach (['Incendio Forestal', 'Inundación', 'Sequía', 'Derrumbe', 'Helada', 'Granizada', 'Epidemia', 'Accidente Masivo', 'Sismo', 'Incendio Estructural'] as $te) {
                if ($db->table('tipo_emergencia')->where('nombre', $te)->exists()) continue;
                $db->table('tipo_emergencia')->insert(['nombre' => $te, 'created_at' => $now, 'updated_at' => $now]);
            }
        }

        $this->command?->info('Logística: Datos demo ampliados significativamente (mínimo 10-20 por tabla).');

        $this->command?->info('Logística: estados, solicitantes, solicitudes y paquetes demo ampliados.');
    }
}
