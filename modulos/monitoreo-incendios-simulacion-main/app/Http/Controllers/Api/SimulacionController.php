<?php

namespace Modules\Incendios\Http\Controllers\Api;

use Modules\Incendios\Http\Controllers\Controller;
use Modules\Incendios\Http\Resources\SimulacioneResource;
use Modules\Incendios\Models\Simulacione;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SimulacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $simulaciones = Simulacione::with('administrador')->get();
        return SimulacioneResource::collection($simulaciones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'parameters' => 'required|array',
            'initial_fires' => 'required|array',
            'history' => 'nullable|array',
        ]);

        // El admin_id se toma del usuario autenticado
        $validated['admin_id'] = $request->user()->administrador->id ?? null;

        if (!$validated['admin_id']) {
            return response()->json([
                'message' => 'Solo los administradores pueden crear simulaciones',
            ], 403);
        }

        $simulacion = Simulacione::create($validated);
        $simulacion->load('administrador');

        return new SimulacioneResource($simulacion);
    }

    /**
     * Display the specified resource.
     */
    public function show(Simulacione $simulacione)
    {
        $simulacione->load('administrador');
        return new SimulacioneResource($simulacione);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Simulacione $simulacione)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'parameters' => 'sometimes|array',
            'initial_fires' => 'sometimes|array',
            'history' => 'nullable|array',
            'estado' => 'sometimes|string|max:50',
        ]);

        $simulacione->update($validated);
        $simulacione->load('administrador');

        return new SimulacioneResource($simulacione);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Simulacione $simulacione)
    {
        $simulacione->delete();

        return response()->json([
            'message' => 'Simulación eliminada correctamente.',
        ]);
    }

    /**
     * Generate PDF report for simulation.
     */
    public function generatePdf($id)
    {
        $simulacion = Simulacione::with('administrador')->findOrFail($id);
        
        $pdf = Pdf::loadView('pdfs.simulacion', [
            'simulacion' => $simulacion
        ]);
        
        $filename = 'simulacion_' . $simulacion->id . '_' . date('YmdHis') . '.pdf';
        
        return $pdf->download($filename);
    }
}
