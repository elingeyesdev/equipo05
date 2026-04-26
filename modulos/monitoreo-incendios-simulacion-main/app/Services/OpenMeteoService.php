<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class OpenMeteoService
{
    /**
     * Get weather data from Open-Meteo API.
     * Supports both current forecast and historical archive.
     * 
     * @param float $latitude
     * @param float $longitude
     * @param string|null $startDate YYYY-MM-DD for historical data
     * @param string|null $endDate YYYY-MM-DD for historical data
     * @return array
     */
    public function getWeatherData(float $latitude, float $longitude, ?string $startDate = null, ?string $endDate = null): array
    {
        // Cache key for current weather (no date range)
        $cacheKey = "weather_current_{$latitude}_{$longitude}";
        
        // Use cache only for current weather (not historical)
        if (!$startDate && !$endDate) {
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return [
                    'ok' => true,
                    'status' => 200,
                    'data' => $cached,
                    'cached' => true,
                ];
            }
        }

        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'timezone' => 'auto',
        ];

        if ($startDate && $endDate) {
            // Historical archive data
            $url = 'https://archive-api.open-meteo.com/v1/archive';
            $params['start_date'] = $startDate;
            $params['end_date'] = $endDate;
            $params['hourly'] = 'temperature_2m,relative_humidity_2m,precipitation,wind_speed_10m';
        } else {
            // Current forecast data
            $url = 'https://api.open-meteo.com/v1/forecast';
            $params['hourly'] = 'temperature_2m,relative_humidity_2m,precipitation';
            $params['current_weather'] = 'true';
            $params['daily'] = 'temperature_2m_max,temperature_2m_min';
        }

        $response = Http::timeout(15)->retry(2, 500)->get($url, $params);

        $data = $response->json();

        // Cache current weather for 10 minutes
        if (!$startDate && !$endDate && $response->ok()) {
            Cache::put($cacheKey, $data, now()->addMinutes(10));
        }

        return [
            'ok' => $response->ok(),
            'status' => $response->status(),
            'data' => $data,
            'cached' => false,
        ];
    }

    /**
     * Get current weather only.
     */
    public function getCurrentWeather(float $latitude, float $longitude): array
    {
        return $this->getWeatherData($latitude, $longitude);
    }

    /**
     * Get historical weather data.
     */
    public function getHistoricalWeather(float $latitude, float $longitude, string $startDate, string $endDate): array
    {
        return $this->getWeatherData($latitude, $longitude, $startDate, $endDate);
    }
}
