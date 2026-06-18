<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sustituye registros LOG-DEMO / "Solicitante Demo" por solicitudes operativas
 * coherentes con campañas de inventario, focos de incendios y comunidades de Santa Cruz.
 */
class LogisticaReemplazarDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('logistica')->hasTable('solicitud')) {
            $this->command?->warn('Logística: tabla solicitud no disponible.');

            return;
        }

        $db = DB::connection('logistica');
        $demoSolicitudes = $db->table('solicitud')
            ->where(function ($q) {
                $q->where('codigo_seguimiento', 'like', 'LOG-DEMO-%')
                    ->orWhere(function ($q2) {
                        $q2->where('codigo_seguimiento', 'like', 'SOL-SCZ-2026-%')
                            ->where('id_solicitud', '<=', 20);
                    })
                    ->orWhereIn('id_solicitante', function ($sub) {
                        $sub->select('id_solicitante')
                            ->from('solicitante')
                            ->where('email', 'like', '%@logistica.demo')
                            ->orWhere('nombre', 'Solicitante');
                    });
            })
            ->orderBy('id_solicitud')
            ->get();

        if ($demoSolicitudes->isEmpty()) {
            $this->command?->info('Logística: no hay solicitudes demo que reemplazar.');

            return;
        }

        $dataset = $this->registrosRealistas();
        $estados = $this->mapaEstados($db);
        $actualizadas = 0;

        foreach ($demoSolicitudes->values() as $index => $sol) {
            $item = $dataset[$index % count($dataset)];
            $fecha = Carbon::parse($item['fecha_base']);

            $solicitanteId = $db->table('solicitante')->insertGetId([
                'nombre' => $item['nombre'],
                'apellido' => $item['apellido'],
                'ci' => $item['ci'],
                'telefono' => $item['telefono'],
                'email' => $item['email'],
                'created_at' => $fecha,
                'updated_at' => now(),
            ], 'id_solicitante');

            $destinoPayload = [
                'comunidad' => $item['comunidad'],
                'provincia' => $item['provincia'],
                'direccion' => $item['direccion'],
                'created_at' => $fecha,
                'updated_at' => now(),
            ];
            if (Schema::connection('logistica')->hasColumn('destino', 'latitud')) {
                $destinoPayload['latitud'] = $item['latitud'];
            }
            if (Schema::connection('logistica')->hasColumn('destino', 'longitud')) {
                $destinoPayload['longitud'] = $item['longitud'];
            }

            $destinoId = $db->table('destino')->insertGetId($destinoPayload, 'id_destino');

            $nuevoCodigo = 'SOL-SCZ-2026-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            $db->table('solicitud')
                ->where('id_solicitud', $sol->id_solicitud)
                ->update([
                    'estado' => $item['estado'],
                    'codigo_seguimiento' => $nuevoCodigo,
                    'cantidad_personas' => $item['afectados'],
                    'fecha_inicio' => $fecha->toDateString(),
                    'fecha_necesidad' => $fecha->copy()->addDays($item['dias_necesidad'])->toDateString(),
                    'tipo_emergencia' => $item['emergencia'],
                    'insumos_necesarios' => $item['insumos'],
                    'id_solicitante' => $solicitanteId,
                    'id_destino' => $destinoId,
                    'fecha_solicitud' => $fecha->copy()->subDays(2)->toDateString(),
                    'aprobada' => in_array($item['estado'], ['aprobada', 'en_ruta', 'entregada'], true),
                    'apoyoaceptado' => $item['estado'] !== 'rechazada',
                    'updated_at' => now(),
                ]);

            if (Schema::connection('logistica')->hasTable('paquete')) {
                $estadoPaqueteId = $this->estadoPaqueteId($estados, $item['estado_paquete']);
                $ubicacion = match ($item['estado_paquete']) {
                    'pendiente' => 'Almacén central — Plan 3000, Santa Cruz (pendiente de armado)',
                    'almacen' => 'Almacén central — Plan 3000, Santa Cruz',
                    'transito' => 'Ruta '.$item['provincia'].' — despacho en curso',
                    'entregado' => $item['comunidad'].', '.$item['provincia'],
                    default => 'Almacén central — Plan 3000, Santa Cruz',
                };

                $db->table('paquete')
                    ->where('id_solicitud', $sol->id_solicitud)
                    ->update([
                        'codigo' => 'PKG-'.$nuevoCodigo,
                        'ubicacion_actual' => $ubicacion,
                        'fecha_creacion' => $fecha->copy()->addHours(4),
                        'estado_id' => $estadoPaqueteId,
                        'updated_at' => now(),
                    ]);
            }

            $actualizadas++;
        }

        $this->eliminarDestinosDemoHuerfanos($db);
        $this->eliminarSolicitantesDemoHuerfanos($db);

        $this->command?->info("Logística: {$actualizadas} solicitudes demo reemplazadas por datos operativos de Santa Cruz.");
    }

    /** @param array<string, int> $estados */
    private function estadoPaqueteId(array $estados, string $clave): int
    {
        return match ($clave) {
            'pendiente' => $estados['pendiente'] ?? reset($estados),
            'almacen' => $estados['en almacén'] ?? $estados['pendiente'] ?? reset($estados),
            'transito' => $estados['en tránsito'] ?? reset($estados),
            'entregado' => $estados['entregado'] ?? reset($estados),
            default => reset($estados),
        };
    }

    /** @return array<string, int> */
    private function mapaEstados($db): array
    {
        return $db->table('estado')->pluck('id_estado', 'nombre_estado')->mapWithKeys(
            fn ($id, $nombre) => [strtolower($nombre) => (int) $id]
        )->all();
    }

    private function eliminarDestinosDemoHuerfanos($db): void
    {
        $usados = $db->table('solicitud')->pluck('id_destino');

        $db->table('destino')
            ->where(function ($q) {
                $q->where('comunidad', 'like', 'Comunidad demo%')
                    ->orWhere('direccion', 'like', 'Zona afectada%');
            })
            ->whereNotIn('id_destino', $usados)
            ->delete();
    }

    private function eliminarSolicitantesDemoHuerfanos($db): void
    {
        $usados = $db->table('solicitud')->pluck('id_solicitante');

        $db->table('solicitante')
            ->where(function ($q) {
                $q->where('email', 'like', '%@logistica.demo')
                    ->orWhere('nombre', 'Solicitante')
                    ->orWhere('ci', 'like', 'LOG%');
            })
            ->whereNotIn('id_solicitante', $usados)
            ->delete();
    }

    /** @return array<int, array<string, mixed>> */
    private function registrosRealistas(): array
    {
        $filas = [
            ['Roberto', 'Mamani', '7891234', '77001234', 'roberto.mamani@gmail.com', 'Warnes', 'Warnes', 'Barrio San José, frente a plaza Warnes', -17.5167, -63.1667, 'Incendio forestal', 48, 'Agua potable (200 L), manta ignífuga, linternas recargables y guantes.', 'pendiente', 'pendiente', '2026-05-08 14:30:00', 3],
            ['Carla', 'Flores', '6549871', '75554321', 'carla.flores@outlook.com', 'San Ignacio de Velasco', 'Velasco', 'Barrio El Progreso, cerca iglesia San Ignacio', -16.3667, -60.9500, 'Inundación', 32, 'Arroz, fideo, botiquín, colchones y kit de higiene.', 'pendiente', 'pendiente', '2026-05-09 09:15:00', 2],
            ['Pedro', 'Quispe', '5234187', '76332211', 'pedro.quispe@yahoo.com', 'Montero', 'Obispo Santistevan', 'Zona norte, calle 3 de Mayo s/n', -17.3378, -63.2500, 'Sequía severa', 67, 'Bidones de agua (20), filtros y hervidor comunitario.', 'pendiente', 'pendiente', '2026-05-10 16:45:00', 4],
            ['Ana', 'Vargas', '8012456', '78114455', 'ana.vargas@gmail.com', 'El Torno', 'Cordillera', 'Mercado central El Torno, puesto 12', -17.9833, -63.3833, 'Incendio estructural', 19, 'Extintores PQS, mascarillas N95 y kit primeros auxilios.', 'pendiente', 'pendiente', '2026-05-11 11:20:00', 1],
            ['Luis', 'Suárez', '6987412', '75667788', 'luis.suarez@gmail.com', 'Cotoca', 'Cordillera', 'Comunidad San José de Cotoca, zona agrícola', -17.7544, -62.9336, 'Granizada', 41, 'Lona impermeable, martillo, clavos galvanizados y frazadas.', 'pendiente', 'pendiente', '2026-05-12 08:05:00', 5],
            ['Sofía', 'Ramos', '7456123', '79223344', 'sofia.ramos@gmail.com', 'Portachuelo', 'Sara', 'Plaza principal Portachuelo, junto a alcaldía', -17.8833, -63.2167, 'Incendio forestal', 55, 'Herramientas manuales, guantes ignífugos y bidones de agua.', 'pendiente', 'pendiente', '2026-05-13 13:40:00', 2],
            ['Carlos', 'Daza', '5321987', '76445566', 'carlos.daza@gmail.com', 'San Matías', 'Germán Busch', 'Barrio 24 de Septiembre, San Matías', -19.6519, -57.6333, 'Sequía severa', 88, 'Forraje, agua potable y medicamentos veterinarios básicos.', 'pendiente', 'pendiente', '2026-05-14 10:25:00', 3],
            ['Elena', 'Toro', '6123456', '77336622', 'elena.toro@gmail.com', 'Pailón', 'Sara', 'Escuela fiscal Pailón, sector sur', -18.0167, -63.3167, 'Inundación', 27, 'Frazadas, ropa seca, kit de higiene y arroz.', 'pendiente', 'pendiente', '2026-05-15 15:50:00', 2],
            ['Diego', 'Mercado', '4789123', '75889900', 'diego.mercado@gmail.com', 'Cuatro Cañadas', 'Chapare', 'Comunidad La Esperanza, km 42 carretera antigua', -17.4500, -63.8500, 'Derrumbe', 36, 'Palas, picos, cascos y cuerdas de rescate.', 'pendiente', 'pendiente', '2026-05-16 07:35:00', 4],
            ['Lucía', 'Condori', '7012345', '78556677', 'lucia.condori@gmail.com', 'Mineros', 'Chapare', 'Zona agrícola sur, cooperativa El Progreso', -17.5500, -63.9000, 'Incendio forestal', 62, 'Mochila contra incendio, bidones y linternas LED.', 'pendiente', 'pendiente', '2026-05-17 12:10:00', 3],
            ['Marcos', 'Aguilera', '5890123', '76112233', 'marcos.aguilera@gmail.com', 'Puerto Suárez', 'Germán Busch', 'Barrio Ferroviario, cerca estación', -18.3167, -57.7333, 'Inundación', 44, 'Arroz, aceite, azúcar, leche en polvo y botiquín.', 'pendiente', 'pendiente', '2026-05-18 17:55:00', 2],
            ['Patricia', 'Gonzales', '6345678', '77998877', 'patricia.gonzales@gmail.com', 'Roboré', 'Chiquitos', 'Comunidad San Miguel, Chiquitanía', -18.3333, -59.7500, 'Incendio forestal', 73, 'Agua embotellada, manta térmica y kit de herramientas.', 'pendiente', 'pendiente', '2026-05-19 09:30:00', 1],
            ['Jorge', 'Salazar', '7123987', '75443322', 'jorge.salazar@gmail.com', 'Concepción', 'Ñuflo de Chávez', 'Plaza 24 de Septiembre, Concepción', -16.4333, -62.0167, 'Sequía severa', 51, 'Tanques de agua, mangueras y filtros de barro.', 'aprobada', 'almacen', '2026-05-20 14:15:00', 3],
            ['Rosa', 'Pinto', '5678901', '78665544', 'rosa.pinto@gmail.com', 'San Javier', 'Ñuflo de Chávez', 'Barrio Nuevo Amanecer, San Javier', -16.2667, -62.1333, 'Incendio forestal', 29, 'Extintor portátil, linternas y botiquín de trauma.', 'aprobada', 'almacen', '2026-05-21 11:40:00', 2],
            ['Fernando', 'Roca', '6234567', '76221100', 'fernando.roca@gmail.com', 'Ascensión de Guarayos', 'Guarayos', 'Comunidad El Carmen, Ascensión', -15.7167, -62.9833, 'Inundación', 38, 'Colchones, frazadas y kit escolar.', 'en_ruta', 'transito', '2026-05-22 16:25:00', 4],
            ['Gabriela', 'Justiniano', '7456789', '78887766', 'gabriela.justiniano@gmail.com', 'San Julián', 'Chiquitos', 'Av. principal s/n, San Julián', -17.7833, -60.1000, 'Granizada', 46, 'Láminas de zinc, clavos y martillo.', 'en_ruta', 'transito', '2026-05-23 08:50:00', 2],
            ['Héctor', 'Bustillos', '5123456', '75334455', 'hector.bustillos@gmail.com', 'Charagua', 'Cordillera', 'Zona chaco norte, comunidad Km 42', -19.7833, -63.2000, 'Sequía severa', 95, 'Agua potable, forraje y sales de rehidratación oral.', 'en_ruta', 'transito', '2026-05-24 13:05:00', 5],
            ['Valeria', 'Ortiz', '6890123', '77110022', 'valeria.ortiz@gmail.com', 'Yapacaní', 'Ichilo', 'Comunidad 3 de Mayo, Yapacaní', -17.4000, -63.8833, 'Incendio forestal', 58, 'Herramientas, guantes y bidones de agua.', 'entregada', 'entregado', '2026-05-25 10:20:00', 3],
            ['Andrés', 'Peña', '7567890', '76556688', 'andres.pena@gmail.com', 'Buena Vista', 'Ichilo', 'Mercado municipal Buena Vista', -17.4667, -63.9833, 'Inundación', 33, 'Arroz, fideo, atún enlatado y agua embotellada.', 'entregada', 'entregado', '2026-05-01 09:00:00', 1],
            ['Camila', 'Rivera', '6012345', '78443311', 'camila.rivera@gmail.com', 'Limoncito', 'Cordillera', 'Barrio San Roque, Limoncito', -17.9500, -63.4500, 'Incendio forestal', 24, 'Mantas, agua y kit de primeros auxilios.', 'entregada', 'entregado', '2026-05-03 15:30:00', 2],
        ];

        return array_map(fn (array $f) => [
            'nombre' => $f[0],
            'apellido' => $f[1],
            'ci' => $f[2],
            'telefono' => $f[3],
            'email' => $f[4],
            'comunidad' => $f[5],
            'provincia' => $f[6],
            'direccion' => $f[7],
            'latitud' => $f[8],
            'longitud' => $f[9],
            'emergencia' => $f[10],
            'afectados' => $f[11],
            'insumos' => $f[12],
            'estado' => $f[13],
            'estado_paquete' => $f[14],
            'fecha_base' => $f[15],
            'dias_necesidad' => $f[16],
        ], $filas);
    }
}
