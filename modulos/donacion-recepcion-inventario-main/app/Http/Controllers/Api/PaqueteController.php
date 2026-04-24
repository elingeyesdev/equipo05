<?php

namespace Modules\Inventario\Http\Controllers\Api;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\Paquete;
use Illuminate\Http\JsonResponse;

class PaqueteController extends Controller
{
    /**
     * Obtener información completa de un paquete por su código
     * 
     * @param string $codigo
     * @return JsonResponse
     */
    public function porCodigo(string $codigo): JsonResponse
    {
        // Buscar paquete por código (incluyendo eliminados para trazabilidad)
        $paquete = Paquete::withTrashed()
            ->where('codigo_paquete', $codigo)
            ->with([
                'paqueteDetalles.donacionDetalle.producto:id_producto,nombre,descripcion',
                'paqueteDetalles.donacionDetalle.donacion.donante:id_donante,nombre',
                'paqueteDetalles.donacionDetalle.donacion.campana:id_campana,nombre',
                'registrosSalidas'
            ])
            ->first();

        if (!$paquete) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un paquete con el código proporcionado',
                'codigo' => $codigo
            ], 404);
        }

        // Obtener usuario que registró el paquete
        $usuarioRegistro = null;
        if ($paquete->ci_usuario_registro) {
            $usuario = \Modules\Inventario\Models\Usuario::where('ci', $paquete->ci_usuario_registro)->first();
            if ($usuario) {
                $usuarioRegistro = [
                    'ci' => $usuario->ci,
                    'nombre_completo' => $usuario->nombres . ' ' . $usuario->apellidos,
                ];
            }
        }

        // Mapear detalles del paquete
        $detalles = $paquete->paqueteDetalles->map(function ($detalle) {
            $donacionDetalle = $detalle->donacionDetalle;
            return [
                'id_detalle' => $detalle->id_detalle,
                'cantidad_usada' => $detalle->cantidad_usada,
                'producto' => $donacionDetalle && $donacionDetalle->producto ? [
                    'id' => $donacionDetalle->producto->id_producto,
                    'nombre' => $donacionDetalle->producto->nombre,
                    'descripcion' => $donacionDetalle->producto->descripcion,
                ] : null,
                'donacion' => $donacionDetalle && $donacionDetalle->donacion ? [
                    'id' => $donacionDetalle->donacion->id_donacion,
                    'fecha' => $donacionDetalle->donacion->fecha,
                    'tipo' => $donacionDetalle->donacion->tipo,
                    'donante' => $donacionDetalle->donacion->donante ? [
                        'id' => $donacionDetalle->donacion->donante->id_donante,
                        'nombre' => $donacionDetalle->donacion->donante->nombre,
                    ] : null,
                    'campana' => $donacionDetalle->donacion->campana ? [
                        'id' => $donacionDetalle->donacion->campana->id_campana,
                        'nombre' => $donacionDetalle->donacion->campana->nombre,
                    ] : null,
                ] : null,
            ];
        });

        // Mapear registros de salida
        $registrosSalida = $paquete->registrosSalidas->map(function ($registro) {
            return [
                'id' => $registro->id_salida,
                'fecha_salida' => $registro->fecha_salida,
                'destino' => $registro->destino,
                'encargado' => $registro->encargado,
                'observaciones' => $registro->observaciones,
            ];
        });

        // Construir respuesta
        return response()->json([
            'success' => true,
            'paquete' => [
                'id' => $paquete->id_paquete,
                'codigo' => $paquete->codigo_paquete,
                'fecha_creacion' => $paquete->fecha_creacion,
                'estado' => $paquete->estado,
                'codigo_solicitud_externa' => $paquete->codigo_solicitud_externa,
                'eliminado' => $paquete->deleted_at ? true : false,
                'fecha_eliminacion' => $paquete->deleted_at,
                'razon_eliminacion' => $paquete->deleted_reason,
                'usuario_registro' => $usuarioRegistro,
                'total_productos' => $detalles->count(),
                'total_registros_salida' => $registrosSalida->count(),
            ],
            'detalles' => $detalles,
            'registros_salida' => $registrosSalida,
        ]);
    }
}







