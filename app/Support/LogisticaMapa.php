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

        if (! self::coordValida($destLat, $destLng)) {
            $resuelto = ComunidadCoords::resolver($paquete->comunidad ?? null);
            if ($resuelto !== null) {
                $destLat = $resuelto['lat'];
                $destLng = $resuelto['lng'];
            }
        }

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
        $waypoints = self::waypointsCompletos($points, $destLat, $destLng);
        $posicionActual = self::resolverPosicionActual($paquete, $historial, $waypoints, $destLat, $destLng);

        return [
            'paquete' => $paquete,
            'historial' => $historial,
            'points' => $points,
            'waypoints' => $waypoints,
            'posicion_actual' => $posicionActual,
            'origen' => ['lat' => self::ORIGEN_LAT, 'lng' => self::ORIGEN_LNG, 'label' => 'Almacén central'],
            'destino' => [
                'lat' => $destLat,
                'lng' => $destLng,
                'comunidad' => $paquete->comunidad ?? null,
            ],
        ];
    }

    /** @return array<string, mixed>|null */
    public static function resolverPosicionActual(
        object $paquete,
        Collection $historial,
        array $waypoints,
        ?float $destLat,
        ?float $destLng
    ): ?array {
        foreach ($historial->sortByDesc('fecha_actualizacion') as $h) {
            $lat = isset($h->ub_lat) && is_numeric($h->ub_lat) ? (float) $h->ub_lat : null;
            $lng = isset($h->ub_lng) && is_numeric($h->ub_lng) ? (float) $h->ub_lng : null;
            if (self::coordValida($lat, $lng)) {
                return [
                    'lat' => $lat,
                    'lng' => $lng,
                    'zona' => $h->ub_zona ?? $h->estado ?? 'Ubicación reportada',
                    'fecha' => $h->fecha_actualizacion ?? null,
                    'estado' => $h->estado ?? null,
                    'fuente' => 'historial',
                ];
            }
        }

        $ubicacionActual = (string) ($paquete->ubicacion_actual ?? '');
        if (preg_match('/\(([-0-9.]+),\s*([-0-9.]+)\)/', $ubicacionActual, $m)) {
            $lat = (float) $m[1];
            $lng = (float) $m[2];
            if (self::coordValida($lat, $lng)) {
                return [
                    'lat' => $lat,
                    'lng' => $lng,
                    'zona' => $ubicacionActual,
                    'fecha' => $paquete->updated_at ?? null,
                    'estado' => $paquete->nombre_estado ?? null,
                    'fuente' => 'ubicacion_actual',
                ];
            }
        }

        if (! empty($paquete->fecha_entrega) && self::coordValida($destLat, $destLng)) {
            return [
                'lat' => $destLat,
                'lng' => $destLng,
                'zona' => 'Entregado en destino',
                'fecha' => $paquete->fecha_entrega,
                'estado' => 'entregado',
                'fuente' => 'destino',
            ];
        }

        $estado = strtolower((string) ($paquete->nombre_estado ?? ''));
        $enTransito = str_contains($estado, 'transit') || str_contains($estado, 'tráns')
            || str_contains($estado, 'ruta') || str_contains($estado, 'camino');

        if ($enTransito && self::coordValida($destLat, $destLng)) {
            $avances = max(1, $historial->count());
            $progreso = min(0.9, 0.12 * $avances + 0.18);
            $lat = self::ORIGEN_LAT + ($destLat - self::ORIGEN_LAT) * $progreso;
            $lng = self::ORIGEN_LNG + ($destLng - self::ORIGEN_LNG) * $progreso;

            return [
                'lat' => $lat,
                'lng' => $lng,
                'zona' => $paquete->ubicacion_actual ?: 'En tránsito hacia destino',
                'fecha' => $paquete->updated_at ?? now()->toDateTimeString(),
                'estado' => $paquete->nombre_estado ?? 'en_transito',
                'fuente' => 'estimada',
            ];
        }

        if (count($waypoints) >= 2) {
            $ultimo = $waypoints[count($waypoints) - 1];
            $tipo = $ultimo['tipo'] ?? 'paso';
            if ($tipo !== 'origen' && self::coordValida((float) $ultimo['lat'], (float) $ultimo['lng'])) {
                return [
                    'lat' => (float) $ultimo['lat'],
                    'lng' => (float) $ultimo['lng'],
                    'zona' => $ultimo['zona'] ?? 'Último punto conocido',
                    'fecha' => $ultimo['fecha'] ?? null,
                    'estado' => $paquete->nombre_estado ?? null,
                    'fuente' => 'ruta',
                ];
            }
        }

        return null;
    }

    /**
     * Ruta completa: almacén → avances → destino final.
     *
     * @param  array<int, array<string, mixed>>  $intermedios
     * @return array<int, array<string, mixed>>
     */
    public static function waypointsCompletos(array $intermedios, ?float $destLat, ?float $destLng): array
    {
        $waypoints = [[
            'lat' => self::ORIGEN_LAT,
            'lng' => self::ORIGEN_LNG,
            'zona' => 'Almacén central',
            'tipo' => 'origen',
        ]];

        foreach ($intermedios as $punto) {
            $lat = (float) ($punto['lat'] ?? 0);
            $lng = (float) ($punto['lng'] ?? 0);
            if (! self::coordValida($lat, $lng)) {
                continue;
            }
            if (self::coordsIguales($lat, $lng, self::ORIGEN_LAT, self::ORIGEN_LNG)) {
                continue;
            }
            if (self::coordValida($destLat, $destLng) && self::coordsIguales($lat, $lng, $destLat, $destLng)) {
                continue;
            }
            $waypoints[] = array_merge($punto, ['tipo' => 'paso']);
        }

        if (self::coordValida($destLat, $destLng)) {
            $waypoints[] = [
                'lat' => $destLat,
                'lng' => $destLng,
                'zona' => 'Destino final',
                'tipo' => 'destino',
            ];
        }

        return self::deduplicarWaypoints($waypoints);
    }

    /** @param array<int, array<string, mixed>> $waypoints */
    private static function deduplicarWaypoints(array $waypoints): array
    {
        $result = [];
        foreach ($waypoints as $wp) {
            $lat = (float) ($wp['lat'] ?? 0);
            $lng = (float) ($wp['lng'] ?? 0);
            $duplicado = false;
            foreach ($result as $existente) {
                if (self::coordsIguales($lat, $lng, (float) $existente['lat'], (float) $existente['lng'])) {
                    $duplicado = true;
                    break;
                }
            }
            if (! $duplicado) {
                $result[] = $wp;
            }
        }

        return $result;
    }

    private static function coordsIguales(float $lat1, float $lng1, float $lat2, float $lng2, float $epsilon = 0.0001): bool
    {
        return abs($lat1 - $lat2) < $epsilon && abs($lng1 - $lng2) < $epsilon;
    }

    public static function origenAlmacen(): array
    {
        return ['lat' => self::ORIGEN_LAT, 'lng' => self::ORIGEN_LNG, 'label' => 'Almacén central'];
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
