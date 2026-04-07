<?php

namespace App\Http\Controllers;

use App\Models\Incendio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncendioController extends Controller
{
    public function create(): View
    {
        return view('monitoreo.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'fecha_fin' => $request->input('fecha_fin') ?: null,
        ]);

        $validated = $this->validateIncendio($request);

        Incendio::create($validated);

        return redirect()->route('home')->with('success', 'Incendio registrado correctamente.');
    }

    public function edit(Incendio $incendio): View
    {
        return view('monitoreo.edit', compact('incendio'));
    }

    public function update(Request $request, Incendio $incendio): RedirectResponse
    {
        $request->merge([
            'fecha_fin' => $request->input('fecha_fin') ?: null,
        ]);

        $validated = $this->validateIncendio($request);

        $incendio->update($validated);

        return redirect()->route('home')->with('success', 'Incendio actualizado correctamente.');
    }

    public function destroy(Incendio $incendio): RedirectResponse
    {
        $incendio->delete();

        return redirect()->route('home')->with('success', 'Incendio eliminado correctamente.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateIncendio(Request $request): array
    {
        return $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'estado' => 'required|in:activo,controlado,extinguido',
            'nivel_riesgo' => 'required|in:bajo,medio,alto',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);
    }
}
