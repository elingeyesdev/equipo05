<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OpenMeteoService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    /**
     * Get weather data (current or historical).
     * 
     * Query params:
     * - latitude (required)
     * - longitude (required)
     * - start_date (optional, YYYY-MM-DD for historical)
     * - end_date (optional, YYYY-MM-DD for historical)
     * 
     * @param Request $request
     * @param OpenMeteoService $weather
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, OpenMeteoService $weather)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $latitude = (float) $request->query('latitude');
        $longitude = (float) $request->query('longitude');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $result = $weather->getWeatherData($latitude, $longitude, $startDate, $endDate);

        // If requesting current weather (no date range), return simplified payload
        if (!$startDate && !$endDate && isset($result['data'])) {
            $data = $result['data'];

            $temperature = null;
            $humidity = null;
            $windSpeed = null;
            $windDirection = null;
            $weatherCode = null;
            $precipitation = null;

            // current_weather contains temperature, windspeed, winddirection, weathercode
            if (isset($data['current_weather'])) {
                $cw = $data['current_weather'];
                $temperature = $cw['temperature'] ?? null;
                $windSpeed = $cw['windspeed'] ?? null;
                $windDirection = $cw['winddirection'] ?? null;
                $weatherCode = $cw['weathercode'] ?? null;

                    // try to get humidity and precipitation from hourly at the nearest time
                    if (isset($data['hourly'], $cw['time'])) {
                        $hourly = $data['hourly'];
                        $tz = $data['timezone'] ?? 'UTC';
                        $cwTime = Carbon::parse($cw['time'], $tz);

                        $bestIdx = null;
                        $bestDiff = PHP_INT_MAX;

                        foreach (($hourly['time'] ?? []) as $i => $tstr) {
                            $ht = Carbon::parse($tstr, $tz);
                            $diff = abs($ht->getTimestamp() - $cwTime->getTimestamp());
                            if ($diff < $bestDiff) {
                                $bestDiff = $diff;
                                $bestIdx = $i;
                            }
                        }

                        if ($bestIdx !== null) {
                            if (isset($hourly['relative_humidity_2m'][$bestIdx])) {
                                $humidity = $hourly['relative_humidity_2m'][$bestIdx];
                            }
                            if (isset($hourly['precipitation'][$bestIdx])) {
                                $precipitation = $hourly['precipitation'][$bestIdx];
                            }
                        }
                    }
            } else {
                // Fallback: try to read hourly latest values
                if (isset($result['data']['hourly'])) {
                    $hourly = $result['data']['hourly'];
                    $lastIndex = max(0, count($hourly['time']) - 1);
                    $temperature = $hourly['temperature_2m'][$lastIndex] ?? null;
                    $humidity = $hourly['relative_humidity_2m'][$lastIndex] ?? null;
                    $precipitation = $hourly['precipitation'][$lastIndex] ?? null;
                    $windSpeed = $hourly['wind_speed_10m'][$lastIndex] ?? $windSpeed;
                }
            }

            $simplified = [
                'temperature' => $temperature,
                'humidity' => $humidity,
                'windSpeed' => $windSpeed,
                'windDirection' => $windDirection,
                'weatherCode' => $weatherCode,
                'precipitation' => $precipitation,
            ];

            return response()->json($simplified, $result['status'] ?? 200);
        }

        return response()->json($result, $result['status'] ?? 200);
    }
}
