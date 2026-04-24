<?php

namespace Modules\Inventario\Http\Controllers\Api;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\Almacene;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    /**
     * GET /api/almacenes - Get all almacenes with estantes and espacios
     */
    public function getAllWithStructure()
    {
        try {
            $almacenes = Almacene::with(['estantes.espacios'])
                ->select('id_almacen', 'nombre', 'direccion', 'latitud', 'longitud')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $almacenes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener almacenes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/almacenes - Simple list
     */
    public function index()
    {
        try {
            $almacenes = Almacene::select('id_almacen', 'nombre', 'direccion', 'latitud', 'longitud')
                ->get();

            return response()->json($almacenes, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener almacenes'], 500);
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        return Almacene::with('estantes')->findOrFail($id);
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







