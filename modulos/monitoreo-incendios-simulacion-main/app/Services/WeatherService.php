<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    protected ?string $apiKey = null;
    public function currentByCity(string $city, string $countryCode = 'BO')
    {
        $this->apiKey = config('services.openweather.key');
        if ($error = $this->validateKey()) {
            return $error;
        }

        $q = $countryCode ? "$city,$countryCode" : $city;
        $url = 'https://api.openweathermap.org/data/2.5/weather';
        $params = [
            'q' => $q,
            'appid' => $this->apiKey,
            'units' => 'metric',
            'lang' => 'es',
        ];
        $response = Http::timeout(15)->retry(2, 500)->get($url, $params);
        return [
            'ok' => $response->ok(),
            'status' => $response->status(),
            'data' => $response->json(),
        ];
    }

    public function currentByCoords(float $lat, float $lon)
    {
        $this->apiKey = config('services.openweather.key');
        if ($error = $this->validateKey()) {
            return $error;
        }

        $url = 'https://api.openweathermap.org/data/2.5/weather';
        $params = [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $this->apiKey,
            'units' => 'metric',
            'lang' => 'es',
        ];
        $response = Http::timeout(15)->retry(2, 500)->get($url, $params);
        return [
            'ok' => $response->ok(),
            'status' => $response->status(),
            'data' => $response->json(),
        ];
    }
    
    protected function validateKey(): ?array
    {
        if (!$this->apiKey || trim($this->apiKey) === '') {
            return [
                'ok' => false,
                'status' => 401,
                'data' => [
                    'cod' => 401,
                    'message' => 'Missing OPENWEATHER_API_KEY. Set it in .env.',
                ],
            ];
        }
        return null;
    }
}
