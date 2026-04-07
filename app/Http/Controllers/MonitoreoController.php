<?php

namespace App\Http\Controllers;

use App\Models\Incendio;
use Illuminate\View\View;

class MonitoreoController extends Controller
{
    public function index(): View
    {
        $incendios = Incendio::query()
            ->orderByDesc('updated_at')
            ->orderByDesc('fecha_inicio')
            ->get();

        $mapPoints = $incendios->map(function (Incendio $incendio) {
            return [
                'id' => $incendio->id,
                'lat' => (float) $incendio->latitud,
                'lng' => (float) $incendio->longitud,
                'titulo' => $incendio->titulo,
                'estado' => $incendio->estado,
                'nivel_riesgo' => $incendio->nivel_riesgo,
                'editUrl' => route('incendios.edit', $incendio),
            ];
        })->values()->all();

        return view('monitoreo.index', compact('incendios', 'mapPoints'));
    }
}
