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

        foreach (['Pendiente', 'En tránsito', 'En almacén', 'Entregado', 'Cancelado', 'Retrasado'] as $nombre) {
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

        for ($s = 1; $s <= 3; $s++) {
            $ci = 'LOG'.str_pad((string) $s, 6, '0', STR_PAD_LEFT);
            if ($db->table('solicitante')->where('ci', $ci)->exists()) {
                continue;
            }
            $db->table('solicitante')->insert([
                'nombre' => 'Solicitante',
                'apellido' => 'Demo '.$s,
                'ci' => $ci,
                'email' => 'solicitante'.$s.'@logistica.demo',
                'telefono' => '71'.str_pad((string) (200000 + $s), 7, '0'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        for ($d = 1; $d <= 4; $d++) {
            $comunidad = 'Comunidad demo '.$d;
            if ($db->table('destino')->where('comunidad', $comunidad)->exists()) {
                continue;
            }
            $db->table('destino')->insert([
                'comunidad' => $comunidad,
                'provincia' => 'Santa Cruz',
                'direccion' => 'Zona afectada '.$d,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $solicitanteId = $db->table('solicitante')->value('id_solicitante');
        $destinoId = $db->table('destino')->value('id_destino');

        if ($solicitanteId && $destinoId && Schema::connection('logistica')->hasTable('solicitud')) {
            for ($i = 1; $i <= 12; $i++) {
                $codigo = 'LOG-DEMO-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT);
                if ($db->table('solicitud')->where('codigo_seguimiento', $codigo)->exists()) {
                    continue;
                }

                $solId = $db->table('solicitud')->insertGetId([
                    'estado' => ['pendiente', 'aprobada', 'en_ruta', 'entregada'][rand(0, 3)],
                    'codigo_seguimiento' => $codigo,
                    'cantidad_personas' => rand(5, 80),
                    'fecha_inicio' => $now->copy()->subDays(rand(1, 10))->toDateString(),
                    'tipo_emergencia' => ['incendio', 'inundación', 'sequía'][rand(0, 2)],
                    'insumos_necesarios' => 'Agua, alimentos, frazadas, kit médico',
                    'id_solicitante' => $solicitanteId,
                    'id_destino' => $destinoId,
                    'fecha_solicitud' => $now->copy()->subDays(rand(2, 15))->toDateString(),
                    'aprobada' => (bool) rand(0, 1),
                    'apoyoaceptado' => (bool) rand(0, 1),
                    'created_at' => $now,
                    'updated_at' => $now,
                ], 'id_solicitud');

                if (Schema::connection('logistica')->hasTable('paquete')) {
                    $db->table('paquete')->insert([
                        'id_solicitud' => $solId,
                        'codigo' => 'PKG-'.$codigo,
                        'ubicacion_actual' => 'Depósito central Santa Cruz',
                        'fecha_creacion' => $now,
                        'estado_id' => $estadoId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        if (Schema::connection('logistica')->hasTable('vehiculo')) {
            foreach (['4521ABC', '7890XYZ', '1234SCZ', '5678EMG'] as $placa) {
                if ($db->table('vehiculo')->where('placa', $placa)->exists()) {
                    continue;
                }
                $db->table('vehiculo')->insert([
                    'placa' => $placa,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command?->info('Logística: estados, solicitantes, solicitudes y paquetes demo ampliados.');
    }
}
