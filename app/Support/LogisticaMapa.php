<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LogisticaMapa
{
    private const ORIGEN_LAT = -17.8146;

    private const ORIGEN_LNG = -63.1561;

    /** @return array<int, array<string, mixed>> */
    public static function marcadoresOperativos(): array
    {
        if (! Schema::connection('logistica')->hasTable('solicitud')
            || ! Schema::connection('logistica')->hasTable('destino')) {
            return [];
        }

        $schema = Schema::connection('logistica');
        $hasCoords = $schema->hasColumn('destino', 'latitud')
            && $schema->hasColumn('destino', 'longitud');

        $query = DB::connection('logistica')
            ->table('solicitud')
            ->leftJoin('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
            ->leftJoin('solicitante', 'solicitud.id_solicitante', '=', 'solicitante.id_solicitante')
            ->leftJoin('paquete', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
            ->select([
                'solicitud.id_solicitud',
                'solicitud.estado',
                'solicitud.tipo_emergencia',
                'solicitud.codigo_seguimiento',
                'destino.comunidad',
                'destino.provincia',
                'destino.direccion',
                'solicitante.nombre as solicitante_nombre',
                'solicitante.apellido as solicitante_apellido',
                'paquete.id_paquete',
            ])
            ->where(function ($q) {
                $q->whereNull('solicitud.codigo_seguimiento')
                    ->orWhere('solicitud.codigo_seguimiento', 'not like', 'LOG-DEMO-%');
            })
            ->orderByDesc('solicitud.created_at');

        if ($hasCoords) {
            $query->whereNotNull('destino.latitud')
                ->whereNotNull('destino.longitud')
                ->addSelect(['destino.latitud', 'destino.longitud']);
        }

        return $query->get()
            ->map(function ($row) use ($hasCoords) {
                $lat = $hasCoords ? (float) ($row->latitud ?? 0) : 0.0;
                $lng = $hasCoords ? (float) ($row->longitud ?? 0) : 0.0;

                if (! $hasCoords || ! self::coordValida($lat, $lng)) {
                    return null;
                }

                $estado = strtolower((string) ($row->estado ?? 'pendiente'));
                $tipo = match (true) {
                    str_contains($estado, 'entreg') => 'entregada',
                    str_contains($estado, 'ruta') || str_contains($estado, 'transit') => 'en_ruta',
                    str_contains($estado, 'aprobad') => 'aprobada',
                    str_contains($estado, 'rechaz') || str_contains($estado, 'negad') => 'rechazada',
                    default => 'pendiente',
                };

                return [
                    'id_solicitud' => (int) $row->id_solicitud,
                    'id_paquete' => $row->id_paquete ? (int) $row->id_paquete : null,
                    'lat' => $lat,
                    'lng' => $lng,
                    'tipo' => $tipo,
                    'ref' => LogisticaOperativa::refSolicitud((int) $row->id_solicitud),
                    'comunidad' => $row->comunidad ?? '—',
                    'provincia' => $row->provincia ?? '—',
                    'direccion' => $row->direccion ?? '',
                    'emergencia' => $row->tipo_emergencia ?? '—',
                    'solicitante' => trim(($row->solicitante_nombre ?? '').' '.($row->solicitante_apellido ?? '')) ?: '—',
                    'codigo' => $row->codigo_seguimiento ?? '',
                    'tracking_url' => $row->id_paquete
                        ? route('logistica.seguimiento.tracking', ['id' => $row->id_paquete])
                        : null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /** @return array{paquete: object, historial: Collection<int, object>, points: array<int, array<string, mixed>>, destino: array{lat: float|null, lng: float|null}}|null */
    public static function datosTracking(int $idPaquete): ?array
    {
        if (! Schema::connection('logistica')->hasTable('paquete')) {
            return null;
        }

        $paquete = DB::connection('logistica')
            ->table('paquete')
            ->leftJoin('solicitud', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
            ->leftJoin('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
            ->leftJoin('solicitante', 'solicitud.id_solicitante', '=', 'solicitante.id_solicitante')
            ->leftJoin('estado', 'paquete.estado_id', '=', 'estado.id_estado')
            ->where('paquete.id_paquete', $idPaquete)
            ->select([
                'paquete.*',
                'solicitud.codigo_seguimiento',
                'solicitud.tipo_emergencia',
                'destino.comunidad',
                'destino.provincia',
                'destino.direccion',
                'destino.latitud as destino_latitud',
                'destino.longitud as destino_longitud',
                'solicitante.nombre as solicitante_nombre',
                'solicitante.apellido as solicitante_apellido',
                'solicitante.ci as solicitante_ci',
                'estado.nombre_estado',
            ])
            ->first();

        if (! $paquete) {
            return null;
        }

        $destLat = is_numeric($paquete->destino_latitud ?? null) ? (float) $paquete->destino_latitud : null;
        $destLng = is_numeric($paquete->destino_longitud ?? null) ? (float) $paquete->destino_longitud : null;

        $historial = collect();
        $schema = Schema::connection('logistica');
        if ($schema->hasTable('historial_seguimiento_donaciones')) {
            $hasUbicacion = $schema->hasColumn('historial_seguimiento_donaciones', 'id_ubicacion')
                && $schema->hasTable('ubicacion');

            $q = DB::connection('logistica')
                ->table('historial_seguimiento_donaciones')
                ->where('id_paquete', $idPaquete)
                ->orderBy('fecha_actualizacion');

            if ($hasUbicacion) {
                $q->leftJoin('ubicacion', 'historial_seguimiento_donaciones.id_ubicacion', '=', 'ubicacion.id_ubicacion')
                    ->select([
                        'historial_seguimiento_donaciones.*',
                        'ubicacion.latitud as ub_lat',
                        'ubicacion.longitud as ub_lng',
                        'ubicacion.zona as ub_zona',
                    ]);
            }

            $historial = $q->get();
        }

        $points = self::construirPuntosRecorrido($historial, $paquete, $destLat, $destLng);

        return [
            'paquete' => $paquete,
            'historial' => $historial,
            'points' => $points,
            'destino' => ['lat' => $destLat, 'lng' => $destLng],
        ];
    }

    /** @param Collection<int, object> $historial */
    private static function construirPuntosRecorrido(Collection $historial, object $paquete, ?float $destLat, ?float $destLng): array
    {
        $points = [];

        foreach ($historial as $h) {
            $lat = isset($h->ub_lat) && is_numeric($h->ub_lat) ? (float) $h->ub_lat : null;
            $lng = isset($h->ub_lng) && is_numeric($h->ub_lng) ? (float) $h->ub_lng : null;

            if (self::coordValida($lat, $lng)) {
                $points[] = [
                    'lat' => $lat,
                    'lng' => $lng,
                    'zona' => $h->ub_zona ?? $h->estado ?? '',
                    'fecha' => $h->fecha_actualizacion ?? '',
                ];
            }
        }

        if ($points !== []) {
            return $points;
        }

        $ubicacionActual = (string) ($paquete->ubicacion_actual ?? '');
        if (preg_match('/\(([-0-9.]+),\s*([-0-9.]+)\)/', $ubicacionActual, $m)) {
            $points[] = [
                'lat' => (float) $m[1],
                'lng' => (float) $m[2],
                'zona' => $ubicacionActual,
                'fecha' => $paquete->updated_at ?? '',
            ];
        }

        if ($points !== []) {
            return $points;
        }

        if (! self::coordValida($destLat, $destLng) || $historial->isEmpty()) {
            if (self::coordValida($destLat, $destLng)) {
                return [[
                    'lat' => $destLat,
                    'lng' => $destLng,
                    'zona' => 'Destino final',
                    'fecha' => $paquete->fecha_entrega ?? '',
                ]];
            }

            return [];
        }

        $total = $historial->count();
        foreach ($historial->values() as $i => $h) {
            $t = ($i + 1) / ($total + 1);
            $points[] = [
                'lat' => self::ORIGEN_LAT + ($destLat - self::ORIGEN_LAT) * $t,
                'lng' => self::ORIGEN_LNG + ($destLng - self::ORIGEN_LNG) * $t,
                'zona' => $h->estado ?? 'Avance',
                'fecha' => $h->fecha_actualizacion ?? '',
            ];
        }

        return $points;
    }

    private static function coordValida(?float $lat, ?float $lng): bool
    {
        return $lat !== null && $lng !== null
            && $lat >= -90 && $lat <= 90
            && $lng >= -180 && $lng <= 180
            && ($lat != 0.0 || $lng != 0.0);
    }
}
