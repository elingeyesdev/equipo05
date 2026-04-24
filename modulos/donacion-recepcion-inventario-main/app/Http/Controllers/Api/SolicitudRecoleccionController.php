<?php

namespace Modules\Inventario\Http\Controllers\Api;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\SolicitudesRecoleccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SolicitudRecoleccionController extends Controller
{
    /**
     * POST /api/solicitudesRecoleccion
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ubicacion' => 'nullable|string|max:500',
            'detalle_solicitud' => 'nullable|string',
            'id_donante' => 'required|exists:donantes,id_donante',
        ]);

        try {
            $solicitud = SolicitudesRecoleccion::create([
                'direccion_recoleccion' => $validated['ubicacion'] ?? null,
                'observaciones' => $validated['detalle_solicitud'] ?? null,
                'id_donante' => $validated['id_donante'],
                'estado' => 'Pendiente',
                'fecha_creacion' => now(),
            ]);

            return response()->json([
                'id' => $solicitud->id_solicitud,
                'message' => 'Solicitud creada exitosamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creando solicitud: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al crear solicitud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/solicitudesRecoleccion/donante/{id}
     */
    public function getByDonante($donanteId)
    {
        try {
            $solicitudes = SolicitudesRecoleccion::where('id_donante', $donanteId)
                ->with(['campana'])
                ->orderBy('fecha_creacion', 'desc')
                ->get();

            return response()->json($solicitudes, 200);

        } catch (\Exception $e) {
            Log::error('Error obteniendo solicitudes: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener solicitudes'], 500);
        }
    }

    public function index()
    {
        return SolicitudesRecoleccion::with(['donante', 'campana'])->paginate(20);
    }

    public function show(string $id)
    {
        return SolicitudesRecoleccion::with(['donante'])->findOrFail($id);
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







