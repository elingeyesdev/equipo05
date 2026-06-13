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
            ->leftJoin('estado as est_pkg', 'pkg.estado_id', '=', 'est_pkg.id_estado')
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

        return $query->get()->map(fn ($row) => self::presentarSolicitud($row, self::perspectivaActual()));
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
                'solicitud.id_solicitud',
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

        return $query->get()->map(fn ($row) => self::presentarPaquete($row, self::perspectivaActual()));
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
                'solicitud.id_solicitud',
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
            $row->paquete_ref = self::refPaquete((int) ($row->id_paquete ?? 0));
            $row->solicitud_ref = self::refSolicitud((int) ($row->id_solicitud ?? 0));

            return $row;
        });
    }

    public static function perspectivaActual(): string
    {
        $user = auth()->user();

        return AccessControl::enfoqueLogistica($user instanceof \App\Models\Usuario ? $user : null);
    }

    /** @return array<string, mixed> */
    public static function presentarSolicitud(object $row, string $perspectiva = 'transporte'): array
    {
        $estadoRaw = strtolower(trim((string) ($row->estado ?? 'pendiente')));
        $filtro = self::clasificarEstadoSolicitud($estadoRaw);
        $badge = self::badgeEstadoSolicitud($filtro);
        $integrado = $perspectiva === 'integrado';

        $inventario = $integrado
            ? self::paqueteInventarioPorCodigo($row->codigo_seguimiento ?? null)
            : [];

        $envio = $integrado
            ? self::etiquetaEnvioIntegrado($row, $filtro, $inventario)
            : self::etiquetaEnvioTransporte($row, $filtro);

        return [
            'id_solicitud' => $row->id_solicitud,
            'ref' => self::refSolicitud((int) $row->id_solicitud),
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
            'paquete_logistica_id' => $row->paquete_logistica_id ?? null,
            'paquete_logistica_ref' => ! empty($row->paquete_logistica_id)
                ? self::refPaquete((int) $row->paquete_logistica_id)
                : null,
            'paquete_logistica_codigo' => $row->paquete_logistica_codigo ?? null,
            'paquete_estado' => $row->paquete_estado ?? null,
            'paquete_estado_badge' => ! empty($row->paquete_estado)
                ? self::badgeEstadoPaquete(self::clasificarEstadoPaquete(strtolower((string) $row->paquete_estado)))
                : null,
            'inventario_vinculado' => $integrado && ! empty($inventario),
            'inventario_paquete_codigo' => $inventario['codigo_paquete'] ?? null,
            'inventario_paquete_estado' => isset($inventario['estado'])
                ? ucfirst(str_replace('_', ' ', (string) $inventario['estado']))
                : null,
            'envio_label' => $envio['label'],
            'envio_badge' => $envio['badge'],
            'envio_detalle' => $envio['detalle'] ?? null,
            'vista_integrada' => $integrado,
        ];
    }

    /** @return array<string, mixed> */
    public static function presentarPaquete(object $row, string $perspectiva = 'transporte'): array
    {
        $estadoRaw = strtolower((string) ($row->estado_nombre ?? 'pendiente'));
        $filtro = self::clasificarEstadoPaquete($estadoRaw);
        $integrado = $perspectiva === 'integrado';

        $inventario = $integrado
            ? self::paqueteInventarioPorCodigo($row->codigo_seguimiento ?? $row->codigo ?? null)
            : [];

        $estadoNombre = $integrado
            ? ($row->estado_nombre ?? 'Pendiente')
            : self::etiquetaEstadoPaqueteTransporte($estadoRaw, $filtro);

        return [
            'id_paquete' => $row->id_paquete,
            'ref' => self::refPaquete((int) $row->id_paquete),
            'id_solicitud' => $row->id_solicitud ?? null,
            'solicitud_ref' => ! empty($row->id_solicitud)
                ? self::refSolicitud((int) $row->id_solicitud)
                : null,
            'codigo' => $row->codigo ?? ('PKG-'.$row->id_paquete),
            'estado_nombre' => $estadoNombre,
            'estado_filtro' => $filtro,
            'estado_badge' => self::badgeEstadoPaquete($filtro),
            'ubicacion_actual' => $row->ubicacion_actual ?? '—',
            'fecha_creacion' => self::formatearFecha($row->fecha_creacion ?? null),
            'fecha_entrega' => self::formatearFecha($row->fecha_entrega ?? null),
            'tipo_emergencia' => $row->tipo_emergencia ?? '—',
            'solicitante_nombre' => trim(($row->solicitante_nombre ?? '').' '.($row->solicitante_apellido ?? '')) ?: '—',
            'solicitante_ci' => $row->solicitante_ci ?? '—',
            'codigo_seguimiento' => $row->codigo_seguimiento ?? '—',
            'inventario_vinculado' => $integrado && ! empty($inventario),
            'inventario_paquete_codigo' => $inventario['codigo_paquete'] ?? null,
            'inventario_paquete_estado' => isset($inventario['estado'])
                ? ucfirst(str_replace('_', ' ', (string) $inventario['estado']))
                : null,
            'vista_integrada' => $integrado,
        ];
    }

    /** @param array{codigo_paquete?: string, estado?: string}|array{} $inventario */
    /** @return array{label: string, badge: string, detalle?: string|null} */
    private static function etiquetaEnvioIntegrado(object $row, string $filtro, array $inventario): array
    {
        $base = self::etiquetaEnvioTransporte($row, $filtro);
        if (! empty($inventario['estado'])) {
            $base['detalle'] = 'Inv.: '.ucfirst(str_replace('_', ' ', (string) $inventario['estado']));
        }

        if (! empty($row->paquete_estado) && $integradoLabel = trim((string) $row->paquete_estado)) {
            $base['label'] = $integradoLabel;
        }

        return $base;
    }

    /** @return array{label: string, badge: string} */
    private static function etiquetaEnvioTransporte(object $row, string $filtro): array
    {
        $paqueteEstado = strtolower((string) ($row->paquete_estado ?? ''));

        if ($filtro === 'rechazada' || $filtro === 'negada') {
            return ['label' => 'No aplica', 'badge' => 'secondary'];
        }

        if ($filtro === 'entregada' || str_contains($paqueteEstado, 'entreg')) {
            return ['label' => 'Entregado', 'badge' => 'success'];
        }

        if ($filtro === 'en_ruta' || str_contains($paqueteEstado, 'transit') || str_contains($paqueteEstado, 'tráns') || str_contains($paqueteEstado, 'camino')) {
            return ['label' => 'En tránsito', 'badge' => 'info'];
        }

        if ($filtro === 'aprobada' || str_contains($paqueteEstado, 'almac') || str_contains($paqueteEstado, 'armad') || str_contains($paqueteEstado, 'pendiente')) {
            if (empty($row->paquete_logistica_id)) {
                return ['label' => 'Esperando armado', 'badge' => 'secondary'];
            }

            return ['label' => 'En preparación', 'badge' => 'secondary'];
        }

        return ['label' => 'Sin despacho', 'badge' => 'secondary'];
    }

    private static function etiquetaEstadoPaqueteTransporte(string $estadoRaw, string $filtro): string
    {
        if ($filtro === 'entregado' || str_contains($estadoRaw, 'entreg')) {
            return 'Entregado';
        }
        if ($filtro === 'camino' || str_contains($estadoRaw, 'transit') || str_contains($estadoRaw, 'tráns') || str_contains($estadoRaw, 'camino')) {
            return 'En tránsito';
        }
        if (str_contains($estadoRaw, 'almac') || str_contains($estadoRaw, 'armad') || str_contains($estadoRaw, 'pendiente')) {
            return 'En preparación';
        }

        return 'Pendiente';
    }

    public static function refSolicitud(int $id): string
    {
        return '#'.str_pad((string) max($id, 0), 4, '0', STR_PAD_LEFT);
    }

    public static function refPaquete(int $id): string
    {
        return '#'.str_pad((string) max($id, 0), 4, '0', STR_PAD_LEFT);
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
