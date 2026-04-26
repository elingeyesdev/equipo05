<?php

namespace App\Http\Controllers;

use App\Models\Simulacione;
use App\Models\SimulationFireHistory;
use App\Models\Administrador;
use App\Models\Biomasa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\SimulacioneRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SimulacioneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $simulaciones = Simulacione::with('admin.user')->latest()->paginate();

        return view('simulacione.index', compact('simulaciones'))
            ->with('i', ($request->input('page', 1) - 1) * $simulaciones->perPage());
    }

    /**
     * Show the simulation interface
     */
    public function simulator(): View
    {
        $administradores = Administrador::with('user')->where('activo', true)->get();
        
        // Cargar biomasas para el mapa
        $biomasas = Biomasa::with('tipoBiomasa')
            ->whereNotNull('coordenadas')
            ->get()
            ->map(function ($biomasa) {
                if (is_string($biomasa->coordenadas)) {
                    $biomasa->coordenadas = json_decode($biomasa->coordenadas, true);
                }
                return $biomasa;
            });
        
        return view('simulacione.simulator', compact('administradores', 'biomasas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $simulacione = new Simulacione();

        return view('simulacione.create', compact('simulacione'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SimulacioneRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['ci_usuario'] = auth()->user()->cedula_identidad;
        
        // Calcular riesgo de incendio
        if (isset($data['temperature'], $data['humidity'], $data['wind_speed'])) {
            $data['fire_risk'] = $this->calculateFireRisk(
                $data['temperature'],
                $data['humidity'],
                $data['wind_speed']
            );
        }

        $simulacion = Simulacione::create($data);

        return Redirect::route('simulaciones.index')
            ->with('success', 'Simulación creada exitosamente.');
    }

    /**
     * Save simulation from simulator
     */
    public function saveSimulation(Request $request): JsonResponse
    {
        try {
            // Log para debug
            Log::info('Datos recibidos en saveSimulation:', $request->all());
            
            $validated = $request->validate([
                'nombre' => 'nullable|string|max:255',
                'admin_id' => 'required|integer|exists:administradores,id',
                'duracion' => 'required|integer',
                'focos_activos' => 'required|integer',
                'num_voluntarios_enviados' => 'required|integer',
                'estado' => 'nullable|string',
                'temperature' => 'required|numeric',
                'humidity' => 'required|numeric',
                'wind_speed' => 'required|numeric',
                'wind_direction' => 'required|integer',
                'simulation_speed' => 'required|numeric',
                'fire_risk' => 'required|integer',
                'map_center_lat' => 'nullable|numeric',
                'map_center_lng' => 'nullable|numeric',
                'initial_fires' => 'required|array',
                'mitigation_strategies' => 'nullable|array',
                'auto_stopped' => 'nullable|boolean',
                'fire_history' => 'nullable|array',
            ]);

            // Preparar datos para crear simulación
            $simulationData = [
                'nombre' => $validated['nombre'] ?? 'Simulación ' . now()->format('d/m/Y H:i'),
                'fecha' => now(),
                'duracion' => $validated['duracion'],
                'focos_activos' => $validated['focos_activos'],
                'num_voluntarios_enviados' => $validated['num_voluntarios_enviados'],
                'estado' => $validated['estado'] ?? 'completada',
                'temperature' => $validated['temperature'],
                'humidity' => $validated['humidity'],
                'wind_speed' => $validated['wind_speed'],
                'wind_direction' => $validated['wind_direction'],
                'simulation_speed' => $validated['simulation_speed'],
                'fire_risk' => $validated['fire_risk'],
                'map_center_lat' => $validated['map_center_lat'] ?? null,
                'map_center_lng' => $validated['map_center_lng'] ?? null,
                'initial_fires' => $validated['initial_fires'],
                'mitigation_strategies' => $validated['mitigation_strategies'] ?? [],
                'auto_stopped' => $validated['auto_stopped'] ?? false,
                'admin_id' => $validated['admin_id'],
            ];

            // Simulaciones creadas por administradores son públicas por defecto
            if (auth()->user()?->hasRole('administrador')) {
                $simulationData['public'] = true;
            } else {
                $simulationData['public'] = false;
            }

            $simulacion = Simulacione::create($simulationData);

            // Guardar historial de focos si existe
            if (!empty($validated['fire_history'])) {
                foreach ($validated['fire_history'] as $history) {
                    SimulationFireHistory::create([
                        'simulacion_id' => $simulacion->id,
                        'fire_id' => $history['fire_id'],
                        'time_step' => $history['time_step'],
                        'lat' => $history['lat'],
                        'lng' => $history['lng'],
                        'intensity' => $history['intensity'],
                        'spread' => $history['spread'],
                        'active' => $history['active'] ?? true,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Simulación guardada exitosamente',
                'simulation' => $simulacion
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error guardando simulación: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la simulación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get historical simulations for replay
     */
    public function getHistory(): JsonResponse
    {
        $user = auth()->user();

        // Admins see recent simulations; regular users see their own + public admin simulations
        if ($user && $user->hasRole('administrador')) {
            $collection = Simulacione::with('admin.user')->latest()->take(50)->get();
        } else {
            $ci = $user?->cedula_identidad ?? null;
            $collection = Simulacione::with('admin.user')
                ->where(function ($q) use ($ci) {
                    $q->where('ci_usuario', $ci)
                      ->orWhere('public', true);
                })->latest()->take(50)->get();
        }

        $simulaciones = $collection->map(function ($sim) {
            return [
                'id' => $sim->id,
                'nombre' => $sim->nombre,
                'fecha' => $sim->created_at?->format('d/m/Y'),
                'duracion' => $sim->duracion . 'h',
                'focos' => count($sim->initial_fires ?? []),
                'voluntarios' => $sim->num_voluntarios_enviados,
                'parameters' => [
                    'temperature' => $sim->temperature,
                    'humidity' => $sim->humidity,
                    'windSpeed' => $sim->wind_speed,
                    'windDirection' => $sim->wind_direction,
                    'simulationSpeed' => $sim->simulation_speed,
                ],
                'initialFires' => $sim->initial_fires,
                'public' => $sim->public ?? false,
                'volunteerName' => $sim->admin?->user?->name ?? 'Sistema',
            ];
        });

        return response()->json($simulaciones);
    }

    /**
     * Public endpoint to get a single simulation payload for sharing (no auth)
     */
    public function publicSimulation($id): JsonResponse
    {
        $sim = Simulacione::with('admin.user')->findOrFail($id);

        $payload = [
            'id' => $sim->id,
            'nombre' => $sim->nombre,
            'fecha' => $sim->created_at ? $sim->created_at->format('d/m/Y') : null,
            'duracion' => $sim->duracion . 'h',
            'focos' => count($sim->initial_fires ?? []),
            'voluntarios' => $sim->num_voluntarios_enviados,
            'parameters' => [
                'temperature' => $sim->temperature,
                'humidity' => $sim->humidity,
                'windSpeed' => $sim->wind_speed,
                'windDirection' => $sim->wind_direction,
                'simulationSpeed' => $sim->simulation_speed,
            ],
            'initialFires' => $sim->initial_fires,
            'mitigation_strategies' => $sim->mitigation_strategies ?? [],
            'nombre_admin' => $sim->admin?->user?->name ?? null,
        ];

        return response()->json($payload);
    }

    /**
     * Delete simulation
     */
    public function deleteSimulation($id): JsonResponse
    {
        try {
            $simulacion = Simulacione::findOrFail($id);
            $simulacion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Simulación eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la simulación'
            ], 500);
        }
    }

    /**
     * Calculate fire risk based on environmental parameters
     */
    private function calculateFireRisk($temperature, $humidity, $windSpeed): int
    {
        $tempFactor = min($temperature / 40, 1);
        $humFactor = 1 - ($humidity / 100);
        $windFactor = min($windSpeed / 30, 1);
        
        $risk = ($tempFactor * 0.4 + $humFactor * 0.3 + $windFactor * 0.3) * 100;
        
        return min(round($risk), 100);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $simulacione = Simulacione::find($id);

        return view('simulacione.show', compact('simulacione'));
    }

    /**
     * Display printable PDF report for a simulation.
     */
    public function showPdf($id): View
    {
        $simulacion = Simulacione::with('admin.user')->findOrFail($id);
        return view('reports.simulacion', compact('simulacion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $simulacione = Simulacione::find($id);

        return view('simulacione.edit', compact('simulacione'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SimulacioneRequest $request, Simulacione $simulacione): RedirectResponse
    {
        $simulacione->update($request->validated());

        return Redirect::route('simulaciones.index')
            ->with('success', 'Simulacione updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Simulacione::find($id)->delete();

        return Redirect::route('simulaciones.index')
            ->with('success', 'Simulacione deleted successfully');
    }
}
