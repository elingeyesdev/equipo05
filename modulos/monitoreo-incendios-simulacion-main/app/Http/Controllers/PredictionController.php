<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Models\FocosIncendio;
use App\Models\Biomasa;
use App\Http\Requests\PredictionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class PredictionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $predictions = Prediction::with('focoIncendio')->latest()->paginate(10);

        return view('prediction.index', compact('predictions'))
            ->with('i', ($request->input('page', 1) - 1) * $predictions->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $prediction = new Prediction();
        $focosIncendios = FocosIncendio::whereNotNull('coordenadas')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('prediction.create', compact('prediction', 'focosIncendios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'fire_lat' => 'required|numeric',
            'fire_lng' => 'required|numeric',
            'fire_intensity' => 'required|numeric|min:1|max:10',
            'fire_frp' => 'nullable|numeric',
            'temperature' => 'required|numeric|min:0|max:60',
            'humidity' => 'required|numeric|min:0|max:100',
            'wind_speed' => 'required|numeric|min:0|max:200',
            'wind_direction' => 'required|integer|min:0|max:360',
            'prediction_hours' => 'required|integer|min:1|max:72',
            'terrain_type' => 'required|string',
        ]);

        // Crear un objeto foco temporal con los datos del formulario
        $focoData = (object) [
            'id' => null,
            'coordenadas' => [$request->fire_lat, $request->fire_lng],
            'intensidad' => $request->fire_intensity,
            'frp' => $request->fire_frp,
            'ubicacion' => "FIRMS ({$request->fire_lat}, {$request->fire_lng})",
        ];

        // Generar predicción usando algoritmo
        $predictionData = $this->generatePredictionFromCoords(
            $request->fire_lat,
            $request->fire_lng,
            $request->fire_intensity,
            $request->temperature,
            $request->humidity,
            $request->wind_speed,
            $request->wind_direction,
            $request->prediction_hours,
            $request->terrain_type
        );

        $prediction = Prediction::create([
            'foco_incendio_id' => null, // No hay foco de incendio asociado
            'predicted_at' => now(),
            'path' => $predictionData['path'],
            'meta' => $predictionData['meta'],
            'user_id' => auth()->id(),
            'ci_usuario' => auth()->user()->cedula_identidad,
        ]);

        return redirect()->route('predictions.show', $prediction->id)
            ->with('success', 'Predicción generada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $prediction = Prediction::with('focoIncendio')->findOrFail($id);
        
        // Si hay un foco de incendio asociado, usarlo
        // Si no, crear un objeto temporal con los datos de la predicción
        if ($prediction->focoIncendio) {
            $foco = $prediction->focoIncendio;
        } else {
            // Crear objeto foco desde los datos de la predicción
            $meta = $prediction->meta ?? [];
            $inputParams = $meta['input_parameters'] ?? [];
            $trajectory = $prediction->path ?? [];
            $firstPoint = $trajectory[0] ?? null;
            
            $foco = (object) [
                'id' => null,
                'ubicacion' => 'Foco FIRMS',
                'coordenadas' => $firstPoint ? [$firstPoint['lat'], $firstPoint['lng']] : null,
                'intensidad' => $inputParams['initial_intensity'] ?? 3,
                'fecha' => $prediction->predicted_at,
                'fecha_deteccion' => $prediction->predicted_at,
            ];
        }
        
        // Cargar biomasas para mostrar en el mapa
        $biomasas = Biomasa::with('tipoBiomasa')
            ->whereNotNull('coordenadas')
            ->get()
            ->map(function ($biomasa) {
                if (is_string($biomasa->coordenadas)) {
                    $biomasa->coordenadas = json_decode($biomasa->coordenadas, true);
                }
                return $biomasa;
            });

        return view('prediction.show', compact('prediction', 'foco', 'biomasas'));
    }

    /**
     * Display printable PDF report for a prediction.
     */
    public function showPdf($id): View
    {
        $prediction = Prediction::with('focoIncendio')->findOrFail($id);
        return view('reports.prediction', compact('prediction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $prediction = Prediction::findOrFail($id);
        $focosIncendios = FocosIncendio::whereNotNull('coordenadas')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('prediction.edit', compact('prediction', 'focosIncendios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PredictionRequest $request, Prediction $prediction): RedirectResponse
    {
        $prediction->update($request->validated());

        return redirect()->route('predictions.index')
            ->with('success', 'Predicción actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        Prediction::findOrFail($id)->delete();

        return redirect()->route('predictions.index')
            ->with('success', 'Predicción eliminada exitosamente');
    }

    /**
     * Generar predicción usando algoritmo de propagación de incendios
     */
    private function generatePrediction(
        FocosIncendio $foco,
        float $temperature,
        float $humidity,
        float $windSpeed,
        int $windDirection,
        int $hours,
        string $terrainType
    ): array {
        // Parsear coordenadas para manejar tanto array [lat, lng] como objeto {lat, lng}
        $coords = is_string($foco->coordenadas) ? json_decode($foco->coordenadas, true) : $foco->coordenadas;
        
        // Extraer lat y lng de forma robusta
        if (is_array($coords)) {
            $startLat = (float) ($coords[0] ?? $coords['lat'] ?? 0);
            $startLng = (float) ($coords[1] ?? $coords['lng'] ?? 0);
        } else {
            throw new \Exception('El foco de incendio no tiene coordenadas válidas');
        }
        
        $intensity = (float) ($foco->intensidad ?? 5);
        
        return $this->generatePredictionFromCoords(
            $startLat,
            $startLng,
            $intensity,
            $temperature,
            $humidity,
            $windSpeed,
            $windDirection,
            $hours,
            $terrainType
        );
    }

    /**
     * Generar predicción desde coordenadas directas (para FIRMS)
     */
    private function generatePredictionFromCoords(
        float $startLat,
        float $startLng,
        float $intensity,
        float $temperature,
        float $humidity,
        float $windSpeed,
        int $windDirection,
        int $hours,
        string $terrainType
    ): array {
        if ($startLat == 0 || $startLng == 0) {
            throw new \Exception('Coordenadas inválidas: lat=' . $startLat . ', lng=' . $startLng);
        }

        // Factores de propagación según tipo de terreno
        $terrainFactors = [
            'bosque_denso' => 1.5,
            'bosque_normal' => 1.2,
            'pastizal' => 1.0,
            'matorral' => 0.8,
            'rocoso' => 0.3,
        ];

        $terrainFactor = $terrainFactors[$terrainType] ?? 1.0;

        // Calcular velocidad de propagación (km/h)
        $baseSpeed = 0.5; // velocidad base
        $tempFactor = ($temperature / 30) * 0.5; // mayor temperatura = más rápido
        $humFactor = (1 - ($humidity / 100)) * 0.3; // menor humedad = más rápido
        $windFactor = ($windSpeed / 20) * 0.7; // más viento = más rápido
        
        $spreadSpeed = $baseSpeed + $tempFactor + $humFactor + $windFactor;
        $spreadSpeed *= $terrainFactor;

        // Calcular índice de peligro
        $fireRisk = $this->calculateFireRisk($temperature, $humidity, $windSpeed);

        // Generar trayectoria hora por hora
        $path = [];
        $currentLat = $startLat;
        $currentLng = $startLng;
        $currentIntensity = $intensity;

        // Convertir dirección del viento a radianes
        $windRad = deg2rad($windDirection);

        // Área afectada acumulada
        $totalArea = 0;
        $perimeterGrowth = [];
        
        // Factores de crecimiento de intensidad
        $biomassAvailability = 1.0; // Disponibilidad inicial de combustible (100%)
        
        // Contador para detectar extinción del fuego
        $lowIntensityCount = 0; // Contador de horas consecutivas en intensidad 1
        $fireExtinguished = false; // Flag para marcar si el fuego se extinguió
        
        // Tracking de biomasas atravesadas
        $biomasasEncountered = [];

        for ($hour = 0; $hour <= $hours; $hour++) {
            // Detectar si el fuego está en una zona de biomasa
            $biomasaData = $this->getBiomasaModifier($currentLat, $currentLng);
            $biomasaModifier = $biomasaData['modifier'];
            
            // Registrar biomasa encontrada
            if ($biomasaData['inside_biomasa'] && !in_array($biomasaData['biomasa_id'], array_column($biomasasEncountered, 'id'))) {
                $biomasasEncountered[] = [
                    'id' => $biomasaData['biomasa_id'],
                    'tipo' => $biomasaData['tipo_biomasa'],
                    'modifier' => $biomasaModifier,
                    'entered_at_hour' => $hour,
                ];
            }
            
            // Si el fuego ya se extinguió, terminar el loop (no generar más puntos)
            if ($fireExtinguished) {
                break;
            }
            
            // MODELO REALISTA DE INTENSIDAD
            // La intensidad del fuego evoluciona según condiciones climáticas y combustible disponible
            
            // Calcular factores de intensidad
            // Factor de temperatura: temperaturas altas favorecen el fuego
            $tempFactor = 1.0 + max(0, ($temperature - 25)) / 50; // Escala 1.0 a 1.7
            
            // Factor de humedad: baja humedad = fuego más intenso
            $humidityFactor = 1.0 + (1 - $humidity / 100) * 0.5; // Escala 1.0 a 1.5
            
            // Factor de viento: viento alimenta el fuego
            $windIntensityFactor = 1.0 + min($windSpeed, 40) / 60; // Escala 1.0 a 1.67
            
            // Factor combinado de condiciones
            $conditionsFactor = $tempFactor * $humidityFactor * $windIntensityFactor * $terrainFactor * $biomasaModifier;
            
            // Fase del fuego según hora
            $progressRatio = $hour / max(1, $hours);
            
            if ($progressRatio <= 0.2) {
                // Fase de IGNICIÓN (primeras 20%): El fuego se establece y crece rápidamente
                $growthRate = 1.0 + ($progressRatio / 0.2) * 0.5; // Crece de 1.0 a 1.5
                $currentIntensity = $intensity * $growthRate * $conditionsFactor;
                
            } elseif ($progressRatio <= 0.6) {
                // Fase de MÁXIMA PROPAGACIÓN (20%-60%): El fuego alcanza su máxima intensidad
                // La intensidad se mantiene alta con fluctuaciones
                $peakIntensity = $intensity * 1.5 * $conditionsFactor;
                $fluctuation = 1.0 + (rand(-10, 10) / 100); // ±10% variación
                $currentIntensity = $peakIntensity * $fluctuation;
                
            } elseif ($progressRatio <= 0.85) {
                // Fase de DECLIVE GRADUAL (60%-85%): El combustible empieza a agotarse
                $declineProgress = ($progressRatio - 0.6) / 0.25;
                $declineFactor = 1.0 - ($declineProgress * 0.4); // Baja de 1.0 a 0.6
                $peakIntensity = $intensity * 1.5 * $conditionsFactor;
                $currentIntensity = $peakIntensity * $declineFactor;
                
            } else {
                // Fase de EXTINCIÓN (85%-100%): El fuego se apaga gradualmente
                $extinctionProgress = ($progressRatio - 0.85) / 0.15;
                $extinctionFactor = 0.6 - ($extinctionProgress * 0.5); // Baja de 0.6 a 0.1
                $peakIntensity = $intensity * 1.5 * $conditionsFactor;
                $currentIntensity = max(1, $peakIntensity * $extinctionFactor);
            }
            
            // Limitar intensidad entre 1 y 10
            $currentIntensity = min(10, max(1, $currentIntensity));
            
            // Variación aleatoria pequeña para simular fluctuaciones naturales
            $randomVariation = 1 + (rand(-5, 5) / 100);
            $currentIntensity = min(10, max(1, $currentIntensity * $randomVariation));
            
            // Solo verificar extinción en la fase final (después del 85% del tiempo)
            if ($progressRatio > 0.85 && round($currentIntensity, 1) <= 1.0) {
                $lowIntensityCount++;
                
                // Solo extinguir si ha estado en intensidad mínima por varias horas
                if ($lowIntensityCount > 3) {
                    $fireExtinguished = true;
                    
                    // Agregar último punto de extinción y terminar
                    $path[] = [
                        'hour' => $hour,
                        'lat' => round($currentLat, 6),
                        'lng' => round($currentLng, 6),
                        'intensity' => 1.0,
                        'spread_radius_km' => round($radius ?? 0, 3),
                        'affected_area_km2' => round($area ?? 0, 3),
                        'perimeter_km' => round($perimeter ?? 0, 2),
                        'extinguished' => true,
                    ];
                    break; // Salir del loop, no generar más puntos
                }
            } else {
                // Si la intensidad sube o no estamos en fase final, resetear el contador
                $lowIntensityCount = 0;
            }
            
            // Calcular desplazamiento basado en viento y variación aleatoria
            $mainDirection = $windRad;
            $lateralSpread = 0.3; // propagación lateral
            
            // Distancia recorrida en esta hora (en grados)
            // Aumenta con intensidad y velocidad del viento
            $speedMultiplier = 1 + ($currentIntensity / 10) + ($windSpeed / 30);
            $distance = ($spreadSpeed / 111) * $speedMultiplier * (0.8 + ($hour * 0.05));
            
            // Componente principal (dirección del viento)
            $latOffset = $distance * cos($mainDirection) * (0.8 + rand(0, 40) / 100);
            $lngOffset = $distance * sin($mainDirection) * (0.8 + rand(0, 40) / 100);
            
            // Añadir propagación lateral (más caótica con alta intensidad)
            $lateralOffset = $distance * $lateralSpread * (rand(-50, 50) / 100) * ($currentIntensity / 5);
            $latOffset += $lateralOffset * sin($mainDirection);
            $lngOffset += $lateralOffset * cos($mainDirection);

            $currentLat += $latOffset;
            $currentLng += $lngOffset;

            // Calcular área afectada (crece exponencialmente con intensidad)
            $radius = $spreadSpeed * sqrt($hour + 1) * ($currentIntensity / 5); // km
            $area = pi() * pow($radius, 2); // km²
            $totalArea = $area;

            // Calcular perímetro
            $perimeter = 2 * pi() * $radius;
            $perimeterGrowth[] = round($perimeter, 2);

            $path[] = [
                'hour' => $hour,
                'lat' => round($currentLat, 6),
                'lng' => round($currentLng, 6),
                'intensity' => round($currentIntensity, 2),
                'spread_radius_km' => round($radius, 3),
                'affected_area_km2' => round($area, 3),
                'perimeter_km' => round($perimeter, 2),
                'extinguished' => false,
                'biomasa' => $biomasaData['inside_biomasa'] ? [
                    'tipo' => $biomasaData['tipo_biomasa'],
                    'modifier' => $biomasaData['modifier'],
                    'densidad' => $biomasaData['densidad'],
                ] : null,
            ];
        }

        // Punto final predicho
        $finalPoint = end($path);
        
        // Determinar duración real (cantidad de puntos generados)
        $actualDuration = count($path) - 1; // -1 porque hora 0 es el inicio

        // Calcular distancia total recorrida
        $totalDistance = $this->calculateDistance(
            $startLat, $startLng,
            $finalPoint['lat'], $finalPoint['lng']
        );

        // Probabilidad de contención según factores
        $containmentProbability = $this->calculateContainmentProbability(
            $fireRisk, $terrainType, $hours, $windSpeed
        );

        // Recursos necesarios estimados
        $estimatedResources = $this->estimateResources($totalArea, $fireRisk, $terrainType);

        // Metadatos completos
        $meta = [
            // Parámetros de entrada
            'input_parameters' => [
                'temperature' => $temperature,
                'humidity' => $humidity,
                'wind_speed' => $windSpeed,
                'wind_direction' => $windDirection,
                'prediction_hours' => $hours,
                'terrain_type' => $terrainType,
                'initial_intensity' => $intensity,
            ],
            
            // Trayectoria completa para el mapa interactivo
            'trajectory' => $path,
            
            // Información de biomasas atravesadas
            'biomasas_encountered' => $biomasasEncountered,
            'total_biomasas_crossed' => count($biomasasEncountered),
            
            // Información de extinción
            'fire_extinguished' => $fireExtinguished,
            'actual_duration_hours' => $actualDuration,
            'extinguished_early' => $fireExtinguished && $actualDuration < $hours,
            
            // Índices calculados
            'fire_risk_index' => $fireRisk,
            'spread_speed_kmh' => round($spreadSpeed, 2),
            'terrain_factor' => $terrainFactor,
            
            // Resultados finales
            'final_position' => [
                'lat' => $finalPoint['lat'],
                'lng' => $finalPoint['lng'],
                'intensity' => $finalPoint['intensity'],
            ],
            
            // Estadísticas
            'total_distance_km' => round($totalDistance, 2),
            'total_area_affected_km2' => round($totalArea, 2),
            'final_perimeter_km' => round($finalPoint['perimeter_km'], 2),
            'max_spread_radius_km' => round($finalPoint['spread_radius_km'], 2),
            
            // Probabilidades y evaluación
            'containment_probability' => round($containmentProbability * 100, 1),
            'danger_level' => $this->getDangerLevel($fireRisk),
            'propagation_rate' => $this->getPropagationRate($spreadSpeed),
            
            // Recursos estimados
            'estimated_resources' => $estimatedResources,
            
            // Recomendaciones
            'recommendations' => $this->generateRecommendations(
                $fireRisk, $terrainType, $windSpeed, $totalArea
            ),
            
            // Cronología de crecimiento
            'perimeter_growth_timeline' => $perimeterGrowth,
            
            // Metadatos del algoritmo
            'algorithm_version' => '1.0',
            'prediction_confidence' => $this->calculateConfidenceFromParams($intensity, $humidity, $windSpeed),
            'generated_at' => now()->toIso8601String(),
        ];

        return [
            'path' => $path,
            'meta' => $meta,
        ];
    }

    /**
     * Calcular índice de riesgo de incendio (0-100)
     */
    private function calculateFireRisk(float $temp, float $humidity, float $windSpeed): int
    {
        $tempFactor = min($temp / 40, 1) * 40;
        $humFactor = (1 - ($humidity / 100)) * 30;
        $windFactor = min($windSpeed / 30, 1) * 30;
        
        return min(round($tempFactor + $humFactor + $windFactor), 100);
    }

    /**
     * Calcular distancia entre dos puntos en km
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    /**
     * Calcular probabilidad de contención
     */
    private function calculateContainmentProbability(
        int $fireRisk,
        string $terrainType,
        int $hours,
        float $windSpeed
    ): float {
        $baseProbability = 0.7;
        
        // Reducir por riesgo
        $baseProbability -= ($fireRisk / 100) * 0.3;
        
        // Reducir por tiempo
        $baseProbability -= ($hours / 72) * 0.2;
        
        // Reducir por viento
        $baseProbability -= min($windSpeed / 50, 1) * 0.15;
        
        // Ajustar por terreno
        $terrainAdjustment = [
            'bosque_denso' => -0.2,
            'bosque_normal' => -0.1,
            'pastizal' => 0,
            'matorral' => 0.1,
            'rocoso' => 0.2,
        ];
        
        $baseProbability += $terrainAdjustment[$terrainType] ?? 0;
        
        return max(0.1, min(0.95, $baseProbability));
    }

    /**
     * Estimar recursos necesarios
     */
    private function estimateResources(float $area, int $fireRisk, string $terrainType): array
    {
        // Bomberos necesarios (1 por cada 0.5 km²)
        $firefighters = max(5, ceil($area / 0.5));
        
        // Vehículos (1 por cada 3 bomberos)
        $vehicles = max(2, ceil($firefighters / 3));
        
        // Helicópteros para áreas grandes
        $helicopters = $area > 5 ? ceil($area / 10) : 0;
        
        // Ajustar por riesgo
        if ($fireRisk > 70) {
            $firefighters = ceil($firefighters * 1.5);
            $vehicles = ceil($vehicles * 1.3);
            $helicopters += 1;
        }
        
        return [
            'firefighters' => $firefighters,
            'fire_trucks' => $vehicles,
            'helicopters' => $helicopters,
            'water_needed_liters' => round($area * 10000), // 10,000L por km²
            'estimated_cost_usd' => round($firefighters * 200 + $vehicles * 500 + $helicopters * 5000),
        ];
    }

    /**
     * Generar recomendaciones
     */
    private function generateRecommendations(
        int $fireRisk,
        string $terrainType,
        float $windSpeed,
        float $area
    ): array {
        $recommendations = [];
        
        if ($fireRisk > 70) {
            $recommendations[] = "⚠️ Riesgo CRÍTICO: Evacuación inmediata de zonas cercanas";
            $recommendations[] = "🚁 Solicitar apoyo aéreo urgente";
        } elseif ($fireRisk > 40) {
            $recommendations[] = "⚠️ Riesgo ALTO: Monitoreo constante requerido";
        }
        
        if ($windSpeed > 30) {
            $recommendations[] = "💨 Vientos fuertes: Priorizar cortafuegos en dirección del viento";
        }
        
        if ($terrainType === 'bosque_denso') {
            $recommendations[] = "🌲 Bosque denso: Crear líneas de contención amplias";
        }
        
        if ($area > 10) {
            $recommendations[] = "📏 Área extensa: Dividir zona en sectores de control";
        }
        
        $recommendations[] = "💧 Mantener provisiones de agua constantes";
        $recommendations[] = "📡 Establecer comunicación permanente entre equipos";
        
        return $recommendations;
    }

    /**
     * Obtener nivel de peligro
     */
    private function getDangerLevel(int $fireRisk): string
    {
        if ($fireRisk > 80) return 'EXTREMO';
        if ($fireRisk > 60) return 'MUY ALTO';
        if ($fireRisk > 40) return 'ALTO';
        if ($fireRisk > 20) return 'MODERADO';
        return 'BAJO';
    }

    /**
     * Obtener tasa de propagación
     */
    private function getPropagationRate(float $speed): string
    {
        if ($speed > 2) return 'MUY RÁPIDA';
        if ($speed > 1) return 'RÁPIDA';
        if ($speed > 0.5) return 'MODERADA';
        return 'LENTA';
    }

    /**
     * Calcular confianza de la predicción
     */
    private function calculateConfidence(
        FocosIncendio $foco,
        float $humidity,
        float $windSpeed
    ): float {
        $confidence = 0.85; // base
        
        // Reducir si falta información
        if (!$foco->coordenadas) $confidence -= 0.3;
        if (!$foco->intensidad) $confidence -= 0.1;
        
        // Reducir con condiciones extremas
        if ($humidity < 20 || $humidity > 90) $confidence -= 0.1;
        if ($windSpeed > 50) $confidence -= 0.15;
        
        return max(0.3, min(0.95, $confidence));
    }

    /**
     * Calcular confianza desde parámetros directos (para FIRMS)
     */
    private function calculateConfidenceFromParams(
        float $intensity,
        float $humidity,
        float $windSpeed
    ): float {
        $confidence = 0.85; // base
        
        // Los datos de FIRMS son confiables
        if ($intensity > 0) $confidence += 0.05;
        
        // Reducir con condiciones extremas
        if ($humidity < 20 || $humidity > 90) $confidence -= 0.1;
        if ($windSpeed > 50) $confidence -= 0.15;
        
        return max(0.3, min(0.95, $confidence));
    }

    /**
     * Detectar biomasa en la ubicación actual del fuego
     * Retorna el modificador de intensidad de la biomasa (1.0 si no hay biomasa)
     */
    private function getBiomasaModifier(float $lat, float $lng): array
    {
        // Cargar todas las biomasas con sus tipos
        $biomasas = Biomasa::with('tipoBiomasa')
            ->whereNotNull('coordenadas')
            ->get();

        foreach ($biomasas as $biomasa) {
            // Parsear coordenadas si es string
            $coords = is_string($biomasa->coordenadas) 
                ? json_decode($biomasa->coordenadas, true) 
                : $biomasa->coordenadas;

            if (!$coords || !is_array($coords) || count($coords) < 3) {
                continue;
            }

            // Verificar si el punto está dentro del polígono
            if ($this->isPointInPolygon($lat, $lng, $coords)) {
                $modifier = floatval($biomasa->tipoBiomasa->modificador_intensidad ?? 1.0);
                return [
                    'inside_biomasa' => true,
                    'biomasa_id' => $biomasa->id,
                    'tipo_biomasa' => $biomasa->tipoBiomasa->tipo_biomasa ?? 'Desconocido',
                    'modifier' => $modifier,
                    'densidad' => $biomasa->densidad,
                ];
            }
        }

        // No está en ninguna biomasa
        return [
            'inside_biomasa' => false,
            'biomasa_id' => null,
            'tipo_biomasa' => null,
            'modifier' => 1.0,
            'densidad' => null,
        ];
    }

    /**
     * Algoritmo Ray Casting para detectar si un punto está dentro de un polígono
     * @param float $lat Latitud del punto
     * @param float $lng Longitud del punto
     * @param array $polygon Array de coordenadas [[lat, lng], [lat, lng], ...]
     * @return bool True si el punto está dentro del polígono
     */
    private function isPointInPolygon(float $lat, float $lng, array $polygon): bool
    {
        $numVertices = count($polygon);
        $inside = false;

        for ($i = 0, $j = $numVertices - 1; $i < $numVertices; $j = $i++) {
            $xi = floatval($polygon[$i][0]); // lat
            $yi = floatval($polygon[$i][1]); // lng
            $xj = floatval($polygon[$j][0]); // lat
            $yj = floatval($polygon[$j][1]); // lng

            $intersect = (($yi > $lng) != ($yj > $lng))
                && ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}
