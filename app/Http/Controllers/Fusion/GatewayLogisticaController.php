<?php

namespace App\Http\Controllers\Fusion;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GatewayLogisticaController extends Controller
{
    public function paquetesPendientes(): JsonResponse
    {
        if (! Schema::connection('logistica')->hasTable('paquete')) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $pendienteEstadoIds = DB::connection('logistica')
            ->table('estado')
            ->whereRaw('LOWER(nombre_estado) LIKE ?', ['%pendiente%'])
            ->pluck('id_estado');

        $query = DB::connection('logistica')
            ->table('paquete')
            ->leftJoin('solicitud', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
            ->leftJoin('solicitante', 'solicitud.id_solicitante', '=', 'solicitante.id_solicitante')
            ->leftJoin('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
            ->leftJoin('estado', 'paquete.estado_id', '=', 'estado.id_estado')
            ->select([
                'paquete.id_paquete',
                'paquete.codigo',
                'paquete.fecha_creacion',
                'paquete.ubicacion_actual',
                'estado.id_estado',
                'estado.nombre_estado',
                'solicitud.id_solicitud',
                'solicitud.codigo_seguimiento',
                'solicitud.tipo_emergencia',
                'solicitud.cantidad_personas',
                'solicitud.fecha_inicio',
                'solicitud.fecha_solicitud',
                'solicitud.fecha_necesidad',
                'solicitud.insumos_necesarios',
                'solicitante.nombre as solicitante_nombre',
                'solicitante.apellido as solicitante_apellido',
                'solicitante.ci as solicitante_ci',
                'solicitante.telefono as solicitante_telefono',
                'solicitante.email as solicitante_email',
                'destino.comunidad as destino_comunidad',
                'destino.provincia as destino_provincia',
                'destino.direccion as destino_direccion',
            ])
            ->orderByDesc('paquete.fecha_creacion');

        if ($pendienteEstadoIds->isNotEmpty()) {
            $query->whereIn('paquete.estado_id', $pendienteEstadoIds);
        } else {
            $query->whereRaw('LOWER(COALESCE(estado.nombre_estado, \'\')) LIKE ?', ['%pendiente%']);
        }

        $procesados = collect();
        if (Schema::connection('inventario')->hasTable('paquetes')) {
            $procesados = DB::connection('inventario')
                ->table('paquetes')
                ->whereNotNull('codigo_solicitud_externa')
                ->pluck('codigo_solicitud_externa');
        }

        $rows = $query->limit(100)->get();

        $data = $rows
            ->reject(fn ($row) => $procesados->contains($row->codigo_seguimiento))
            ->map(fn ($row) => $this->mapPaquetePendiente($row))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function armarPaquete(Request $request, int $id): JsonResponse
    {
        if (! Schema::connection('logistica')->hasTable('paquete')) {
            return response()->json(['success' => false, 'message' => 'Módulo logística no disponible.'], 404);
        }

        $validated = $request->validate([
            'ci_usuario' => 'nullable|string|max:40',
            'ubicacion_actual' => 'nullable|string|max:255',
        ]);

        $estadoArmadoId = DB::connection('logistica')
            ->table('estado')
            ->whereRaw('LOWER(nombre_estado) LIKE ?', ['%almac%'])
            ->value('id_estado');

        $update = array_filter([
            'ubicacion_actual' => $validated['ubicacion_actual'] ?? null,
            'updated_at' => now(),
        ]);

        if ($estadoArmadoId) {
            $update['estado_id'] = $estadoArmadoId;
        }

        $updated = DB::connection('logistica')
            ->table('paquete')
            ->where('id_paquete', $id)
            ->update($update);

        if (! $updated) {
            return response()->json(['success' => false, 'message' => 'Paquete no encontrado.'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Paquete marcado como armado en logística.',
            'ci_usuario' => $validated['ci_usuario'] ?? null,
        ]);
    }

    public function destinoVoluntario(string $codigo): JsonResponse
    {
        if (! Schema::connection('logistica')->hasTable('paquete')) {
            return response()->json(['success' => false, 'data' => null], 404);
        }

        $row = DB::connection('logistica')
            ->table('paquete')
            ->leftJoin('solicitud', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
            ->leftJoin('solicitante', 'solicitud.id_solicitante', '=', 'solicitante.id_solicitante')
            ->leftJoin('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
            ->where(function ($query) use ($codigo) {
                $query->where('paquete.codigo', $codigo)
                    ->orWhere('solicitud.codigo_seguimiento', $codigo);
            })
            ->select([
                'destino.direccion',
                'destino.comunidad',
                'destino.provincia',
                'solicitante.nombre',
                'solicitante.apellido',
                'solicitante.telefono',
            ])
            ->first();

        if (! $row) {
            return response()->json(['success' => false, 'data' => null], 404);
        }

        $encargado = trim(($row->nombre ?? '').' '.($row->apellido ?? ''));

        return response()->json([
            'success' => true,
            'data' => [
                'destino' => [
                    'direccion' => $row->direccion ?? $row->comunidad ?? '',
                    'comunidad' => $row->comunidad ?? '',
                    'provincia' => $row->provincia ?? '',
                ],
                'encargado' => [
                    'completo' => $encargado !== '' ? $encargado : ($row->telefono ?? ''),
                    'telefono' => $row->telefono ?? '',
                ],
            ],
        ]);
    }

    private function mapPaquetePendiente(object $row): array
    {
        return [
            'id_paquete' => (int) $row->id_paquete,
            'fecha_creacion' => $row->fecha_creacion,
            'estado' => [
                'id_estado' => $row->id_estado,
                'nombre_estado' => $row->nombre_estado ?? 'Pendiente',
            ],
            'solicitud' => [
                'id_solicitud' => $row->id_solicitud,
                'codigo_seguimiento' => $row->codigo_seguimiento ?? $row->codigo ?? '',
                'tipo_emergencia' => $row->tipo_emergencia,
                'cantidad_personas' => $row->cantidad_personas ?? 0,
                'fecha_inicio' => $row->fecha_inicio,
                'fecha_solicitud' => $row->fecha_solicitud,
                'insumos_necesarios' => $row->insumos_necesarios,
                'solicitante' => [
                    'nombre' => $row->solicitante_nombre ?? 'Sin nombre',
                    'apellido' => $row->solicitante_apellido ?? '',
                    'ci' => $row->solicitante_ci ?? '—',
                    'telefono' => $row->solicitante_telefono ?? '—',
                    'email' => $row->solicitante_email ?? '—',
                ],
                'destino' => [
                    'comunidad' => $row->destino_comunidad ?? '—',
                    'provincia' => $row->destino_provincia ?? '—',
                    'direccion' => $row->destino_direccion ?? '—',
                    'latitud' => '0',
                    'longitud' => '0',
                ],
            ],
        ];
    }
}
