<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LogisticaOperativa
{
    public static function solicitudesOperativas(bool $incluirDemo = false): Collection
    {
        if (! Schema::connection('logistica')->hasTable('solicitud')) {
            return collect();
        }

        $query = DB::connection('logistica')
            ->table('solicitud')
            ->leftJoin('solicitante', 'solicitud.id_solicitante', '=', 'solicitante.id_solicitante')
            ->leftJoin('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
            ->leftJoin('paquete as pkg', 'pkg.id_solicitud', '=', 'solicitud.id_solicitud')
            ->leftJoin('estado as est_pkg', 'pkg.estado_id', '=', 'estado.id_estado')
            ->select([
                'solicitud.id_solicitud',
                'solicitud.codigo_seguimiento',
                'solicitud.estado',
                'solicitud.aprobada',
                'solicitud.tipo_emergencia',
                'solicitud.cantidad_personas',
                'solicitud.fecha_inicio',
                'solicitud.fecha_necesidad',
                'solicitud.fecha_solicitud',
                'solicitud.insumos_necesarios',
                'solicitud.created_at',
                'solicitante.nombre as solicitante_nombre',
                'solicitante.apellido as solicitante_apellido',
                'solicitante.ci as solicitante_ci',
                'solicitante.telefono as solicitante_telefono',
                'solicitante.email as solicitante_email',
                'destino.comunidad as destino_comunidad',
                'destino.provincia as destino_provincia',
                'destino.direccion as destino_direccion',
                'pkg.id_paquete as paquete_logistica_id',
                'pkg.codigo as paquete_logistica_codigo',
                'est_pkg.nombre_estado as paquete_estado',
            ])
            ->orderByDesc('solicitud.created_at');

        if (! $incluirDemo) {
            $query->where(function ($q) {
                $q->whereNull('solicitud.codigo_seguimiento')
                    ->orWhere('solicitud.codigo_seguimiento', 'not like', 'LOG-DEMO-%');
            })->where(function ($q) {
                $q->whereNull('solicitante.email')
                    ->orWhere('solicitante.email', 'not like', '%@logistica.demo');
            });
        }

        return $query->get()->map(fn ($row) => self::presentarSolicitud($row));
    }

    public static function paquetesOperativos(bool $incluirDemo = false): Collection
    {
        if (! Schema::connection('logistica')->hasTable('paquete')) {
            return collect();
        }

        $query = DB::connection('logistica')
            ->table('paquete')
            ->leftJoin('solicitud', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
            ->leftJoin('solicitante', 'solicitud.id_solicitante', '=', 'solicitante.id_solicitante')
            ->leftJoin('estado', 'paquete.estado_id', '=', 'estado.id_estado')
            ->select([
                'paquete.id_paquete',
                'paquete.codigo',
                'paquete.ubicacion_actual',
                'paquete.fecha_creacion',
                'paquete.fecha_entrega',
                'paquete.updated_at',
                'solicitud.codigo_seguimiento',
                'solicitud.tipo_emergencia',
                'solicitante.nombre as solicitante_nombre',
                'solicitante.apellido as solicitante_apellido',
                'solicitante.ci as solicitante_ci',
                'estado.nombre_estado as estado_nombre',
            ])
            ->orderByDesc('paquete.updated_at');

        if (! $incluirDemo) {
            $query->where(function ($q) {
                $q->whereNull('paquete.codigo')
                    ->orWhere('paquete.codigo', 'not like', 'PKG-LOG-DEMO-%');
            });
        }

        return $query->get()->map(fn ($row) => self::presentarPaquete($row));
    }

    public static function seguimientosOperativos(bool $incluirDemo = false): Collection
    {
        if (! Schema::connection('logistica')->hasTable('historial_seguimiento_donaciones')) {
            return collect();
        }

        $query = DB::connection('logistica')
            ->table('historial_seguimiento_donaciones')
            ->leftJoin('paquete', 'historial_seguimiento_donaciones.id_paquete', '=', 'paquete.id_paquete')
            ->leftJoin('solicitud', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
            ->select([
                'historial_seguimiento_donaciones.id_historial',
                'historial_seguimiento_donaciones.id_paquete',
                'historial_seguimiento_donaciones.estado',
                'historial_seguimiento_donaciones.fecha_actualizacion',
                'historial_seguimiento_donaciones.vehiculo_placa',
                'historial_seguimiento_donaciones.conductor_nombre',
                'historial_seguimiento_donaciones.conductor_ci',
                'paquete.codigo as paquete_codigo',
                'solicitud.codigo_seguimiento',
            ])
            ->orderByDesc('historial_seguimiento_donaciones.fecha_actualizacion');

        if (! $incluirDemo) {
            $query->where(function ($q) {
                $q->whereNull('paquete.codigo')
                    ->orWhere('paquete.codigo', 'not like', 'PKG-LOG-DEMO-%');
            });
        }

        return $query->get()->map(function ($row) {
            $row->fecha_actualizacion = self::formatearFecha($row->fecha_actualizacion ?? null);

            return $row;
        });
    }

    /** @return array<string, mixed> */
    public static function presentarSolicitud(object $row): array
    {
        $estadoRaw = strtolower(trim((string) ($row->estado ?? 'pendiente')));
        $filtro = self::clasificarEstadoSolicitud($estadoRaw);
        $badge = self::badgeEstadoSolicitud($filtro);

        $inventario = self::paqueteInventarioPorCodigo($row->codigo_seguimiento ?? null);

        return [
            'id_solicitud' => $row->id_solicitud,
            'codigo_seguimiento' => $row->codigo_seguimiento ?: ('SOL-'.$row->id_solicitud),
            'estado' => $row->estado ?? 'pendiente',
            'estado_label' => ucfirst(str_replace('_', ' ', $estadoRaw)),
            'estado_filtro' => $filtro,
            'estado_badge' => $badge,
            'aprobada' => (bool) ($row->aprobada ?? false),
            'tipo_emergencia' => $row->tipo_emergencia ?? '—',
            'cantidad_personas' => (int) ($row->cantidad_personas ?? 0),
            'fecha_inicio' => self::formatearFecha($row->fecha_inicio ?? null),
            'fecha_necesidad' => self::formatearFecha($row->fecha_necesidad ?? null),
            'fecha_solicitud' => self::formatearFecha($row->fecha_solicitud ?? $row->created_at ?? null),
            'insumos' => self::truncar($row->insumos_necesarios ?? '', 120),
            'solicitante_nombre' => trim(($row->solicitante_nombre ?? '').' '.($row->solicitante_apellido ?? '')) ?: '—',
            'solicitante_ci' => $row->solicitante_ci ?? '—',
            'solicitante_telefono' => $row->solicitante_telefono ?? '—',
            'destino_comunidad' => $row->destino_comunidad ?? '—',
            'destino_provincia' => $row->destino_provincia ?? '—',
            'destino_direccion' => $row->destino_direccion ?? '',
            'paquete_logistica_codigo' => $row->paquete_logistica_codigo ?? null,
            'paquete_estado' => $row->paquete_estado ?? null,
            'inventario_paquete_codigo' => $inventario['codigo_paquete'] ?? null,
            'inventario_paquete_estado' => $inventario['estado'] ?? null,
        ];
    }

    /** @return array<string, mixed> */
    public static function presentarPaquete(object $row): array
    {
        $estadoRaw = strtolower((string) ($row->estado_nombre ?? 'pendiente'));
        $filtro = self::clasificarEstadoPaquete($estadoRaw);

        $inventario = self::paqueteInventarioPorCodigo($row->codigo_seguimiento ?? $row->codigo ?? null);

        return [
            'id_paquete' => $row->id_paquete,
            'codigo' => $row->codigo ?? ('PKG-'.$row->id_paquete),
            'estado_nombre' => $row->estado_nombre ?? 'Pendiente',
            'estado_filtro' => $filtro,
            'estado_badge' => self::badgeEstadoPaquete($filtro),
            'ubicacion_actual' => $row->ubicacion_actual ?? '—',
            'fecha_creacion' => self::formatearFecha($row->fecha_creacion ?? null),
            'fecha_entrega' => self::formatearFecha($row->fecha_entrega ?? null),
            'tipo_emergencia' => $row->tipo_emergencia ?? '—',
            'solicitante_nombre' => trim(($row->solicitante_nombre ?? '').' '.($row->solicitante_apellido ?? '')) ?: '—',
            'solicitante_ci' => $row->solicitante_ci ?? '—',
            'codigo_seguimiento' => $row->codigo_seguimiento ?? '—',
            'inventario_paquete_codigo' => $inventario['codigo_paquete'] ?? null,
        ];
    }

    /** @return array{codigo_paquete?: string, estado?: string}|array{} */
    private static function paqueteInventarioPorCodigo(?string $codigo): array
    {
        if (! $codigo || ! Schema::connection('inventario')->hasTable('paquetes')) {
            return [];
        }

        $row = DB::connection('inventario')
            ->table('paquetes')
            ->where('codigo_solicitud_externa', $codigo)
            ->first(['codigo_paquete', 'estado']);

        if (! $row) {
            return [];
        }

        return [
            'codigo_paquete' => $row->codigo_paquete,
            'estado' => $row->estado,
        ];
    }

    private static function clasificarEstadoSolicitud(string $estado): string
    {
        return match (true) {
            str_contains($estado, 'aprobad') => 'aprobada',
            str_contains($estado, 'entreg') => 'entregada',
            str_contains($estado, 'rechaz') || str_contains($estado, 'negad') => 'negada',
            str_contains($estado, 'ruta') || str_contains($estado, 'transit') => 'en_ruta',
            default => 'pendiente',
        };
    }

    private static function clasificarEstadoPaquete(string $estado): string
    {
        return match (true) {
            str_contains($estado, 'entreg') => 'entregado',
            str_contains($estado, 'transit') || str_contains($estado, 'camino') || str_contains($estado, 'tráns') => 'camino',
            str_contains($estado, 'almac') || str_contains($estado, 'armad') => 'armado',
            default => 'pendiente',
        };
    }

    private static function badgeEstadoSolicitud(string $filtro): string
    {
        return match ($filtro) {
            'aprobada', 'entregada' => 'success',
            'negada' => 'danger',
            'en_ruta' => 'info',
            default => 'warning',
        };
    }

    private static function badgeEstadoPaquete(string $filtro): string
    {
        return match ($filtro) {
            'entregado' => 'success',
            'camino' => 'info',
            'armado' => 'primary',
            default => 'warning',
        };
    }

    private static function formatearFecha(mixed $fecha): string
    {
        if (empty($fecha)) {
            return '—';
        }

        try {
            return Carbon::parse($fecha)->format('d/m/Y');
        } catch (\Throwable) {
            return (string) $fecha;
        }
    }

    private static function truncar(string $texto, int $max): string
    {
        $texto = trim($texto);
        if ($texto === '') {
            return '—';
        }

        return mb_strlen($texto) > $max ? mb_substr($texto, 0, $max - 1).'…' : $texto;
    }
}
