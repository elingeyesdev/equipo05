<?php

namespace Modules\Rescate\Http\Controllers\Api;

use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Models\Report;
use Modules\Rescate\Models\Person;
use Modules\Rescate\Models\AnimalHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Rescate\Http\Requests\ReportRequest;
use Modules\Rescate\Services\Animal\AnimalTransferTransactionalService;
use Modules\Rescate\Services\Report\ReportUrgencyService;
use App\Models\PersonalAccessToken as CorePersonalAccessToken;
use Modules\Rescate\Models\Species;

class ReportApiController extends Controller
{
    public function __construct(
        private readonly AnimalTransferTransactionalService $transferService,
        private readonly ReportUrgencyService $urgencyService
    ) {
        // Permitir store sin autenticación para endpoints externos
        $this->middleware('auth:sanctum')->except(['store']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Report::latest()->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportRequest $request)
    {
        $data = $request->validated();

        // persona_id del usuario logueado (rescate Sanctum o token core ciudadano)
        if (Auth::check()) {
            $personId = Person::where('usuario_id', Auth::id())->value('id');
            $data['persona_id'] = $personId;
        } elseif ($coreUser = $this->resolveCoreCitizen($request)) {
            $data['persona_id'] = $this->ensurePersonForUsuario($coreUser);
        } else {
            $data['persona_id'] = null;
        }
        $data['aprobado'] = 0;

        // Especie opcional: se refleja en observaciones de forma legible.
        $especieId = $request->input('especie_id');
        if ($especieId) {
            $species = Species::find($especieId);
            if ($species) {
                $userObs = trim((string) ($data['observaciones'] ?? ''));
                $data['observaciones'] = $userObs !== ''
                    ? 'Especie: '.$species->nombre."\n".$userObs
                    : 'Especie: '.$species->nombre;
            }
        }

        // imagen
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $extension = $file->guessExtension() ?: 'jpg';
            $filename = 'report-'.uniqid('', true).'.'.$extension;
            $path = $file->storeAs('reports', $filename, 'public');
            $data['imagen_url'] = $path;
        }

        // urgencia
        $data['urgencia'] = $this->urgencyService->compute($data);

        // guardar hallazgo
        $report = Report::create($data);

        AnimalHistory::recordEvent(
            animalFileId: null,
            estadoAnterior: 'Sin registro previo',
            estadoNuevo: 'Hallazgo reportado',
            observaciones: $report->observaciones ?? 'Registro de Hallazgo',
            oldValues: null,
            newValues: [
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
                    'created_at' => $report->created_at
                        ? $report->created_at->toDateTimeString()
                        : null,
                ],
            ],
            changedAt: $report->created_at,
        );

        // traslado inmediato (primer traslado)
        if ($request->boolean('traslado_inmediato')) {
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

        return response()->json([
            'message' => 'El hallazgo se registró correctamente.',
            'report'  => $report,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        //
    }

    private function resolveCoreCitizen(Request $request): ?Usuario
    {
        $plain = $request->bearerToken();
        if ($plain === null || $plain === '') {
            return null;
        }

        $accessToken = CorePersonalAccessToken::findToken($plain);
        if ($accessToken === null) {
            return null;
        }

        $user = $accessToken->tokenable;

        return $user instanceof Usuario ? $user : null;
    }

    private function ensurePersonForUsuario(Usuario $user): ?int
    {
        $existing = Person::where('usuario_id', $user->getKey())->first();
        if ($existing) {
            return (int) $existing->id;
        }

        $ci = preg_replace('/\D+/', '', (string) ($user->cedula_identidad ?? ''));
        if ($ci === '') {
            $ci = 'CORE-'.$user->getKey();
        }

        $person = Person::create([
            'usuario_id' => $user->getKey(),
            'nombre' => trim((string) $user->nombre.' '.(string) $user->apellido),
            'ci' => $ci,
            'telefono' => $user->telefono,
        ]);

        return (int) $person->id;
    }
}
