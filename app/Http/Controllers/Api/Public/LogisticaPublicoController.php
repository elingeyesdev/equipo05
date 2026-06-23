<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LogisticaPublicoController extends Controller
{
    public function storeSolicitud(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'apellido' => ['nullable', 'string', 'max:120'],
            'ci' => ['required', 'string', 'max:40'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'comunidad' => ['required', 'string', 'max:120'],
            'provincia' => ['required', 'string', 'max:120'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'tipo_emergencia' => ['required', 'string', 'max:120'],
            'cantidad_personas' => ['required', 'integer', 'min:1'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_necesidad' => ['nullable', 'date'],
            'insumos_necesarios' => ['nullable', 'string'],
        ]);

        $codigo = $this->crearSolicitud($data);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud enviada correctamente.',
            'codigo_seguimiento' => $codigo,
        ], 201);
    }

    public function showSolicitud(string $codigo): JsonResponse
    {
        $conn = DB::connection('logistica');

        $row = $conn->table('solicitud')
            ->join('solicitante', 'solicitud.id_solicitante', '=', 'solicitante.id_solicitante')
            ->join('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
            ->where('solicitud.codigo_seguimiento', $codigo)
            ->select([
                'solicitud.id_solicitud',
                'solicitud.codigo_seguimiento',
                'solicitud.estado',
                'solicitud.tipo_emergencia',
                'solicitud.cantidad_personas',
                'solicitud.fecha_inicio',
                'solicitud.fecha_necesidad',
                'solicitud.fecha_solicitud',
                'solicitud.insumos_necesarios',
                'solicitud.aprobada',
                'solicitud.apoyoaceptado',
                'solicitud.created_at',
                'solicitante.nombre as solicitante_nombre',
                'solicitante.apellido as solicitante_apellido',
                'solicitante.ci as solicitante_ci',
                'solicitante.telefono as solicitante_telefono',
                'destino.comunidad',
                'destino.provincia',
                'destino.direccion',
            ])
            ->first();

        if (! $row) {
            return response()->json([
                'message' => 'No se encontró una solicitud con ese código.',
            ], 404);
        }

        return response()->json([
            'codigo_seguimiento' => $row->codigo_seguimiento,
            'estado' => $row->estado,
            'tipo_emergencia' => $row->tipo_emergencia,
            'cantidad_personas' => (int) $row->cantidad_personas,
            'fecha_inicio' => $row->fecha_inicio,
            'fecha_necesidad' => $row->fecha_necesidad,
            'fecha_solicitud' => $row->fecha_solicitud,
            'insumos_necesarios' => $row->insumos_necesarios,
            'aprobada' => (bool) $row->aprobada,
            'apoyo_aceptado' => (bool) $row->apoyoaceptado,
            'solicitante' => [
                'nombre' => $row->solicitante_nombre,
                'apellido' => $row->solicitante_apellido,
                'ci' => $row->solicitante_ci,
                'telefono' => $row->solicitante_telefono,
            ],
            'destino' => [
                'comunidad' => $row->comunidad,
                'provincia' => $row->provincia,
                'direccion' => $row->direccion,
            ],
        ]);
    }

    public function galeria(): JsonResponse
    {
        $conn = DB::connection('logistica');
        $schema = $conn->getSchemaBuilder();
        $items = collect();

        if ($schema->hasTable('paquete')) {
            $query = $conn->table('paquete')
                ->leftJoin('solicitud', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
                ->leftJoin('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
                ->select([
                    'paquete.id_paquete',
                    'paquete.codigo',
                    'paquete.fecha_entrega',
                    'destino.comunidad',
                    'solicitud.codigo_seguimiento',
                    'solicitud.tipo_emergencia',
                ]);

            $hasImagen = $schema->hasColumn('paquete', 'imagen');
            if ($hasImagen) {
                $query->addSelect('paquete.imagen');
            }

            if ($schema->hasColumn('paquete', 'updated_at')) {
                $query->orderByDesc('paquete.updated_at');
            } else {
                $query->orderByDesc('paquete.id_paquete');
            }

            $items = $query->limit(24)->get()->map(function ($paquete) use ($hasImagen) {
                $entregado = ! empty($paquete->fecha_entrega);
                $codigoLogistica = $paquete->codigo ?? ('PKG-'.$paquete->id_paquete);
                $codigoSeguimiento = $paquete->codigo_seguimiento
                    ?: $this->derivarCodigoSeguimiento($codigoLogistica);
                $codigoTrazabilidad = $this->codigoTrazabilidadInventario(
                    $codigoSeguimiento,
                    $codigoLogistica
                );
                $comunidad = $paquete->comunidad;
                $tipoEmergencia = $paquete->tipo_emergencia;

                $payload = [
                    'id' => $paquete->id_paquete,
                    'codigo' => $codigoLogistica,
                    'nombre' => $this->nombrePaqueteGaleria($comunidad, $tipoEmergencia),
                    'codigo_trazabilidad' => $codigoTrazabilidad,
                    'comunidad' => $comunidad,
                    'fecha_entrega' => $paquete->fecha_entrega,
                    'entregado' => $entregado,
                    'tiene_imagen' => false,
                ];

                if ($hasImagen && ! empty($paquete->imagen)) {
                    $payload['tiene_imagen'] = true;
                    $payload['imagen_base64'] = base64_encode($paquete->imagen);
                }

                return $payload;
            });
        }

        return response()->json([
            'data' => $items->values(),
            'total' => $items->count(),
        ]);
    }

    private function nombrePaqueteGaleria(?string $comunidad, ?string $tipoEmergencia): string
    {
        $destino = trim((string) ($comunidad ?: 'comunidad'));
        $tipo = trim((string) ($tipoEmergencia ?? ''));

        if ($tipo !== '') {
            return ucfirst(str_replace('_', ' ', $tipo)).' · '.$destino;
        }

        return 'Paquete para '.$destino;
    }

    private function derivarCodigoSeguimiento(string $codigoLogistica): string
    {
        $normalizado = strtoupper(trim($codigoLogistica));

        if (str_starts_with($normalizado, 'PKG-SOL-')) {
            return 'SOL-'.substr($normalizado, 8);
        }

        return $codigoLogistica;
    }

    private function codigoTrazabilidadInventario(
        string $codigoSeguimiento,
        string $codigoLogistica
    ): string {
        $seguimiento = strtoupper(trim($codigoSeguimiento));

        if (str_starts_with($seguimiento, 'SOL-')) {
            return 'PKG-INV-'.substr($seguimiento, 4);
        }

        return $codigoSeguimiento ?: $this->derivarCodigoSeguimiento($codigoLogistica);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function crearSolicitud(array $data): string
    {
        $conn = DB::connection('logistica');

        $solicitanteId = $conn->table('solicitante')->insertGetId([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'] ?? null,
            'ci' => $data['ci'],
            'telefono' => $data['telefono'] ?? null,
            'email' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_solicitante');

        $destinoId = $conn->table('destino')->insertGetId([
            'comunidad' => $data['comunidad'],
            'provincia' => $data['provincia'],
            'direccion' => $data['direccion'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_destino');

        $codigo = 'SOL-'.now()->format('YmdHis');

        $conn->table('solicitud')->insert([
            'estado' => 'pendiente',
            'codigo_seguimiento' => $codigo,
            'cantidad_personas' => $data['cantidad_personas'],
            'fecha_inicio' => $data['fecha_inicio'],
            'tipo_emergencia' => $data['tipo_emergencia'],
            'insumos_necesarios' => $data['insumos_necesarios'] ?? null,
            'id_solicitante' => $solicitanteId,
            'id_destino' => $destinoId,
            'fecha_solicitud' => now()->toDateString(),
            'aprobada' => 0,
            'apoyoaceptado' => 0,
            'fecha_necesidad' => $data['fecha_necesidad'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $codigo;
    }
}
