<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TipoBiomasaResource;
use App\Models\TipoBiomasa;
use Illuminate\Http\Request;

class TipoBiomasaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipos = TipoBiomasa::withCount('biomasas')->get();
        return TipoBiomasaResource::collection($tipos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_biomasa' => 'required|string|max:255|unique:tipo_biomasas',
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'modificador_intensidad' => 'required|numeric|between:0,10',
        ]);

        $tipo = TipoBiomasa::create($validated);

        return new TipoBiomasaResource($tipo);
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoBiomasa $tipoBiomasa)
    {
        $tipoBiomasa->loadCount('biomasas');
        return new TipoBiomasaResource($tipoBiomasa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoBiomasa $tipoBiomasa)
    {
        $validated = $request->validate([
            'tipo_biomasa' => 'sometimes|string|max:255|unique:tipo_biomasas,tipo_biomasa,' . $tipoBiomasa->id,
            'color' => 'sometimes|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'modificador_intensidad' => 'sometimes|numeric|between:0,10',
        ]);

        $tipoBiomasa->update($validated);

        return new TipoBiomasaResource($tipoBiomasa);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoBiomasa $tipoBiomasa)
    {
        $tipoBiomasa->delete();

        return response()->json([
            'message' => 'Tipo de biomasa eliminado exitosamente',
        ]);
    }
}
