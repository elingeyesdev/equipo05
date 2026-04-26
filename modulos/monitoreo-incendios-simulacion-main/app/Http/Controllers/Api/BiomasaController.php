<?php

namespace Modules\Incendios\Http\Controllers\Api;

use Modules\Incendios\Http\Controllers\Controller;
use Modules\Incendios\Http\Resources\BiomasaResource;
use Modules\Incendios\Models\Biomasa;
use Illuminate\Http\Request;

class BiomasaController extends Controller
{
    /**
     * Display a listing of biomasas.
     */
    public function index()
    {
        $biomasas = Biomasa::with('tipoBiomasa', 'user')->latest()->get();
        return BiomasaResource::collection($biomasas);
    }

    /**
     * Store a newly created biomasa.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_biomasa_id' => 'required|exists:tipo_biomasa,id',
            'densidad' => 'required|in:Baja,Media,Alta',
            'coordenadas' => 'required|array|min:3',
            'coordenadas.*' => 'array|size:2',
            'area_m2' => 'required|numeric|min:0',
            'perimetro_m' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'fecha_reporte' => 'required|date',
        ]);

        // El mutator del modelo convierte automáticamente el array a JSON
        $validated['user_id'] = auth()->id(); // Usuario autenticado
        $validated['ci_usuario'] = auth()->user()->cedula_identidad;

        // Si el usuario es administrador, aprobar automáticamente
        if (auth()->user()->hasRole('administrador')) {
            $validated['estado'] = 'aprobada';
            $validated['aprobada_por'] = auth()->id();
            $validated['fecha_revision'] = now();
        } else {
            $validated['estado'] = 'pendiente';
        }

        $biomasa = Biomasa::create($validated);
        
        return new BiomasaResource($biomasa->load('tipoBiomasa', 'user'));
    }

    /**
     * Display the specified biomasa.
     */
    public function show(Biomasa $biomasa)
    {
        return new BiomasaResource($biomasa->load('tipoBiomasa', 'user'));
    }

    /**
     * Update the specified biomasa.
     */
    public function update(Request $request, Biomasa $biomasa)
    {
        $validated = $request->validate([
            'tipo_biomasa_id' => 'sometimes|required|exists:tipo_biomasa,id',
            'densidad' => 'sometimes|required|in:Baja,Media,Alta',
            'coordenadas' => 'sometimes|required|array|min:3',
            'coordenadas.*' => 'array|size:2',
            'area_m2' => 'sometimes|required|numeric|min:0',
            'perimetro_m' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'fecha_reporte' => 'sometimes|required|date',
        ]);

        if (isset($validated['coordenadas'])) {
            // El mutator del modelo convierte automáticamente el array a JSON
        }

        $biomasa->update($validated);
        
        return new BiomasaResource($biomasa->load('tipoBiomasa', 'user'));
    }

    /**
     * Remove the specified biomasa.
     */
    public function destroy(Biomasa $biomasa)
    {
        $biomasa->delete();
        return response()->json(['message' => 'Biomasa eliminada exitosamente'], 200);
    }
}
