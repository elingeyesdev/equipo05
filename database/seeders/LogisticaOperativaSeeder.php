<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LogisticaOperativaSeeder extends Seeder
{
    /** @var array<int, array<string, mixed>> */
    private array $conductores = [
        ['nombre' => 'Ricardo', 'apellido' => 'Cabrera', 'ci' => '5843210'],
        ['nombre' => 'Mario', 'apellido' => 'Villca', 'ci' => '7123456'],
        ['nombre' => 'Hugo', 'apellido' => 'Tapia', 'ci' => '6234789'],
        ['nombre' => 'Javier', 'apellido' => 'Siles', 'ci' => '8012345'],
        ['nombre' => 'Marcelo', 'apellido' => 'Mercado', 'ci' => '4567890'],
        ['nombre' => 'Felipe', 'apellido' => 'Rojas', 'ci' => '6987412'],
        ['nombre' => 'Oscar', 'apellido' => 'Condori', 'ci' => '7456123'],
        ['nombre' => 'Daniel', 'apellido' => 'Aguilera', 'ci' => '5321987'],
    ];

    /** @var array<int, string> */
    private array $placas = [
        '2458SCZ', '1890EMG', '3366ABC', '4521XYZ', '7788HJK',
        '1020PLK', '5544TTR', '6677PAS', '9012QWE', '4433RTY',
    ];

    public function run(): void
    {
        if (! Schema::connection('logistica')->hasTable('solicitud')) {
            $this->command?->warn('Logística: tabla solicitud no disponible.');

            return;
        }

        $db = DB::connection('logistica');
        $this->asegurarEstados($db);
        $this->asegurarFlota($db);
        $estados = $this->mapaEstados($db);

        $creadas = 0;
        foreach ($this->solicitudesOperativas() as $item) {
            if ($db->table('solicitud')->where('codigo_seguimiento', $item['codigo'])->exists()) {
                continue;
            }

            $fecha = Carbon::parse($item['fecha_base']);
            $solicitanteId = $db->table('solicitante')->insertGetId([
                'nombre' => $item['nombre'],
                'apellido' => $item['apellido'],
                'ci' => $item['ci'],
                'telefono' => $item['telefono'],
                'email' => null,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ], 'id_solicitante');

            $destinoId = $db->table('destino')->insertGetId([
                'comunidad' => $item['comunidad'],
                'provincia' => $item['provincia'],
                'direccion' => $item['direccion'],
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ], 'id_destino');

            $solId = $db->table('solicitud')->insertGetId([
                'estado' => $item['estado'],
                'codigo_seguimiento' => $item['codigo'],
                'cantidad_personas' => $item['afectados'],
                'fecha_inicio' => $fecha->toDateString(),
                'fecha_necesidad' => $fecha->copy()->addDays($item['dias_necesidad'])->toDateString(),
                'tipo_emergencia' => $item['emergencia'],
                'insumos_necesarios' => $item['insumos'],
                'id_solicitante' => $solicitanteId,
                'id_destino' => $destinoId,
                'fecha_solicitud' => $fecha->toDateString(),
                'aprobada' => in_array($item['estado'], ['aprobada', 'en_ruta', 'entregada'], true),
                'apoyoaceptado' => $item['estado'] !== 'rechazada',
                'created_at' => $fecha,
                'updated_at' => $fecha->copy()->addHours($item['horas_actualizacion']),
            ], 'id_solicitud');

            if (in_array($item['estado'], ['pendiente', 'aprobada', 'en_ruta', 'entregada'], true)
                && Schema::connection('logistica')->hasTable('paquete')) {
                $this->crearPaqueteYSeguimiento($db, $estados, $solId, $item, $fecha);
            }

            if ($item['vincular_inventario'] ?? false) {
                $this->vincularPaqueteInventario($item['codigo'], $item['estado'], $fecha);
            }

            $creadas++;
        }

        $this->command?->info("Logística operativa: {$creadas} solicitudes realistas sembradas.");
    }

    private function asegurarEstados($db): void
    {
        if (! Schema::connection('logistica')->hasTable('estado')) {
            return;
        }

        $now = now();
        foreach (['Pendiente', 'En tránsito', 'En almacén', 'Entregado', 'Cancelado', 'Rechazado'] as $nombre) {
            if ($db->table('estado')->where('nombre_estado', $nombre)->exists()) {
                continue;
            }
            $db->table('estado')->insert([
                'nombre_estado' => $nombre,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /** @return array<string, int> */
    private function mapaEstados($db): array
    {
        return $db->table('estado')->pluck('id_estado', 'nombre_estado')->mapWithKeys(
            fn ($id, $nombre) => [strtolower($nombre) => (int) $id]
        )->all();
    }

    private function asegurarFlota($db): void
    {
        if (Schema::connection('logistica')->hasTable('vehiculo')) {
            foreach ($this->placas as $placa) {
                if ($db->table('vehiculo')->where('placa', $placa)->exists()) {
                    continue;
                }
                $row = ['placa' => $placa, 'created_at' => now(), 'updated_at' => now()];
                if (Schema::connection('logistica')->hasColumn('vehiculo', 'modelo')) {
                    $row['modelo'] = ['Toyota Hilux', 'Volvo FMX', 'Mercedes Atego', 'Nissan Patrol'][array_rand([0, 1, 2, 3])];
                }
                if (Schema::connection('logistica')->hasColumn('vehiculo', 'capacidad')) {
                    $row['capacidad'] = rand(3, 12).' Ton';
                }
                $db->table('vehiculo')->insert($row);
            }
        }

        if (Schema::connection('logistica')->hasTable('conductor')) {
            foreach ($this->conductores as $c) {
                if ($db->table('conductor')->where('nombre', $c['nombre'])->where('apellido', $c['apellido'])->exists()) {
                    continue;
                }
                $db->table('conductor')->insert([
                    'nombre' => $c['nombre'],
                    'apellido' => $c['apellido'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /** @param array<string, int> $estados */
    private function crearPaqueteYSeguimiento($db, array $estados, int $solId, array $item, Carbon $fecha): void
    {
        $estadoPaquete = match ($item['estado']) {
            'pendiente' => $estados['pendiente'] ?? reset($estados),
            'aprobada' => $estados['en almacén'] ?? $estados['pendiente'] ?? reset($estados),
            'en_ruta' => $estados['en tránsito'] ?? reset($estados),
            'entregada' => $estados['entregado'] ?? reset($estados),
            default => reset($estados),
        };

        $ubicacion = match ($item['estado']) {
            'pendiente' => 'Almacén central — Plan 3000, Santa Cruz (pendiente de armado)',
            'aprobada' => 'Almacén central Santa Cruz',
            'en_ruta' => 'Ruta '.$item['provincia'].' — km '.rand(12, 180),
            'entregada' => $item['comunidad'].', '.$item['provincia'],
            default => 'Almacén central Santa Cruz',
        };

        $pkgId = $db->table('paquete')->insertGetId([
            'id_solicitud' => $solId,
            'codigo' => 'PKG-'.$item['codigo'],
            'ubicacion_actual' => $ubicacion,
            'fecha_creacion' => $fecha->copy()->addHours(4),
            'fecha_entrega' => $item['estado'] === 'entregada' ? $fecha->copy()->addDays(2) : null,
            'estado_id' => $estadoPaquete,
            'created_at' => $fecha->copy()->addHours(4),
            'updated_at' => $fecha->copy()->addHours($item['horas_actualizacion']),
        ], 'id_paquete');

        if (! Schema::connection('logistica')->hasTable('historial_seguimiento_donaciones')) {
            return;
        }

        $conductor = $this->conductores[array_rand($this->conductores)];
        $placa = $this->placas[array_rand($this->placas)];
        $pasos = match ($item['estado']) {
            'pendiente' => [],
            'aprobada' => [['estado' => 'Armado en almacén', 'horas' => 6]],
            'en_ruta' => [
                ['estado' => 'Cargado en almacén', 'horas' => 8],
                ['estado' => 'En tránsito', 'horas' => 20],
            ],
            'entregada' => [
                ['estado' => 'Cargado en almacén', 'horas' => 6],
                ['estado' => 'En tránsito', 'horas' => 18],
                ['estado' => 'Llegada a comunidad', 'horas' => 30],
                ['estado' => 'Entregado', 'horas' => 36],
            ],
            default => [],
        };

        foreach ($pasos as $paso) {
            $db->table('historial_seguimiento_donaciones')->insert([
                'id_paquete' => $pkgId,
                'estado' => $paso['estado'],
                'fecha_actualizacion' => $fecha->copy()->addHours($paso['horas']),
                'vehiculo_placa' => $placa,
                'conductor_nombre' => trim($conductor['nombre'].' '.$conductor['apellido']),
                'conductor_ci' => $conductor['ci'],
                'created_at' => $fecha->copy()->addHours($paso['horas']),
                'updated_at' => $fecha->copy()->addHours($paso['horas']),
            ]);
        }
    }

    private function vincularPaqueteInventario(string $codigoSolicitud, string $estado, Carbon $fecha): void
    {
        if (! Schema::connection('inventario')->hasTable('paquetes')) {
            return;
        }

        $inv = DB::connection('inventario');
        if ($inv->table('paquetes')->where('codigo_solicitud_externa', $codigoSolicitud)->exists()) {
            return;
        }

        $estadoInv = match ($estado) {
            'entregada' => 'entregado',
            'en_ruta' => 'en_transito',
            'aprobada' => 'empacado',
            default => 'pendiente',
        };

        $inv->table('paquetes')->insert([
            'codigo_paquete' => 'PKG-INV-'.substr($codigoSolicitud, 4),
            'codigo_solicitud_externa' => $codigoSolicitud,
            'estado' => $estadoInv,
            'fecha_creacion' => $fecha->copy()->addHours(2),
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    private function solicitudesOperativas(): array
    {
        $filas = [
            ['Roberto', 'Mamani', '7891234', '77001234', 'Warnes', 'Warnes', 'Av. Banzer km 12', 'Incendio forestal', 48, 'Agua potable, manta térmica, linternas.', 'pendiente', '2026-05-08 14:30:01', 3, 12, false],
            ['Carla', 'Flores', '6549871', '75554321', 'San Ignacio de Velasco', 'Velasco', 'Barrio El Progreso', 'Inundación', 32, 'Arroz, fideo, botiquín y colchones.', 'pendiente', '2026-05-09 09:15:22', 2, 8, false],
            ['Pedro', 'Quispe', '5234187', '76332211', 'Montero', 'Obispo Santistevan', 'Zona norte calle 3', 'Sequía severa', 67, 'Agua en bidones, hervidor y filtros.', 'pendiente', '2026-05-10 16:45:33', 4, 6, false],
            ['Ana', 'Vargas', '8012456', '78114455', 'El Torno', 'Cordillera', 'Mercado central', 'Incendio estructural', 19, 'Extintores, mascarillas y kit primeros auxilios.', 'pendiente', '2026-05-11 11:20:44', 1, 10, false],
            ['Luis', 'Suarez', '6987412', '75667788', 'Cotoca', 'Cordillera', 'Comunidad San José', 'Granizada', 41, 'Lona, martillo, clavos y frazadas.', 'pendiente', '2026-05-12 08:05:55', 5, 14, false],
            ['Sofía', 'Ramos', '7456123', '79223344', 'Portachuelo', 'Sara', 'Frente a plaza principal', 'Incendio forestal', 55, 'Herramientas manuales, guantes y agua.', 'aprobada', '2026-05-13 13:40:06', 2, 24, true],
            ['Carlos', 'Daza', '5321987', '76445566', 'San Matías', 'Germán Busch', 'Barrio 24 de Septiembre', 'Sequía severa', 88, 'Forraje, agua y medicamentos veterinarios básicos.', 'aprobada', '2026-05-14 10:25:17', 3, 20, true],
            ['Elena', 'Toro', '6123456', '77336622', 'Pailón', 'Sara', 'Escuela fiscal', 'Inundación', 27, 'Frazadas, ropa seca y kit de higiene.', 'aprobada', '2026-05-15 15:50:28', 2, 18, true],
            ['Diego', 'Mercado', '4789123', '75889900', 'Cuatro Cañadas', 'Chapare', 'Comunidad La Esperanza', 'Derrumbe', 36, 'Palas, picos, cascos y cuerdas.', 'aprobada', '2026-05-16 07:35:39', 4, 22, true],
            ['Lucía', 'Condori', '7012345', '78556677', 'Mineros', 'Chapare', 'Zona agrícola sur', 'Incendio forestal', 62, 'Mochila contra incendio, bidones y linternas.', 'aprobada', '2026-05-17 12:10:50', 3, 16, true],
            ['Marcos', 'Aguilera', '5890123', '76112233', 'Puerto Suárez', 'Germán Busch', 'Barrio Ferroviario', 'Inundación', 44, 'Arroz, aceite, azúcar y leche en polvo.', 'aprobada', '2026-05-18 17:55:01', 2, 26, true],
            ['Patricia', 'Gonzales', '6345678', '77998877', 'Roboré', 'Chiquitos', 'Comunidad San Miguel', 'Incendio forestal', 73, 'Agua, manta y kit de herramientas.', 'en_ruta', '2026-05-20 09:30:12', 1, 40, true],
            ['Jorge', 'Salazar', '7123987', '75443322', 'Concepción', 'Ñuflo de Chávez', 'Plaza 24 de Septiembre', 'Sequía severa', 51, 'Tanques de agua, mangueras y filtros.', 'en_ruta', '2026-05-21 14:15:23', 3, 38, true],
            ['Rosa', 'Pinto', '5678901', '78665544', 'San Javier', 'Ñuflo de Chávez', 'Barrio Nuevo Amanecer', 'Incendio forestal', 29, 'Extintor portátil, linternas y botiquín.', 'en_ruta', '2026-05-22 11:40:34', 2, 42, true],
            ['Fernando', 'Roca', '6234567', '76221100', 'Ascensión de Guarayos', 'Guarayos', 'Comunidad El Carmen', 'Inundación', 38, 'Colchones, frazadas y kit escolar.', 'en_ruta', '2026-05-23 16:25:45', 4, 36, true],
            ['Gabriela', 'Justiniano', '7456789', '78887766', 'San Julián', 'Chiquitos', 'Av. principal s/n', 'Granizada', 46, 'Láminas de zinc, clavos y martillo.', 'en_ruta', '2026-05-24 08:50:56', 2, 44, true],
            ['Héctor', 'Bustillos', '5123456', '75334455', 'Charagua', 'Cordillera', 'Zona chaco norte', 'Sequía severa', 95, 'Agua potable, forraje y sales rehidratantes.', 'en_ruta', '2026-05-25 13:05:07', 5, 48, true],
            ['Valeria', 'Ortiz', '6890123', '77110022', 'Yapacaní', 'Ichilo', 'Comunidad 3 de Mayo', 'Incendio forestal', 58, 'Herramientas, guantes y bidones.', 'en_ruta', '2026-05-26 10:20:18', 3, 46, true],
            ['Andrés', 'Peña', '7567890', '76556688', 'Buena Vista', 'Ichilo', 'Mercado municipal', 'Inundación', 33, 'Arroz, fideo, atún y agua embotellada.', 'entregada', '2026-05-01 09:00:19', 1, 72, true],
            ['Camila', 'Rivera', '6012345', '78443311', 'Limoncito', 'Cordillera', 'Barrio San Roque', 'Incendio forestal', 24, 'Mantas, agua y kit de primeros auxilios.', 'entregada', '2026-05-03 15:30:20', 2, 68, true],
            ['Raúl', 'Espinoza', '5789012', '75992233', 'La Guardia', 'Andrés Ibáñez', 'Comunidad Los Alamos', 'Sequía severa', 71, 'Bidones, filtros y hervidor.', 'entregada', '2026-05-05 11:45:21', 4, 70, true],
            ['Natalia', 'Céspedes', '7234567', '78776655', 'San Ramón', 'Chapare', 'Escuela San Ramón', 'Inundación', 42, 'Colchones, ropa y kit de higiene.', 'entregada', '2026-05-07 08:10:22', 2, 66, true],
            ['Miguel', 'Arze', '6456789', '75221144', 'Comarapa', 'Vallegrande', 'Plazuela central', 'Granizada', 37, 'Lona, clavos y herramientas menores.', 'entregada', '2026-05-14 14:25:23', 3, 64, true],
            ['Daniela', 'Montero', '7123450', '78009988', 'Samaipata', 'Florida', 'Barrio El Fuerte', 'Incendio forestal', 52, 'Mochila contra incendio, linternas y guantes.', 'entregada', '2026-05-16 17:40:24', 2, 62, true],
            ['Iván', 'Terrazas', '5345678', '76668877', 'Mairana', 'Florida', 'Comunidad La Angostura', 'Derrumbe', 31, 'Palas, picos, cascos y cuerdas.', 'entregada', '2026-05-18 12:55:25', 4, 60, true],
            ['Paola', 'Sandoval', '6891234', '78332211', 'Postrervalle', 'Vallegrande', 'Barrio San Pedro', 'Inundación', 49, 'Arroz, aceite, azúcar y botiquín.', 'entregada', '2026-05-20 10:15:26', 2, 58, true],
            ['René', 'Velasco', '7561234', '75554433', 'Lagunillas', 'Vallegrande', 'Zona alta comunidad', 'Sequía severa', 84, 'Agua, forraje y medicamentos básicos.', 'entregada', '2026-05-22 16:30:27', 5, 56, true],
            ['Silvia', 'Balderrama', '6123789', '77881122', 'Jorochito', 'Andrés Ibáñez', 'Frente capilla', 'Incendio estructural', 16, 'Extintores, mascarillas y manta.', 'rechazada', '2026-05-19 09:20:28', 1, 8, false],
            ['Tomás', 'Barrientos', '5456780', '76113344', 'Abapó', 'Chiquitos', 'Comunidad San Antonio', 'Helada', 28, 'Frazadas y ropa de abrigo.', 'rechazada', '2026-05-21 13:35:29', 3, 6, false],
            ['Verónica', 'Saavedra', '7012789', '78996655', 'Pailón', 'Sara', 'Barrio 16 de Julio', 'Accidente vial masivo', 22, 'Botiquín, camilla y material de inmovilización.', 'rechazada', '2026-05-23 18:50:30', 2, 4, false],
        ];

        return array_map(function (array $f): array {
            return [
                'nombre' => $f[0],
                'apellido' => $f[1],
                'ci' => $f[2],
                'telefono' => $f[3],
                'comunidad' => $f[4],
                'provincia' => $f[5],
                'direccion' => $f[6],
                'emergencia' => $f[7],
                'afectados' => $f[8],
                'insumos' => $f[9],
                'estado' => $f[10],
                'codigo' => 'SOL-'.str_replace([' ', '-', ':'], '', $f[11]),
                'fecha_base' => $f[11],
                'dias_necesidad' => $f[12],
                'horas_actualizacion' => $f[13],
                'vincular_inventario' => $f[14],
            ];
        }, $filas);
    }
}
