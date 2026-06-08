<?php

namespace Modules\Rescate\Http\Controllers;

use Modules\Rescate\Models\Report;
use Modules\Rescate\Models\Person;
use Modules\Rescate\Models\Center;
use Modules\Rescate\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Rescate\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\Rescate\Services\Animal\AnimalTransferTransactionalService;
use Modules\Rescate\Services\Report\ReportUrgencyService;
use Modules\Rescate\Services\Fire\FocosCalorService;
use Modules\Rescate\Services\Fire\ExternalFireReportsService;
use Modules\Rescate\Services\Fire\MapaCampoDataService;
use Modules\Rescate\Models\AnimalCondition;
use Modules\Rescate\Models\IncidentType;
use Modules\Rescate\Models\AnimalHistory;
use Modules\Rescate\Mail\NewReportNotification;
use Modules\Rescate\Services\User\UserTrackingService;
use Modules\Rescate\Models\Animal;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\Species;
use Modules\Rescate\Models\Release;
use Modules\Rescate\Models\Transfer;

class ReportController extends Controller
{
    public function __construct(
        private readonly AnimalTransferTransactionalService $transferService,
        private readonly ReportUrgencyService $urgencyService,
        private readonly FocosCalorService $focosCalorService,
        private readonly ExternalFireReportsService $externalFireReportsService,
        private readonly MapaCampoDataService $mapaCampoDataService
    ) {
        // Permitir create y store sin autenticación (para usuarios anónimos desde landing)
        $this->middleware('auth')->except(['create', 'store']);
        // Solo ciertos roles gestionan reportes en el panel interno
        // Ciudadanos solo pueden ver y crear, no editar ni eliminar
        // Solo administradores pueden eliminar reportes
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Report::with([
            'person', 
            'condicionInicial', 
            'incidentType', 
            'firstTransfer.center',
            'animals.animalFiles.species',
            'animals.animalFiles.release',
            'transfers' => function($query) {
                $query->where('primer_traslado', true);
            }
        ])
            ->orderByDesc('id');

        // Filters
        if ($request->filled('urgencia_nivel')) {
            $nivel = $request->string('urgencia_nivel')->toString();
            if ($nivel === 'alta') {
                // 4-5
                $query->where('urgencia', '>=', 4);
            } elseif ($nivel === 'media') {
                // 3
                $query->where('urgencia', 3);
            } elseif ($nivel === 'baja') {
                // 1-2
                $query->where('urgencia', '<=', 2);
            }
        }
        if ($request->filled('persona_id')) {
            $query->where('persona_id', $request->input('persona_id'));
        }
        if ($request->filled('tipo_incidente_id')) {
            $query->where('tipo_incidente_id', $request->input('tipo_incidente_id'));
        }
        if ($request->filled('aprobado')) {
            // aprobado can be '1' or '0'
            $query->where('aprobado', (int) $request->input('aprobado'));
        }

        $reports = $query->paginate(12)->withQueryString();

        $reporters = Person::whereIn(
                'id',
                Report::select('persona_id')->whereNotNull('persona_id')->distinct()->pluck('persona_id')
            )
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
        $incidentTypes = IncidentType::where('activo', true)->orderBy('nombre')->get(['id','nombre']);

        return view('report.index', compact('reports', 'reporters', 'incidentTypes'))
            ->with('i', ($request->input('page', 1) - 1) * $reports->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $report = new Report();

        $centers = Center::orderBy('nombre')->get(['id','nombre','latitud','longitud']);
        $conditions = AnimalCondition::where('activo', true)->orderBy('nombre')->get(['id','nombre']);
        $incidentTypes = IncidentType::where('activo', true)->orderBy('nombre')->get(['id','nombre']);

        // Detectar si viene desde landing o desde el panel interno
        $referer = $request->headers->get('referer');
        $hasFromParam = $request->has('from') && $request->get('from') === 'landing';
        $fromLanding = $referer && (str_contains($referer, route('rescate.landing', [], false)) || str_contains($referer, '/landing'));
        $fromReports = $referer && (str_contains($referer, route('rescate.reports.index', [], false)) || str_contains($referer, '/reports'));
        
        // Si viene con parámetro 'from=landing' o desde landing, usar formato simple
        $useSimpleFormat = $hasFromParam || $fromLanding;
        
        // Si está autenticado y viene desde reports (o no viene desde landing), usar formato completo
        $useFullFormat = Auth::check() && ($fromReports || (!$fromLanding && !$hasFromParam));

        return view('report.create', compact('report','centers','conditions','incidentTypes', 'useSimpleFormat', 'useFullFormat'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $isAuthenticated = Auth::check();

            // Si el usuario está autenticado, obtener su persona_id
            if ($isAuthenticated) {
                $personId = Person::where('usuario_id', Auth::id())->value('id');
                if (empty($personId)) {
                    return Redirect::back()
                        ->withInput()
                        ->withErrors(['persona_id' => 'Tu usuario no está vinculado a una persona. Comunícate con el administrador.']);
                }
                $data['persona_id'] = $personId;
            } else {
                // Usuario no autenticado: guardar sin persona_id
                $data['persona_id'] = null;
            }
            
            $data['aprobado'] = 0;
           
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('reports', 'public');
                $data['imagen_url'] = $path;
            }
            // Calcular urgencia
            $data['urgencia'] = $this->urgencyService->compute($data);

            $report = Report::create($data);
            
            // NOTA: No asociamos reportes con focos de calor por ID porque:
            // - La API de NASA FIRMS no proporciona IDs de incendios
            // - Los focos de calor son detecciones independientes
            // - La relación se hace por proximidad geográfica cuando se visualiza en el mapa
            
            $report->load(['person', 'condicionInicial', 'incidentType']);

            // Registrar tracking de creación de reporte
            if ($isAuthenticated) {
                try {
                    app(UserTrackingService::class)->logReportCreation($report, Auth::id());
                } catch (\Exception $e) {
                    \Log::warning('Error registrando tracking de creación de reporte: ' . $e->getMessage());
                }
            }

            // Enviar correo a todos los encargados y administradores
            $adminsAndEncargados = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'encargado']);
            })->get();

            foreach ($adminsAndEncargados as $user) {
                try {
                    Mail::to($user->email)->send(new NewReportNotification($report));
                } catch (\Exception $e) {
                    // Log error pero no interrumpir el flujo
                    \Log::error('Error enviando correo de nuevo reporte: ' . $e->getMessage());
                }
            }

            // Registrar evento de reporte en el historial (sin hoja)
            $hist = new AnimalHistory();
            $hist->animal_file_id = null;
            $hist->valores_antiguos = null;
            $hist->valores_nuevos = [
                'report' => [
                    'id' => $report->id,
                    'persona_id' => $report->persona_id,
                    'direccion' => $report->direccion,
                    'latitud' => $report->latitud,
                    'longitud' => $report->longitud,
                    'condicion_inicial_id' => $report->condicion_inicial_id,
                    'tipo_incidente_id' => $report->tipo_incidente_id,
                    'tamano' => $report->tamano,
                    'puede_moverse' => $report->puede_moverse,
                    'urgencia' => $report->urgencia,
                    'imagen_url' => $report->imagen_url,
                    'created_at' => $report->created_at ? $report->created_at->toDateTimeString() : null, // Guardar fecha original del reporte
                ],
            ];
            $hist->observaciones = ['texto' => $report->observaciones ?? 'Registro de hallazgo'];
            $hist->changed_at = $report->created_at;
            $hist->save();

            // Si se marcó traslado inmediato, registrar primer traslado (sin hoja)
            // Solo si hay persona_id (usuario autenticado)
            if ($request->boolean('traslado_inmediato') && $report->persona_id) {
                $tData = [
                    'persona_id' => $report->persona_id,
                    'centro_id' => $request->input('centro_id'),
                    'observaciones' => $report->observaciones,
                    'primer_traslado' => true,
                    'animal_id' => null,
                    'latitud' => $report->latitud,
                    'longitud' => $report->longitud,
                    'reporte_id' => $report->id,
                ];
                $this->transferService->create($tData);
            }

            // Si el usuario NO está autenticado, guardar el reporte en sesión y preguntar si quiere conservarlo
            if (!$isAuthenticated) {
                // Guardar el ID del reporte en la sesión para asociarlo después del login
                $request->session()->put('pending_report_id', $report->id);
                return Redirect::route('rescate.reports.claim')
                    ->with('success', 'El hallazgo se registró correctamente. ¿Deseas conservar este reporte como tuyo?');
            }

            return Redirect::route('rescate.reports.index')
                ->with('success', 'El hallazgo se registró correctamente.');
        } catch (\Throwable $e) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['general' => 'No se pudo registrar el hallazgo en este momento.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $report = Report::with([
            'firstTransfer.center', 
            'incidentType',
            'condicionInicial',
            'animals.animalFiles.species',
            'animals.animalFiles.release',
            'transfers' => function($query) {
                $query->where('primer_traslado', true);
            }
        ])->findOrFail($id);
        
        // Obtener focos de calor cercanos (por proximidad, no por ID)
        $nearbyFocosCalor = $report->getNearbyFocosCalor(20, 7);

        return view('report.show', compact('report', 'nearbyFocosCalor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $report = Report::findOrFail($id);

        $conditions = AnimalCondition::where('activo', true)->orderBy('nombre')->get(['id','nombre']);
        $incidentTypes = IncidentType::where('activo', true)->orderBy('nombre')->get(['id','nombre']);

        return view('report.edit', compact('report','conditions','incidentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReportRequest $request, Report $report): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('reports', 'public');
            $data['imagen_url'] = $path;
        }
        // Recalcular urgencia si cambian parámetros
        $data['urgencia'] = $this->urgencyService->compute(array_merge($report->toArray(), $data));

        $report->update($data);

        return Redirect::route('rescate.reports.index')
            ->with('success', 'El hallazgo se actualizó correctamente');
    }

    /**
     * Approve or reject a report.
     */
    public function approve(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $oldApproved = $report->aprobado;
        $report->aprobado = $validated['action'] === 'approve' ? 1 : 0;
        $report->save();

        // Registrar tracking de aprobación/rechazo de reporte
        if ($oldApproved != $report->aprobado) {
            try {
                app(UserTrackingService::class)->logReportApproval(
                    $report,
                    $report->aprobado == 1,
                    $oldApproved
                );
            } catch (\Exception $e) {
                \Log::warning('Error registrando tracking de aprobación de reporte: ' . $e->getMessage());
            }
        }

        // Registrar en historial si existe
        $hist = AnimalHistory::whereNull('animal_file_id')
            ->whereNotNull('valores_nuevos')
            ->where('valores_nuevos->report->id', $report->id)
            ->first();

        if ($hist) {
            // Actualizar observaciones con la acción de aprobación/rechazo
            $obs = $hist->observaciones ?? [];
            $obsTexto = is_array($obs) ? ($obs['texto'] ?? '') : (string)$obs;
            $accionTexto = $validated['action'] === 'approve' ? 'Aprobado' : 'Rechazado';
            $usuario = Auth::user();
            $aprobador = $usuario?->person?->nombre ?? $usuario?->name ?? 'usuario-sistema';
            $obs['texto'] = $obsTexto . ' | ' . $accionTexto . ' por: ' . $aprobador;
            $hist->observaciones = $obs;
            $hist->save();
        }

        $message = $validated['action'] === 'approve' 
            ? 'El hallazgo ha sido aprobado correctamente.' 
            : 'El hallazgo ha sido rechazado correctamente.';

        // Redirigir a la vista desde donde se llamó (index o show)
        $redirectTo = $request->get('redirect_to', 'reports.index');
        if ($redirectTo === 'show') {
            return Redirect::route('rescate.reports.show', $report->id)
                ->with('success', $message);
        }
        
        return Redirect::route('rescate.reports.index')
            ->with('success', $message);
    }

    public function destroy($id): RedirectResponse
    {
        Report::findOrFail($id)->delete();

        return Redirect::route('rescate.reports.index')
            ->with('success', 'El hallazgo se eliminó correctamente');
    }

    /**
     * Mostrar el mapa de campo con todos los hallazgos e incendios
     */
    public function mapaCampo(): View
    {
        $data = $this->mapaCampoDataService->build();

        return view('report.mapa-campo', [
            'reports' => $data['reports'],
            'focosCalorFormatted' => $data['focosCalorFormatted'],
            'releases' => $data['releases'],
            'species' => $data['species'],
            'operationalFiresFormatted' => $data['operationalFiresFormatted'],
            'firesMapSource' => $data['firesMapSource'],
        ]);
    }

    public function getExternalFireReportsApi()
    {
        $fires = $this->externalFireReportsService->getOperationalFiresForMap();

        return response()->json($fires['items']);
    }

    public function getExternalFireReportDetails(string $externalId)
    {
        $fires = $this->externalFireReportsService->getOperationalFiresForMap();
        $found = collect($fires['items'])->firstWhere('id', is_numeric($externalId) ? (int) $externalId : $externalId);

        if (! $found) {
            return response()->json(['message' => 'Foco de incendio no encontrado'], 404);
        }

        return response()->json($found);
    }

    /**
     * Mostrar página para reclamar el reporte (si el usuario no estaba autenticado)
     */
    public function claim(): View
    {
        $reportId = session('pending_report_id');
        if (!$reportId) {
            return view('reports.claim', ['report' => null]);
        }

        $report = Report::with(['condicionInicial', 'incidentType'])->find($reportId);
        return view('reports.claim', compact('report'));
    }

    /**
     * Procesar la decisión del usuario sobre conservar el reporte
     */
    public function claimStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:yes,no',
        ]);

        $reportId = session('pending_report_id');
        if (!$reportId) {
            return Redirect::route('rescate.landing')
                ->with('info', 'No hay reportes pendientes de asociar.');
        }

        if ($validated['action'] === 'no') {
            // El usuario no quiere conservar el reporte, limpiar sesión y redirigir
            session()->forget('pending_report_id');
            return Redirect::route('rescate.landing')
                ->with('success', 'El reporte se ha registrado correctamente. Gracias por tu colaboración.');
        }

        // El usuario quiere conservar el reporte, mantener en sesión y redirigir a login
        // El reporte ya está en sesión, solo redirigir
        return Redirect::route('login')
            ->with('info', 'Por favor, inicia sesión o regístrate para asociar este reporte a tu cuenta.');
    }
}
