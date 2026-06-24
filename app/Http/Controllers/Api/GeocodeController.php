<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OpenStreetMapGeocodingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeocodeController extends Controller
{
    public function lugar(Request $request, OpenStreetMapGeocodingService $geocoder): JsonResponse
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $lat = (float) $validated['lat'];
        $lng = (float) $validated['lng'];

        $lugar = $geocoder->reverse($lat, $lng);

        return response()->json([
            'lugar' => $lugar ?? 'Zona no identificada',
            'zona' => $lugar ?? 'Zona no identificada',
            'lat' => $lat,
            'lng' => $lng,
        ]);
    }
}
