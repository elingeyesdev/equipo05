<?php

namespace Modules\Rescate\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Modules\Rescate\Http\Requests\QuickReportRequest;
use Modules\Rescate\Mail\NewReportNotification;
use Modules\Rescate\Models\AnimalCondition;
use Modules\Rescate\Models\AnimalHistory;
use Modules\Rescate\Models\IncidentType;
use Modules\Rescate\Models\Person;
use Modules\Rescate\Models\Report;
use Modules\Rescate\Models\User;
use Modules\Rescate\Services\Report\ReportUrgencyService;
use Modules\Rescate\Services\User\UserTrackingService;

class QuickReportController extends Controller
{
    public function create(): View
    {
        return view('quick-report');
    }

    public function store(QuickReportRequest $request, ReportUrgencyService $urgencyService): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $tipoNombre = $validated['tipo_emergencia'] === 'incendio' ? 'Incendio' : 'Otro';
            $tipoId = IncidentType::where('nombre', $tipoNombre)->where('activo', true)->value('id');
            $condId = AnimalCondition::where('nombre', 'Desconocido')->where('activo', true)->value('id');

            if (! $tipoId || ! $condId) {
                return Redirect::back()
                    ->withInput()
                    ->withErrors(['general' => 'No se pudo preparar el reporte: catálogo incompleto.']);
            }

            $obsParts = array_filter([
                trim((string) ($validated['descripcion'] ?? '')),
                ! empty($validated['nombre']) ? 'Nombre contacto: '.$validated['nombre'] : null,
                ! empty($validated['telefono']) ? 'Teléfono: '.$validated['telefono'] : null,
            ]);
            $observaciones = implode("\n", $obsParts);

            $isAuthenticated = Auth::check();
            $personId = null;
            if ($isAuthenticated) {
                $personId = Person::where('usuario_id', Auth::id())->value('id');
                if (empty($personId)) {
                    return Redirect::back()
                        ->withInput()
                        ->withErrors(['general' => 'Tu usuario no está vinculado a una persona. Comunícate con el administrador.']);
                }
            }

            $path = $request->file('imagen')->store('reports', 'public');

            $data = [
                'persona_id' => $personId,
                'aprobado' => 0,
                'imagen_url' => $path,
                'observaciones' => $observaciones ?: null,
                'latitud' => $validated['latitud'],
                'longitud' => $validated['longitud'],
                'direccion' => null,
                'condicion_inicial_id' => $condId,
                'tipo_incidente_id' => $tipoId,
                'tamano' => 'mediano',
                'puede_moverse' => true,
            ];

            $data['urgencia'] = $urgencyService->compute($data);

            $report = Report::create($data);
            $report->load(['person', 'condicionInicial', 'incidentType']);

            if ($isAuthenticated) {
                try {
                    app(UserTrackingService::class)->logReportCreation($report, Auth::id());
                } catch (\Exception $e) {
                    Log::warning('Error registrando tracking de creación de reporte: '.$e->getMessage());
                }
            }

            $adminsAndEncargados = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'encargado']);
            })->get();

            foreach ($adminsAndEncargados as $user) {
                try {
                    Mail::to($user->email)->send(new NewReportNotification($report));
                } catch (\Exception $e) {
                    Log::error('Error enviando correo de nuevo reporte: '.$e->getMessage());
                }
            }

            $hist = new AnimalHistory;
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
                    'created_at' => $report->created_at ? $report->created_at->toDateTimeString() : null,
                ],
            ];
            $hist->observaciones = ['texto' => $report->observaciones ?? 'Registro rápido de emergencia'];
            $hist->changed_at = $report->created_at;
            $hist->save();

            if (! $isAuthenticated) {
                $request->session()->put('pending_report_id', $report->id);

                return Redirect::route('rescate.reports.claim')
                    ->with('success', 'El hallazgo se registró correctamente. ¿Deseas conservar este reporte como tuyo?');
            }

            return Redirect::route('rescate.reports.index')
                ->with('success', 'El hallazgo se registró correctamente.');
        } catch (\Throwable $e) {
            Log::error('QuickReport store falló', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            $msg = 'No se pudo registrar el hallazgo en este momento. Intente nuevamente o contacte al administrador.';

            return Redirect::back()
                ->withInput()
                ->withErrors(['general' => $msg]);
        }
    }
}
