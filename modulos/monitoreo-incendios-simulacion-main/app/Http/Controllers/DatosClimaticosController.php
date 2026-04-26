<?php

namespace App\Http\Controllers;

use App\Services\OpenMeteoService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DatosClimaticosController extends Controller
{
    /**
     * Ubicaciones de la Chiquitanía, Bolivia
     */
    private array $ubicaciones = [
        'san-jose' => [
            'nombre' => 'San José de Chiquitos',
            'lat' => -17.8857,
            'lng' => -60.7556,
        ],
        'robore' => [
            'nombre' => 'Roboré',
            'lat' => -18.3333,
            'lng' => -59.7667,
        ],
        'san-ignacio' => [
            'nombre' => 'San Ignacio de Velasco',
            'lat' => -16.3667,
            'lng' => -60.9500,
        ],
        'concepcion' => [
            'nombre' => 'Concepción',
            'lat' => -16.1500,
            'lng' => -62.0333,
        ],
        'san-matias' => [
            'nombre' => 'San Matías',
            'lat' => -16.3500,
            'lng' => -58.4000,
        ],
        'san-rafael' => [
            'nombre' => 'San Rafael de Velasco',
            'lat' => -16.7833,
            'lng' => -60.6833,
        ],
        'san-miguel' => [
            'nombre' => 'San Miguel de Velasco',
            'lat' => -16.6833,
            'lng' => -60.9667,
        ],
        'santa-ana' => [
            'nombre' => 'Santa Ana de Velasco',
            'lat' => -16.6667,
            'lng' => -60.7167,
        ],
        'santiago' => [
            'nombre' => 'Santiago de Chiquitos',
            'lat' => -18.3333,
            'lng' => -59.6000,
        ],
        'chochis' => [
            'nombre' => 'Chochís',
            'lat' => -18.2167,
            'lng' => -59.7000,
        ],
        'aguas-calientes' => [
            'nombre' => 'Aguas Calientes',
            'lat' => -18.0500,
            'lng' => -59.6167,
        ],
        'quimome' => [
            'nombre' => 'Quimome',
            'lat' => -17.5000,
            'lng' => -60.4833,
        ],
    ];

    /**
     * Mostrar página de datos climáticos históricos (última semana)
     */
    public function index(Request $request, OpenMeteoService $weather)
    {
        // Obtener ubicación seleccionada (default: San José)
        $ubicacionKey = $request->query('ubicacion', 'san-jose');
        
        // Validar que la ubicación exista
        if (!isset($this->ubicaciones[$ubicacionKey])) {
            $ubicacionKey = 'san-jose';
        }
        
        $ubicacionData = $this->ubicaciones[$ubicacionKey];
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
            'ubicaciones' => $this->ubicaciones,
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
        if (!isset($weatherData['data']['hourly'])) {
            return [
                'labels' => [],
                'temperatura' => [],
                'humedad' => [],
                'precipitacion' => [],
                'viento' => [],
            ];
        }
        
        $hourly = $weatherData['data']['hourly'];
        
        return [
            'labels' => $hourly['time'] ?? [],
            'temperatura' => $hourly['temperature_2m'] ?? [],
            'humedad' => $hourly['relative_humidity_2m'] ?? [],
            'precipitacion' => $hourly['precipitation'] ?? [],
            'viento' => $hourly['wind_speed_10m'] ?? [],
        ];
    }
}
