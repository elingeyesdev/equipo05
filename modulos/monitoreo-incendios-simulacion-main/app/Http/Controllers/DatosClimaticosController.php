<?php

namespace Modules\Incendios\Http\Controllers;

use Modules\Incendios\Services\OpenMeteoService;
use Modules\Incendios\Support\ClimaUbicaciones;
use Modules\Incendios\Support\SensacionTermica;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DatosClimaticosController extends Controller
{
    /**
     * Mostrar página de datos climáticos históricos (última semana)
     */
    public function index(Request $request, OpenMeteoService $weather)
    {
        $ubicaciones = ClimaUbicaciones::all();

        if ($request->filled('ubicacion')) {
            $ubicacionKey = ClimaUbicaciones::normalizeKey($request->query('ubicacion'));
            session(['incendios_clima_ubicacion' => $ubicacionKey]);
        } else {
            $ubicacionKey = ClimaUbicaciones::normalizeKey(session('incendios_clima_ubicacion'));
        }

        $ubicacionData = $ubicaciones[$ubicacionKey];
        $latitude = $ubicacionData['lat'];
        $longitude = $ubicacionData['lng'];
        
        // Obtener datos de los últimos 7 días
        $fechaFin = Carbon::now();
        $fechaInicio = Carbon::now()->subDays(7);
        
        // Obtener datos históricos
        $weatherData = $weather->getHistoricalWeather(
            $latitude, 
            $longitude, 
            $fechaInicio->format('Y-m-d'),
            $fechaFin->format('Y-m-d')
        );
        
        // Procesar datos para las gráficas
        $datosGraficas = $this->procesarDatosParaGraficas($weatherData);
        
        return view('datos-climaticos.index', [
            'datosGraficas' => $datosGraficas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'ubicacion' => $ubicacionData['nombre'] . ', Bolivia',
            'ubicacionKey' => $ubicacionKey,
            'ubicaciones' => $ubicaciones,
            'coordenadas' => [
                'lat' => $latitude,
                'lng' => $longitude,
            ],
        ]);
    }
    
    /**
     * Procesar datos para las gráficas
     */
    private function procesarDatosParaGraficas($weatherData)
    {
        if (! isset($weatherData['data']['hourly'])) {
            return $this->estructuraGraficasVacia();
        }

        $hourly = $weatherData['data']['hourly'];

        $temperatura = $hourly['temperature_2m'] ?? [];
        $humedad = $hourly['relative_humidity_2m'] ?? [];
        $viento = $hourly['wind_speed_10m'] ?? [];
        $rafagas = $hourly['wind_gusts_10m'] ?? [];
        $precip = $hourly['precipitation'] ?? [];
        $aparenteApi = $hourly['apparent_temperature'] ?? [];

        if (empty(array_filter($rafagas, fn ($v) => $v !== null))) {
            $rafagas = array_map(
                fn ($v) => $v !== null ? round((float) $v * 1.35, 1) : null,
                $viento
            );
        }

        $sensacion = SensacionTermica::serie(
            $temperatura,
            $humedad,
            $viento,
            $rafagas,
            $precip,
            $aparenteApi
        );

        $idxActual = max(0, count($temperatura) - 1);

        return [
            'labels' => $hourly['time'] ?? [],
            'temperatura' => $temperatura,
            'humedad' => $humedad,
            'precipitacion' => $precip,
            'viento' => $viento,
            'rafagas' => $rafagas,
            'uv_index' => $hourly['uv_index'] ?? [],
            'sensacion_termica' => $sensacion,
            'sensacion_actual' => $sensacion[$idxActual] ?? null,
            'temperatura_actual' => $temperatura[$idxActual] ?? null,
            'viento_actual' => $viento[$idxActual] ?? null,
            'rafagas_actual' => $rafagas[$idxActual] ?? null,
            'humedad_actual' => $humedad[$idxActual] ?? null,
            'precip_actual' => $precip[$idxActual] ?? null,
        ];
    }

    private function estructuraGraficasVacia(): array
    {
        return [
            'labels' => [],
            'temperatura' => [],
            'humedad' => [],
            'precipitacion' => [],
            'viento' => [],
            'rafagas' => [],
            'uv_index' => [],
            'sensacion_termica' => [],
            'sensacion_termica_serie' => [],
            'sensacion_actual' => null,
            'temperatura_actual' => null,
            'viento_actual' => null,
            'rafagas_actual' => null,
            'humedad_actual' => null,
            'precip_actual' => null,
        ];
    }
}
