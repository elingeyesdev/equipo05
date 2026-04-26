<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PredictionResource;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PredictionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $predictions = Prediction::with('focoIncendio')->get();
        return PredictionResource::collection($predictions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'foco_incendio_id' => 'required|exists:focos_incendios,id',
            'predicted_at' => 'required|date',
            'path' => 'required|array',
            'meta' => 'nullable|array',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['ci_usuario'] = auth()->user()->cedula_identidad;

        $prediction = Prediction::create($validated);
        $prediction->load('focoIncendio');

        return new PredictionResource($prediction);
    }

    /**
     * Display the specified resource.
     */
    public function show(Prediction $prediction)
    {
        $prediction->load('focoIncendio');
        return new PredictionResource($prediction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prediction $prediction)
    {
        $validated = $request->validate([
            'foco_incendio_id' => 'sometimes|exists:focos_incendios,id',
            'predicted_at' => 'sometimes|date',
            'path' => 'sometimes|array',
            'meta' => 'nullable|array',
        ]);

        $prediction->update($validated);
        $prediction->load('focoIncendio');

        return new PredictionResource($prediction);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prediction $prediction)
    {
        $prediction->delete();

        return response()->json([
            'message' => 'Predicción eliminada exitosamente',
        ]);
    }

    /**
     * Generate PDF report for prediction.
     */
    public function generatePdf($id)
    {
        $prediction = Prediction::with('focoIncendio')->findOrFail($id);
        
        // Decodificar el path si está en JSON string
        $path = $prediction->path;
        if (is_string($path)) {
            $path = json_decode($path, true);
        }
        
        $pdf = Pdf::loadView('pdfs.prediction', [
            'prediction' => $prediction,
            'path' => $path ?? []
        ]);
        
        $filename = 'prediccion_' . $prediction->id . '_' . date('YmdHis') . '.pdf';
        
        return $pdf->download($filename);
    }
}
