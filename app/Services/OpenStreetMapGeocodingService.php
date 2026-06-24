<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenStreetMapGeocodingService
{
    private const NOMINATIM_REVERSE = 'https://nominatim.openstreetmap.org/reverse';

    public function reverse(float $lat, float $lng, int $zoom = 13): ?string
    {
        if (! $this->validCoord($lat, $lng)) {
            return null;
        }

        $cacheKey = 'osm:zone:v2:'.round($lat, 5).':'.round($lng, 5).':'.$zoom;

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

                return $this->formatZoneLabel($data);
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
    public function formatZoneLabel(array $data): ?string
    {
        $address = $data['address'] ?? null;
        if (is_array($address)) {
            $zone = $address['neighbourhood']
                ?? $address['suburb']
                ?? $address['quarter']
                ?? $address['residential']
                ?? $address['hamlet']
                ?? $address['village']
                ?? $address['isolated_dwelling']
                ?? null;

            $municipio = $address['municipality']
                ?? $address['city_district']
                ?? $address['town']
                ?? $address['city']
                ?? null;

            $departamento = $address['state'] ?? $address['region'] ?? null;

            $street = trim((string) ($address['road'] ?? $address['pedestrian'] ?? $address['path'] ?? ''));

            $parts = [];
            if ($zone) {
                $parts[] = $zone;
            } elseif ($street !== '') {
                $parts[] = $street;
            }

            if ($municipio && ! in_array($municipio, $parts, true)) {
                $parts[] = $municipio;
            }

            if ($departamento && count($parts) < 2 && ! in_array($departamento, $parts, true)) {
                $parts[] = $departamento;
            }

            if ($parts !== []) {
                return implode(', ', $parts);
            }
        }

        return $this->shortDisplayName((string) ($data['display_name'] ?? ''));
    }

    /** @param array<string, mixed> $data */
    public function formatAddress(array $data): ?string
    {
        return $this->formatZoneLabel($data);
    }

    private function shortDisplayName(string $display): ?string
    {
        $display = trim($display);
        if ($display === '') {
            return null;
        }

        $parts = array_values(array_filter(array_map('trim', explode(',', $display))));
        if ($parts !== [] && end($parts) === 'Bolivia') {
            array_pop($parts);
        }

        if ($parts === []) {
            return null;
        }

        return implode(', ', array_slice($parts, 0, min(3, count($parts))));
    }

    private function validCoord(float $lat, float $lng): bool
    {
        return $lat >= -90 && $lat <= 90
            && $lng >= -180 && $lng <= 180
            && ($lat != 0.0 || $lng != 0.0);
    }

    private function userAgent(): string
    {
        $name = config('app.name', 'Alas chiquitanas');

        return $name.' Geocoding/1.0 ('.config('app.url', 'http://localhost').')';
    }
}
