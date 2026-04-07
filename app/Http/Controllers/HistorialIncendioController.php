<?php

namespace App\Http\Controllers;

use App\Models\HistorialIncendio;
use App\Models\Incendio;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistorialIncendioController extends Controller
{
    public function index(Request $request): View
    {
        $incendioId = $request->integer('incendio_id');
        $estadoNuevo = $request->string('estado_nuevo')->toString();

        $historial = HistorialIncendio::query()
            ->with('incendio')
            ->when($incendioId, fn ($query) => $query->where('incendio_id', $incendioId))
            ->when($estadoNuevo, fn ($query) => $query->where('estado_nuevo', $estadoNuevo))
            ->orderByDesc('fecha_cambio')
            ->get();

        $incendios = Incendio::query()
            ->orderBy('titulo')
            ->get(['id', 'titulo']);

        return view('historial.index', compact('historial', 'incendios', 'incendioId', 'estadoNuevo'));
    }
}
