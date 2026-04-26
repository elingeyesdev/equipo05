<?php

namespace Modules\Rescate\Http\Controllers\Api;

use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Models\AnimalHistory;
use Modules\Rescate\Services\History\AnimalHistoryTimelineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnimalHistoryApiController extends Controller
{
    public function __construct(
        private readonly AnimalHistoryTimelineService $timelineService
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Listado de la última entrada de historial por hoja de animal.
     * Soporta orden por ?order=asc|desc.
     */
    public function index(Request $request): JsonResponse
    {
        $order = $request->get('order', 'desc');
        $histories = $this->timelineService->latestPerAnimalFileOrdered($order, 20);

        return response()->json($histories);
    }

    /**
     * Timeline completo de historial para una hoja de animal.
     * El parámetro {animal_history} se interpreta como animal_file_id.
     */
    public function show(int $animal_history): JsonResponse
    {
        $timeline = $this->timelineService->buildForAnimalFile($animal_history);
        $route = $this->timelineService->buildLocationRoute($animal_history);

        return response()->json([
            'animal_file_id' => $animal_history,
            'timeline'       => $timeline,
            'route'          => $route,
        ]);
    }

    /**
     * No se permite crear historial directamente por API
     * (siempre se genera a través de los servicios transaccionales).
     */
    public function store(): JsonResponse
    {
        return response()->json([
            'message' => 'El historial se genera automáticamente. No se puede crear manualmente vía API.',
        ], 405);
    }
}


