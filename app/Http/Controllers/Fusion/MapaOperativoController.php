<?php

namespace App\Http\Controllers\Fusion;

use App\Http\Controllers\Controller;
use App\Services\UnifiedOperationalMapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapaOperativoController extends Controller
{
    public function __construct(
        private readonly UnifiedOperationalMapService $mapService
    ) {}

    public function index(): View
    {
        $summary = $this->mapService->summary();
        $total = array_sum($summary);

        return view('fusion.mapa-operativo.index', [
            'summary' => $summary,
            'totalMarkers' => $total,
            'layerKeys' => UnifiedOperationalMapService::LAYER_KEYS,
        ]);
    }

    public function capas(Request $request): JsonResponse
    {
        $requested = $request->query('layers');
        $only = null;
        if (is_string($requested) && $requested !== '' && $requested !== 'all') {
            $only = array_values(array_filter(array_map('trim', explode(',', $requested))));
        }

        return response()->json($this->mapService->payload($only));
    }
}
