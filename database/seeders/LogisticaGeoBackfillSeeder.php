<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rellena coordenadas en destinos y ubicaciones de seguimiento
 * para habilitar el mapa operativo (portado del sistema original).
 */
class LogisticaGeoBackfillSeeder extends Seeder
{
    /** @var array<string, array{lat: float, lng: float}> */
    private array $coordsPorComunidad = [
        'Warnes' => ['lat' => -17.5167, 'lng' => -63.1667],
        'San Ignacio de Velasco' => ['lat' => -16.3667, 'lng' => -60.9500],
        'Montero' => ['lat' => -17.3378, 'lng' => -63.2500],
        'El Torno' => ['lat' => -17.9833, 'lng' => -63.3833],
        'Cotoca' => ['lat' => -17.7544, 'lng' => -62.9336],
        'Portachuelo' => ['lat' => -17.8833, 'lng' => -63.2167],
        'San Matías' => ['lat' => -19.6519, 'lng' => -57.6333],
        'Pailón' => ['lat' => -18.0167, 'lng' => -63.3167],
        'Cuatro Cañadas' => ['lat' => -17.4500, 'lng' => -63.8500],
        'Mineros' => ['lat' => -17.5500, 'lng' => -63.9000],
        'Puerto Suárez' => ['lat' => -18.3167, 'lng' => -57.7333],
        'Roboré' => ['lat' => -18.3333, 'lng' => -59.7500],
        'Concepción' => ['lat' => -16.4333, 'lng' => -62.0167],
        'San Javier' => ['lat' => -16.2667, 'lng' => -62.1333],
        'Ascensión de Guarayos' => ['lat' => -15.7167, 'lng' => -62.9833],
        'San Julián' => ['lat' => -17.7833, 'lng' => -60.1000],
        'Charagua' => ['lat' => -19.7833, 'lng' => -63.2000],
        'Yapacaní' => ['lat' => -17.4000, 'lng' => -63.8833],
        'Buena Vista' => ['lat' => -17.4667, 'lng' => -63.9833],
        'Limoncito' => ['lat' => -17.9500, 'lng' => -63.4500],
    ];

    public function run(): void
    {
        if (! Schema::connection('logistica')->hasTable('destino')) {
            return;
        }

        $db = Schema::connection('logistica');
        $conn = DB::connection('logistica');

        if ($db->hasColumn('destino', 'latitud') && $db->hasColumn('destino', 'longitud')) {
            foreach ($this->coordsPorComunidad as $comunidad => $coords) {
                $conn->table('destino')
                    ->where('comunidad', $comunidad)
                    ->where(function ($q) {
                        $q->whereNull('latitud')->orWhereNull('longitud');
                    })
                    ->update([
                        'latitud' => $coords['lat'],
                        'longitud' => $coords['lng'],
                        'updated_at' => now(),
                    ]);
            }
        }

        if (! $db->hasTable('historial_seguimiento_donaciones')
            || ! $db->hasTable('ubicacion')
            || ! $db->hasColumn('historial_seguimiento_donaciones', 'id_ubicacion')) {
            return;
        }

        $hasUbCoords = $db->hasColumn('ubicacion', 'latitud') && $db->hasColumn('ubicacion', 'longitud');

        $historiales = $conn->table('historial_seguimiento_donaciones')
            ->whereNull('id_ubicacion')
            ->orderBy('id_historial')
            ->get(['id_historial', 'id_paquete', 'estado', 'fecha_actualizacion']);

        foreach ($historiales as $h) {
            $dest = $conn->table('paquete')
                ->join('solicitud', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
                ->join('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
                ->where('paquete.id_paquete', $h->id_paquete)
                ->first(['destino.latitud', 'destino.longitud', 'destino.comunidad']);

            if (! $dest || ! is_numeric($dest->latitud ?? null) || ! is_numeric($dest->longitud ?? null)) {
                continue;
            }

            $payload = [
                'descripcion' => ($h->estado ?? 'Avance').' — '.($dest->comunidad ?? 'Ruta'),
                'created_at' => $h->fecha_actualizacion ?? now(),
                'updated_at' => now(),
            ];

            if ($hasUbCoords) {
                $t = 0.35 + (crc32((string) $h->id_historial) % 50) / 100;
                $payload['latitud'] = -17.8146 + ((float) $dest->latitud - (-17.8146)) * $t;
                $payload['longitud'] = -63.1561 + ((float) $dest->longitud - (-63.1561)) * $t;
                $payload['zona'] = $h->estado ?? 'Punto de avance';
            }

            $ubicacionId = $conn->table('ubicacion')->insertGetId($payload, 'id_ubicacion');
            $conn->table('historial_seguimiento_donaciones')
                ->where('id_historial', $h->id_historial)
                ->update(['id_ubicacion' => $ubicacionId]);
        }

        $this->command?->info('Logística: coordenadas de mapa rellenadas.');
    }
}
