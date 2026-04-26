<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirmsService
{
    /**
     * Fetch active fires from NASA FIRMS JSON area API.
     * Supports product and region (ISO3 country, bbox "west,south,east,north" or "world").
     */
    public function activeFires(string $product = 'VIIRS_SNPP_NRT', string $region = 'BOL', int $days = 1): array
    {
        $apiKey = config('services.firms.key');
        if (!$apiKey || trim($apiKey) === '') {
            return [
                'ok' => false,
                'status' => 401,
                'data' => ['message' => 'Missing FIRMS_API_KEY. Set it in .env.'],
            ];
        }

        $base = 'https://firms.modaps.eosdis.nasa.gov/api/area/json';
        $area = $this->normalizeArea($region);
        // Endpoint pattern: /api/area/json/{api_key}/{product}/{area}/{days}
        $url = sprintf('%s/%s/%s/%s/%d', $base, $apiKey, $product, $area, $days);
        $response = Http::timeout(20)->retry(2, 500)->get($url);
        return [
            'ok' => $response->ok(),
            'status' => $response->status(),
            'data' => $response->json(),
        ];
    }

    protected function normalizeArea(string $region): string
    {
        $trim = trim($region);
        if (strtolower($trim) === 'world') {
            return 'world';
        }
        // bbox: west,south,east,north
        if (preg_match('/^-?\d+(?:\.\d+)?,-?\d+(?:\.\d+)?,-?\d+(?:\.\d+)?,-?\d+(?:\.\d+)?$/', $trim)) {
            return $trim; // already bbox
        }
        return strtoupper($trim); // assume ISO3 country code
    }
}
