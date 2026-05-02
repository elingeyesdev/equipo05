<?php

namespace Modules\Incendios\Http\Controllers;

use Modules\Incendios\Models\Biomasa;
use Modules\Incendios\Models\TipoBiomasa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Incendios\Http\Requests\BiomasaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class BiomasaController extends Controller
{
    /**
     * Display a listing of the resource.
     * - Voluntarios: solo ven sus propias biomasas
     * - Administradores: ven todas las biomasas pendientes de moderación
     */
    public function index(Request $request): View
    {
        $biomasas = Biomasa::with(['tipoBiomasa', 'user'])
            ->latest()
            ->paginate(15);

        return view('biomasa.admin-index', compact('biomasas'))
            ->with('i', ($request->input('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $biomasa = new Biomasa();
        $tipoBiomasas = TipoBiomasa::all();

        return view('biomasa.create', compact('biomasa', 'tipoBiomasas'));
    }

    /**
     * Store a newly created resource in storage.
     * - Voluntarios: biomasas en estado 'pendiente' (requieren aprobación)
     * - Administradores: biomasas aprobadas automáticamente
     */
    public function store(BiomasaRequest $request): RedirectResponse
    {
        \Log::info('INICIO store() - Request recibido', [
            'user_id' => auth()->id(),
            'all_data' => $request->all(),
            'method' => $request->method()
        ]);
        
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();
            $data['ci_usuario'] = auth()->user()->cedula_identidad;
            
            // Las coordenadas se convierten automáticamente a JSON en el mutator del modelo
            // Si viene como string JSON, el mutator lo maneja
            if (isset($data['coordenadas']) && is_string($data['coordenadas'])) {
                $decoded = json_decode($data['coordenadas'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data['coordenadas'] = $decoded;
                }
            }
            
            // Valores por defecto
            if (!isset($data['densidad']) || empty($data['densidad'])) {
                $data['densidad'] = 'media';
            }
            
            if (!isset($data['area_m2']) || empty($data['area_m2'])) {
                $data['area_m2'] = 0;
            }
            
            if (!isset($data['fecha_reporte']) || empty($data['fecha_reporte'])) {
                $data['fecha_reporte'] = now()->toDateString();
            }
            
            $data['estado'] = 'aprobada';
            $data['aprobada_por'] = auth()->id();
            $data['fecha_revision'] = now();
            $successMessage = 'Biomasa creada y aprobada exitosamente.';
            
            $biomasa = Biomasa::create($data);
            
            \Log::info('Biomasa creada exitosamente', [
                'id' => $biomasa->id,
                'user_id' => auth()->id(),
                'estado' => $data['estado']
            ]);

            return Redirect::route('incendios.biomasas.index')
                ->with('success', $successMessage);
                
        } catch (\Exception $e) {
            \Log::error('Error al crear biomasa: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Redirect::back()
                ->withInput()
                ->with('error', 'Error al crear la biomasa: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $biomasa = Biomasa::findOrFail($id);

        return view('biomasa.show', compact('biomasa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $biomasa = Biomasa::findOrFail($id);
        $tipoBiomasas = TipoBiomasa::all();

        return view('biomasa.edit', compact('biomasa', 'tipoBiomasas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BiomasaRequest $request, Biomasa $biomasa): RedirectResponse
    {
        $biomasa->update($request->validated());

        return Redirect::route('incendios.biomasas.index')
            ->with('success', 'Biomasa updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Biomasa::findOrFail($id)->delete();

        return Redirect::route('incendios.biomasas.index')
            ->with('success', 'Biomasa deleted successfully');
    }
    
    /**
     * Aprobar una biomasa (solo administradores)
     */
    public function aprobar($id): RedirectResponse
    {
        $biomasa = Biomasa::findOrFail($id);
        
        $biomasa->update([
            'estado' => 'aprobada',
            'aprobada_por' => auth()->id(),
            'fecha_revision' => now(),
            'motivo_rechazo' => null,
        ]);
        
        return back()->with('success', 'Biomasa aprobada exitosamente. Ahora aparecerá en el mapa.');
    }
    
    /**
     * Rechazar una biomasa (solo administradores)
     */
    public function rechazar(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:500',
        ]);
        
        $biomasa = Biomasa::findOrFail($id);
        
        $biomasa->update([
            'estado' => 'rechazada',
            'aprobada_por' => auth()->id(),
            'fecha_revision' => now(),
            'motivo_rechazo' => $request->motivo_rechazo,
        ]);
        
        return back()->with('success', 'Biomasa rechazada.');
    }
}

