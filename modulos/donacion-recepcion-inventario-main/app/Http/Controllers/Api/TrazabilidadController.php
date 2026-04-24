<?php

namespace Modules\Inventario\Http\Controllers\Api;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\Usuario;
use Modules\Inventario\Models\Paquete;
use Modules\Inventario\Models\Donacione;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TrazabilidadController extends Controller
{
    /**
     * Obtener todas las acciones realizadas por un voluntario identificado por su CI
     * 
     * @param string $ci
     * @return JsonResponse
     */
    public function porVoluntario(string $ci): JsonResponse
    {
        // Buscar usuario por CI
        $usuario = Usuario::where('ci', $ci)->first();

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un usuario con el CI proporcionado',
                'ci' => $ci
            ], 404);
        }

        // Obtener paquetes creados por este usuario (incluyendo eliminados)
        $paquetesCreados = Paquete::withTrashed()
            ->where('ci_usuario_registro', $ci)
            ->orderBy('fecha_creacion', 'desc')
            ->get()
            ->map(function ($paquete) {
                return [
                    'id' => $paquete->id_paquete,
                    'codigo' => $paquete->codigo_paquete,
                    'fecha' => $paquete->fecha_creacion,
                    'estado' => $paquete->estado,
                    'eliminado' => $paquete->deleted_at ? true : false,
                    'fecha_eliminacion' => $paquete->deleted_at,
                    'razon_eliminacion' => $paquete->deleted_reason,
                ];
            });

        // Obtener paquetes eliminados por este usuario (busca por CI o por id_usuario para compatibilidad)
        $paquetesEliminados = Paquete::onlyTrashed()
            ->where(function ($query) use ($ci, $usuario) {
                $query->where('deleted_by', $ci)
                      ->orWhere('deleted_by', (string) $usuario->id_usuario);
            })
            ->orderBy('deleted_at', 'desc')
            ->get()
            ->map(function ($paquete) {
                return [
                    'id' => $paquete->id_paquete,
                    'codigo' => $paquete->codigo_paquete,
                    'fecha_creacion' => $paquete->fecha_creacion,
                    'fecha_eliminacion' => $paquete->deleted_at,
                    'razon_eliminacion' => $paquete->deleted_reason,
                ];
            });

        // Obtener donaciones registradas por este usuario (incluyendo info de eliminadas)
        $donacionesRegistradas = Donacione::withTrashed()
            ->where('ci_usuario_registro', $ci)
            ->orderBy('fecha', 'desc')
            ->with(['donante:id_donante,nombre', 'campana:id_campana,nombre'])
            ->get()
            ->map(function ($donacion) {
                return [
                    'id' => $donacion->id_donacion,
                    'tipo' => $donacion->tipo,
                    'fecha' => $donacion->fecha,
                    'observaciones' => $donacion->observaciones,
                    'eliminado' => $donacion->deleted_at ? true : false,
                    'fecha_eliminacion' => $donacion->deleted_at,
                    'razon_eliminacion' => $donacion->deleted_reason,
                    'donante' => $donacion->donante ? [
                        'id' => $donacion->donante->id_donante,
                        'nombre' => $donacion->donante->nombre,
                    ] : null,
                    'campana' => $donacion->campana ? [
                        'id' => $donacion->campana->id_campana,
                        'nombre' => $donacion->campana->nombre,
                    ] : null,
                ];
            });

        // Obtener donaciones eliminadas por este usuario (busca por CI o por id_usuario para compatibilidad)
        $donacionesEliminadas = Donacione::onlyTrashed()
            ->where(function ($query) use ($ci, $usuario) {
                $query->where('deleted_by', $ci)
                      ->orWhere('deleted_by', (string) $usuario->id_usuario);
            })
            ->orderBy('fecha', 'desc')
            ->with(['donante:id_donante,nombre', 'campana:id_campana,nombre'])
            ->get()
            ->map(function ($donacion) {
                return [
                    'id' => $donacion->id_donacion,
                    'tipo' => $donacion->tipo,
                    'fecha' => $donacion->fecha,
                    'fecha_eliminacion' => $donacion->deleted_at,
                    'razon_eliminacion' => $donacion->deleted_reason,
                    'donante' => $donacion->donante ? [
                        'id' => $donacion->donante->id_donante,
                        'nombre' => $donacion->donante->nombre,
                    ] : null,
                    'campana' => $donacion->campana ? [
                        'id' => $donacion->campana->id_campana,
                        'nombre' => $donacion->campana->nombre,
                    ] : null,
                ];
            });

        // Construir respuesta con resumen
        return response()->json([
            'success' => true,
            'usuario' => [
                'id' => $usuario->id_usuario,
                'ci' => $usuario->ci,
                'nombre_completo' => $usuario->nombres . ' ' . $usuario->apellidos,
                'correo' => $usuario->correo,
            ],
            'resumen' => [
                'total_paquetes_creados' => $paquetesCreados->count(),
                'total_paquetes_eliminados' => $paquetesEliminados->count(),
                'total_donaciones_registradas' => $donacionesRegistradas->count(),
                'total_donaciones_eliminadas' => $donacionesEliminadas->count(),
            ],
            'acciones' => [
                'paquetes_creados' => $paquetesCreados,
                'paquetes_eliminados' => $paquetesEliminados,
                'donaciones_registradas' => $donacionesRegistradas,
                'donaciones_eliminadas' => $donacionesEliminadas,
            ]
        ]);
    }
}







