<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Incendio;
=======
use App\Models\HistorialIncendio;
use App\Models\Incendio;
use App\Models\Notificacion;
>>>>>>> origin/santiago
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
<<<<<<< HEAD
        $request->merge([
            'fecha_fin' => $request->input('fecha_fin') ?: null,
        ]);

        $validated = $this->validateIncendio($request);

        Incendio::create($validated);

        return redirect()->route('home')->with('success', 'Incendio registrado correctamente.');
=======
        $data = $request->validate($this->rules());

        $incendio = Incendio::create($data);

        HistorialIncendio::create([
            'incendio_id' => $incendio->id,
            'estado_anterior' => null,
            'estado_nuevo' => $incendio->estado,
            'descripcion' => 'Se registró un nuevo incendio en el sistema.',
            'fecha_cambio' => now(),
        ]);

        Notificacion::create([
            'incendio_id' => $incendio->id,
            'mensaje' => "Nuevo incendio reportado: {$incendio->titulo}. Estado: {$incendio->estado}.",
            'tipo' => $incendio->nivel_riesgo === 'alto' ? 'emergencia' : 'alerta',
            'leido' => false,
        ]);

        return redirect()
            ->route('monitoreo.index')
            ->with('success', 'Incendio registrado correctamente.');
>>>>>>> origin/santiago
    }

    public function edit(Incendio $incendio): View
    {
        return view('monitoreo.edit', compact('incendio'));
    }

    public function update(Request $request, Incendio $incendio): RedirectResponse
    {
<<<<<<< HEAD
        $request->merge([
            'fecha_fin' => $request->input('fecha_fin') ?: null,
        ]);

        $validated = $this->validateIncendio($request);

        $incendio->update($validated);

        return redirect()->route('home')->with('success', 'Incendio actualizado correctamente.');
=======
        $data = $request->validate($this->rules());
        $estadoAnterior = $incendio->estado;

        $incendio->update($data);

        if ($estadoAnterior !== $incendio->estado) {
            HistorialIncendio::create([
                'incendio_id' => $incendio->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $incendio->estado,
                'descripcion' => "Cambio de estado de {$estadoAnterior} a {$incendio->estado}.",
                'fecha_cambio' => now(),
            ]);

            Notificacion::create([
                'incendio_id' => $incendio->id,
                'mensaje' => "Actualización de estado en {$incendio->titulo}: {$estadoAnterior} -> {$incendio->estado}.",
                'tipo' => 'info',
                'leido' => false,
            ]);
        }

        return redirect()
            ->route('monitoreo.index')
            ->with('success', 'Incendio actualizado correctamente.');
>>>>>>> origin/santiago
    }

    public function destroy(Incendio $incendio): RedirectResponse
    {
<<<<<<< HEAD
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
=======
        $titulo = $incendio->titulo;
        $incendio->delete();

        return redirect()
            ->route('monitoreo.index')
            ->with('success', "Incendio {$titulo} eliminado correctamente.");
    }

    private function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
            'estado' => ['required', 'in:activo,controlado,extinguido'],
            'nivel_riesgo' => ['required', 'in:bajo,medio,alto'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ];
>>>>>>> origin/santiago
    }
}
