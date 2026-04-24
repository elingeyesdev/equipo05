<?php

namespace Modules\Inventario\Http\Controllers\Api;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\Estante;
use Illuminate\Http\Request;

class EstanteController extends Controller
{
    /**
     * GET /api/estantes
     */
    public function index()
    {
        try {
            $estantes = Estante::with('almacen')->get();
            return response()->json($estantes, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener estantes'], 500);
        }
    }

    /**
     * GET /api/estantes/almacen/{id}
     */
    public function getByAlmacen($idAlmacen)
    {
        try {
            $estantes = Estante::where('id_almacen', $idAlmacen)
                ->with('almacen')
                ->get();

            return response()->json($estantes, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener estantes del almacén'], 500);
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        return Estante::with(['almacen', 'espacios'])->findOrFail($id);
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







