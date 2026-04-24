<?php

namespace Modules\Inventario\Http\Controllers\Api;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\Campana;
use Illuminate\Http\Request;

class CampanaController extends Controller
{
    /**
     * GET /api/campanas - Lista campañas activas
     */
    public function index()
    {
        try {
            $campanas = Campana::select('id_campana', 'nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'imagen_banner')
                ->where('fecha_fin', '>=', now())
                ->orderBy('fecha_inicio', 'desc')
                ->get();

            return response()->json($campanas, 200);

        } catch (\Exception $e) {
            \Log::error('Error en GET /api/campanas: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al obtener campañas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $campana = Campana::with(['puntosRecoleccion', 'donaciones'])->findOrFail($id);
            return response()->json($campana, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Campaña no encontrada'], 404);
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}







