<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenStreetMapGeocodingService
{
    private const NOMINATIM_REVERSE = 'https://nominatim.openstreetmap.org/reverse';

    public function reverse(float $lat, float $lng, int $zoom = 16): ?string
    {
        if (! $this->validCoord($lat, $lng)) {
            return null;
        }

        $cacheKey = 'osm:reverse:'.round($lat, 5).':'.round($lng, 5).':'.$zoom;

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($lat, $lng, $zoom) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => $this->userAgent(),
                    'Accept-Language' => 'es',
                ])->timeout(8)->get(self::NOMINATIM_REVERSE, [
                    'format' => 'json',
                    'lat' => $lat,
                    'lon' => $lng,
                    'zoom' => $zoom,
                    'addressdetails' => 1,
                ]);

                if (! $response->successful()) {
                    return null;
                }

                $data = $response->json();
                if (! is_array($data)) {
                    return null;
                }

                return $this->formatAddress($data);
            } catch (\Throwable $e) {
                Log::debug('OSM reverse geocode failed', [
                    'lat' => $lat,
                    'lng' => $lng,
                    'error' => $e->getMessage(),
                ]);

                return null;
            }
        });
    }

    /** @param array<string, mixed> $data */
    public function formatAddress(array $data): ?string
    {
        $address = $data['address'] ?? null;
        if (is_array($address)) {
            $street = trim(implode(' ', array_filter([
                $address['road'] ?? $address['pedestrian'] ?? $address['path'] ?? null,
                $address['house_number'] ?? null,
            ])));

            $locality = $address['neighbourhood']
                ?? $address['suburb']
                ?? $address['village']
                ?? $address['hamlet']
                ?? null;

            $city = $address['city']
                ?? $address['town']
                ?? $address['municipality']
                ?? $address['county']
                ?? null;

            $region = $address['state'] ?? $address['region'] ?? null;

            $parts = array_values(array_unique(array_filter([$street, $locality, $city, $region])));
            if ($parts !== []) {
                return implode(', ', $parts);
            }
        }

        $display = trim((string) ($data['display_name'] ?? ''));

        return $display !== '' ? $display : null;
    }

    private function validCoord(float $lat, float $lng): bool
    {
        return $lat >= -90 && $lat <= 90
            && $lng >= -180 && $lng <= 180
            && ($lat != 0.0 || $lng != 0.0);
    }

    private function userAgent(): string
    {
        $name = config('app.name', 'Equipo05');

        return $name.' Geocoding/1.0 ('.config('app.url', 'http://localhost').')';
    }
}
