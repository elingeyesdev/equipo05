<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirmsDataService;
use Illuminate\Http\Request;

class FiresController extends Controller
{
    /**
     * Get fire data directly from NASA FIRMS CSV API.
     * 
     * Query params:
     * - product (optional): FIRMS product, default: VIIRS_NOAA20_NRT
     * - area (optional): Bounding box as "west,south,east,north" or "world", default: Chiquitanía area
     * - days (optional): Number of days (1-10), default: 2
     * - cluster (optional): Whether to cluster nearby fires (true/false), default: true
     * - radius (optional): Clustering radius in km, default: 20.0
     * 
     * @param Request $request
     * @param FirmsDataService $firms
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, FirmsDataService $firms)
    {
        $product = $request->query('product', 'VIIRS_NOAA20_NRT');
        // Default area: Chiquitanía, Santa Cruz, Bolivia (west,south,east,north)
        $area = $request->query('area', '-62.5,-18.5,-57.5,-14.5');
        $days = (int) $request->query('days', 2);
        $cluster = filter_var($request->query('cluster', 'true'), FILTER_VALIDATE_BOOLEAN);
        $radius = (float) $request->query('radius', 20.0);

        // Validate days range
        $days = max(1, min(10, $days));
        
        // Validate radius range (0.5km to 50km)
        $radius = max(0.5, min(50.0, $radius));

        $result = $firms->getFireData($product, $area, $days, $cluster, $radius);

        return response()->json($result, $result['status'] ?? 200);
    }
}
