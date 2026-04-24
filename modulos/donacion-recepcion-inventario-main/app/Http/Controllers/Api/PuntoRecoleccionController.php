<?php

namespace Modules\Inventario\Http\Controllers\Api;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\PuntosRecoleccion;
use Illuminate\Http\Request;

class PuntoRecoleccionController extends Controller
{
    /**
     * GET /api/puntos-de-recoleccion/campana/{id}
     */
    public function getByCampana($idCampana)
    {
        try {
            $puntos = PuntosRecoleccion::where('id_campana', $idCampana)
                ->get();

            return response()->json($puntos, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener puntos de recolección'], 500);
        }
    }

    public function index()
    {
        return PuntosRecoleccion::all();
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
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







