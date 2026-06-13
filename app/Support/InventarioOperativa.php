<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventarioOperativa
{
    /** @return array{pendientes_armado: int, empacados: int, en_transito: int, entregados: int, total_vinculados: int} */
    public static function resumenAlmacenLogistica(): array
    {
        $defaults = [
            'pendientes_armado' => 0,
            'empacados' => 0,
            'en_transito' => 0,
            'entregados' => 0,
            'total_vinculados' => 0,
        ];

        if (! Schema::connection('logistica')->hasTable('solicitud')) {
            return $defaults;
        }

        $pendientesArmado = self::solicitudesPendientesArmado()->count();

        if (! Schema::connection('inventario')->hasTable('paquetes')) {
            return array_merge($defaults, ['pendientes_armado' => $pendientesArmado]);
        }

        $porEstado = DB::connection('inventario')
            ->table('paquetes')
            ->whereNotNull('codigo_solicitud_externa')
            ->selectRaw('LOWER(COALESCE(estado, \'pendiente\')) as estado_norm, COUNT(*) as total')
            ->groupBy('estado_norm')
            ->pluck('total', 'estado_norm');

        $empacados = (int) ($porEstado['empacado'] ?? 0);
        $enTransito = (int) (($porEstado['en_transito'] ?? 0) + ($porEstado['en tránsito'] ?? 0));
        $entregados = (int) ($porEstado['entregado'] ?? 0);

        return [
            'pendientes_armado' => $pendientesArmado,
            'empacados' => $empacados,
            'en_transito' => $enTransito,
            'entregados' => $entregados,
            'total_vinculados' => (int) DB::connection('inventario')
                ->table('paquetes')
                ->whereNotNull('codigo_solicitud_externa')
                ->count(),
        ];
    }

    public static function solicitudesPendientesArmado(int $limit = 12): Collection
    {
        if (! Schema::connection('logistica')->hasTable('paquete')) {
            return collect();
        }

        $procesados = collect();
        if (Schema::connection('inventario')->hasTable('paquetes')) {
            $procesados = DB::connection('inventario')
                ->table('paquetes')
                ->whereNotNull('codigo_solicitud_externa')
                ->pluck('codigo_solicitud_externa');
        }

        $estadosAlmacen = DB::connection('logistica')
            ->table('estado')
            ->where(function ($q) {
                $q->whereRaw('LOWER(nombre_estado) LIKE ?', ['%pendiente%'])
                    ->orWhereRaw('LOWER(nombre_estado) LIKE ?', ['%almac%'])
                    ->orWhereRaw('LOWER(nombre_estado) LIKE ?', ['%aprobad%']);
            })
            ->pluck('id_estado');

        $query = DB::connection('logistica')
            ->table('solicitud')
            ->leftJoin('solicitante', 'solicitud.id_solicitante', '=', 'solicitante.id_solicitante')
            ->leftJoin('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
            ->leftJoin('paquete', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
            ->leftJoin('estado', 'paquete.estado_id', '=', 'estado.id_estado')
            ->where(function ($q) {
                $q->where('solicitud.estado', 'aprobada')
                    ->orWhere('solicitud.aprobada', true);
            })
            ->where(function ($q) {
                $q->whereNull('solicitud.codigo_seguimiento')
                    ->orWhere('solicitud.codigo_seguimiento', 'not like', 'LOG-DEMO-%');
            })
            ->select([
                'solicitud.id_solicitud',
                'solicitud.tipo_emergencia',
                'solicitud.cantidad_personas',
                'solicitud.fecha_necesidad',
                'solicitud.insumos_necesarios',
                'solicitud.codigo_seguimiento',
                'solicitante.nombre as solicitante_nombre',
                'solicitante.apellido as solicitante_apellido',
                'destino.comunidad as destino_comunidad',
                'destino.provincia as destino_provincia',
                'paquete.id_paquete',
                'estado.nombre_estado as paquete_estado',
            ])
            ->orderByDesc('solicitud.created_at')
            ->limit($limit * 3);

        if ($estadosAlmacen->isNotEmpty()) {
            $query->where(function ($q) use ($estadosAlmacen) {
                $q->whereNull('paquete.id_paquete')
                    ->orWhereIn('paquete.estado_id', $estadosAlmacen)
                    ->orWhereRaw('LOWER(COALESCE(estado.nombre_estado, \'\')) LIKE ?', ['%almac%'])
                    ->orWhereRaw('LOWER(COALESCE(estado.nombre_estado, \'\')) LIKE ?', ['%pendiente%']);
            });
        }

        return $query->get()
            ->reject(fn ($row) => $procesados->contains($row->codigo_seguimiento))
            ->take($limit)
            ->map(fn ($row) => [
                'ref' => LogisticaOperativa::refSolicitud((int) $row->id_solicitud),
                'id_solicitud' => (int) $row->id_solicitud,
                'solicitante_nombre' => trim(($row->solicitante_nombre ?? '').' '.($row->solicitante_apellido ?? '')) ?: '—',
                'destino' => trim(($row->destino_comunidad ?? '—').', '.($row->destino_provincia ?? '')),
                'tipo_emergencia' => $row->tipo_emergencia ?? '—',
                'cantidad_personas' => (int) ($row->cantidad_personas ?? 0),
                'fecha_necesidad' => self::formatearFecha($row->fecha_necesidad ?? null),
                'insumos' => self::truncar($row->insumos_necesarios ?? '', 80),
                'estado_almacen' => $row->id_paquete ? ($row->paquete_estado ?? 'Pendiente de armado') : 'Sin paquete logístico',
            ]);
    }

    public static function paquetesAlmacenRecientes(int $limit = 8): Collection
    {
        if (! Schema::connection('inventario')->hasTable('paquetes')) {
            return collect();
        }

        return DB::connection('inventario')
            ->table('paquetes')
            ->whereNotNull('codigo_solicitud_externa')
            ->orderByDesc('fecha_creacion')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $log = self::solicitudLogisticaPorCodigo($row->codigo_solicitud_externa);

                return [
                    'ref' => 'PAQ-'.str_pad((string) $row->id_paquete, 4, '0', STR_PAD_LEFT),
                    'estado' => ucfirst(str_replace('_', ' ', (string) ($row->estado ?? 'pendiente'))),
                    'solicitud_ref' => $log['ref'] ?? '—',
                    'solicitante' => $log['solicitante'] ?? '—',
                    'destino' => $log['destino'] ?? '—',
                    'emergencia' => $log['emergencia'] ?? '—',
                ];
            });
    }

    /** @return array{ref?: string, solicitante?: string, destino?: string, emergencia?: string} */
    private static function solicitudLogisticaPorCodigo(?string $codigo): array
    {
        if (! $codigo || ! Schema::connection('logistica')->hasTable('solicitud')) {
            return [];
        }

        $row = DB::connection('logistica')
            ->table('solicitud')
            ->leftJoin('solicitante', 'solicitud.id_solicitante', '=', 'solicitante.id_solicitante')
            ->leftJoin('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
            ->where('solicitud.codigo_seguimiento', $codigo)
            ->first([
                'solicitud.id_solicitud',
                'solicitud.tipo_emergencia',
                'solicitante.nombre',
                'solicitante.apellido',
                'destino.comunidad',
                'destino.provincia',
            ]);

        if (! $row) {
            return [];
        }

        return [
            'ref' => LogisticaOperativa::refSolicitud((int) $row->id_solicitud),
            'solicitante' => trim(($row->nombre ?? '').' '.($row->apellido ?? '')) ?: '—',
            'destino' => trim(($row->comunidad ?? '—').', '.($row->provincia ?? '')),
            'emergencia' => $row->tipo_emergencia ?? '—',
        ];
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
