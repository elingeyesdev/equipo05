<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Services\FirmsService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request, WeatherService $weather, FirmsService $firms)
    {
        $city = $request->query('city');
        $country = $request->query('country');
        $lat = $request->has('lat') ? (float) $request->query('lat') : null;
        $lon = $request->has('lon') ? (float) $request->query('lon') : null;
        $product = $request->query('product');
        $satellite = $request->query('sat');
        $countryFirms = $request->query('countryFirms', 'BOL');

        // Backwards compatibility: map 'sat' to a default product if 'product' not provided
        if (!$product) {
            $product = match (strtoupper((string) $satellite)) {
                'MODIS' => 'MODIS_NRT',
                default => 'VIIRS_SNPP_NRT',
            };
        }

        if ($lat !== null && $lon !== null) {
            $weatherData = $weather->currentByCoords($lat, $lon);
        } else {
            $weatherData = $weather->currentByCity($city ?? 'Santa Cruz', $country ?? 'BO');
        }
        $firmsData = $firms->activeFires($product, $countryFirms);

        if ($request->wantsJson()) {
            return response()->json([
                'weather' => $weatherData,
                'firms' => $firmsData,
            ]);
        }

        return view('test', [
            'weather' => $weatherData,
            'firms' => $firmsData,
            'params' => compact('city', 'country', 'lat', 'lon', 'product', 'satellite', 'countryFirms'),
        ]);
    }
}
