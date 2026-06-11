<?php

namespace Modules\Inventario\Http\Controllers\Api;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\CategoriasProducto;
use Illuminate\Http\JsonResponse;

class CategoriaController extends Controller
{
    /**
     * Get all categories with their products
     */
    public function getAllWithProducts(): JsonResponse
    {
        try {
            $categorias = CategoriasProducto::with(['productos' => function($query) {
                $query->select('id_producto', 'id_categoria', 'nombre', 'descripcion', 'unidad_medida');
            }])
            ->select(
                'id_categoria',
                'nombre',
                'codigo',
                'descripcion',
                'tipo_categoria',
                'unidad_medida',
                'es_perecedero',
                'requiere_fecha_vencimiento',
                'prioridad',
                'condiciones_almacenamiento',
                'recomendaciones_uso',
                'observaciones',
                'color',
                'icono',
                'estado'
            )
            ->ordenEmergencia()
            ->get();

            return response()->json([
                'success' => true,
                'data' => $categorias
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener categorías',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}







