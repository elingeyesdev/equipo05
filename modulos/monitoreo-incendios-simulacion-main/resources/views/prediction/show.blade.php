@extends('layouts.app')

@section('subtitle', 'Resultado de Predicción')
@section('content_header_title', 'Predicciones')
@section('content_header_subtitle', 'Resultado de Predicción')

@section('content_body')
@php
    $meta = $prediction->meta ?? [];
    $inputParams = $meta['input_parameters'] ?? [];
    $finalPos = $meta['final_position'] ?? [];
    $resources = $meta['estimated_resources'] ?? [];
    $recommendations = $meta['recommendations'] ?? [];
    $trajectory = $meta['trajectory'] ?? $prediction->path ?? [];
@endphp

<div class="container-fluid">
    <!-- Resumen General -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Información General</h3>
                    <div class="card-tools">
                        <a href="{{ route('predictions.index') }}" class="btn btn-tool">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Foco de Incendio:</dt>
                                <dd class="col-sm-7">
                                    <strong>{{ $foco->ubicacion ?? 'Foco FIRMS' }}</strong><br>
                                    <small class="text-muted">
                                        @if(isset($foco->fecha) && $foco->fecha)
                                            Fecha: {{ is_object($foco->fecha) ? $foco->fecha->format('d/m/Y H:i') : $foco->fecha }}<br>
                                        @endif
                                        Intensidad Inicial: {{ $inputParams['initial_intensity'] ?? 'N/A' }}
                                    </small>
                                </dd>
                                
                                <dt class="col-sm-5">Predicción Generada:</dt>
                                <dd class="col-sm-7">{{ $prediction->predicted_at?->format('d/m/Y H:i:s') }}</dd>
                                
                                <dt class="col-sm-5">Horizonte Temporal:</dt>
                                <dd class="col-sm-7"><strong>{{ $inputParams['prediction_hours'] ?? 'N/A' }} horas</strong></dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Tipo de Terreno:</dt>
                                <dd class="col-sm-7">{{ ucwords(str_replace('_', ' ', $inputParams['terrain_type'] ?? 'N/A')) }}</dd>
                                
                                <dt class="col-sm-5">Confianza:</dt>
                                <dd class="col-sm-7">
                                    <div class="progress">
                                        <div class="progress-bar bg-{{ ($meta['prediction_confidence'] ?? 0) > 0.7 ? 'success' : (($meta['prediction_confidence'] ?? 0) > 0.5 ? 'warning' : 'danger') }}" 
                                             style="width: {{ ($meta['prediction_confidence'] ?? 0) * 100 }}%">
                                            {{ round(($meta['prediction_confidence'] ?? 0) * 100, 1) }}%
                                        </div>
                                    </div>
                                </dd>
                                
                                <dt class="col-sm-5">Versión Algoritmo:</dt>
                                <dd class="col-sm-7">{{ $meta['algorithm_version'] ?? 'N/A' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicadores Principales -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $meta['fire_risk_index'] ?? 0 }}</h3>
                    <p>Índice de Riesgo</p>
                    <small>{{ $meta['danger_level'] ?? 'N/A' }}</small>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $meta['total_distance_km'] ?? 0 }} <small>km</small></h3>
                    <p>Distancia Recorrida</p>
                    <small>{{ $meta['propagation_rate'] ?? 'N/A' }}</small>
                </div>
                <div class="icon">
                    <i class="fas fa-route"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $meta['total_area_affected_km2'] ?? 0 }} <small>km²</small></h3>
                    <p>Área Afectada</p>
                    <small>Perímetro: {{ $meta['final_perimeter_km'] ?? 0 }} km</small>
                </div>
                <div class="icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $meta['containment_probability'] ?? 0 }}<small>%</small></h3>
                    <p>Prob. Contención</p>
                    <small>Con recursos adecuados</small>
                </div>
                <div class="icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Condiciones Ambientales -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cloud-sun"></i> Condiciones Ambientales</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6"><i class="fas fa-thermometer-half text-danger"></i> Temperatura:</dt>
                        <dd class="col-sm-6">{{ $inputParams['temperature'] ?? 'N/A' }}°C</dd>
                        
                        <dt class="col-sm-6"><i class="fas fa-tint text-info"></i> Humedad:</dt>
                        <dd class="col-sm-6">{{ $inputParams['humidity'] ?? 'N/A' }}%</dd>
                        
                        <dt class="col-sm-6"><i class="fas fa-wind text-primary"></i> Velocidad del Viento:</dt>
                        <dd class="col-sm-6">{{ $inputParams['wind_speed'] ?? 'N/A' }} km/h</dd>
                        
                        <dt class="col-sm-6"><i class="fas fa-compass"></i> Dirección del Viento:</dt>
                        <dd class="col-sm-6">
                            {{ $inputParams['wind_direction'] ?? 'N/A' }}° 
                            @php
                                $degrees = $inputParams['wind_direction'] ?? 0;
                                $directions = ['Norte', 'NE', 'Este', 'SE', 'Sur', 'SO', 'Oeste', 'NO'];
                                $index = round($degrees / 45) % 8;
                                $direction = $directions[$index];
                            @endphp
                            ({{ $direction }})
                        </dd>
                        
                        <dt class="col-sm-6">Velocidad de Propagación:</dt>
                        <dd class="col-sm-6"><strong>{{ $meta['spread_speed_kmh'] ?? 'N/A' }} km/h</strong></dd>
                        
                        <dt class="col-sm-6">Factor de Terreno:</dt>
                        <dd class="col-sm-6">{{ $meta['terrain_factor'] ?? 'N/A' }}x</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Posición Final Predicha</h3>
                </div>
                <div class="card-body">
                    @if($meta['fire_extinguished'] ?? false)
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle"></i> 
                        <strong>Fuego Extinguido Naturalmente</strong>
                        <br><small>Combustible agotado después de {{ $meta['actual_duration_hours'] ?? 'N/A' }} horas</small>
                    </div>
                    @endif
                    
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Latitud:</dt>
                        <dd class="col-sm-6"><code>{{ $finalPos['lat'] ?? 'N/A' }}</code></dd>
                        
                        <dt class="col-sm-6">Longitud:</dt>
                        <dd class="col-sm-6"><code>{{ $finalPos['lng'] ?? 'N/A' }}</code></dd>
                        
                        <dt class="col-sm-6">Intensidad Final:</dt>
                        <dd class="col-sm-6">{{ $finalPos['intensity'] ?? 'N/A' }}</dd>
                        
                        <dt class="col-sm-6">Radio Máximo:</dt>
                        <dd class="col-sm-6">{{ $meta['max_spread_radius_km'] ?? 'N/A' }} km</dd>
                    </dl>
                    
                    @if(isset($finalPos['lat']) && isset($finalPos['lng']))
                    <div class="mt-3">
                        <a href="https://www.google.com/maps?q={{ $finalPos['lat'] }},{{ $finalPos['lng'] }}" 
                           target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-external-link-alt"></i> Ver en Google Maps
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(isset($meta['biomasas_encountered']) && count($meta['biomasas_encountered']) > 0)
    <!-- Biomasas Atravesadas -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-leaf"></i> Zonas de Biomasa Atravesadas
                        <span class="badge badge-light ml-2">{{ $meta['total_biomasas_crossed'] ?? 0 }}</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        El fuego atravesó <strong>{{ $meta['total_biomasas_crossed'] ?? 0 }}</strong> zona(s) de biomasa registrada(s), 
                        lo que afectó su velocidad de propagación e intensidad.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-clock"></i> Hora de Entrada</th>
                                    <th><i class="fas fa-tree"></i> Tipo de Biomasa</th>
                                    <th><i class="fas fa-chart-line"></i> Modificador de Intensidad</th>
                                    <th><i class="fas fa-fire"></i> Efecto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($meta['biomasas_encountered'] as $biomasa)
                                <tr>
                                    <td><strong>Hora {{ $biomasa['entered_at_hour'] }}</strong></td>
                                    <td>
                                        <span class="badge badge-success">{{ $biomasa['tipo'] }}</span>
                                    </td>
                                    <td>
                                        <strong style="color: {{ $biomasa['modifier'] > 1.0 ? '#dc3545' : ($biomasa['modifier'] < 1.0 ? '#28a745' : '#6c757d') }}">
                                            {{ number_format($biomasa['modifier'], 2) }}x
                                        </strong>
                                    </td>
                                    <td>
                                        @if($biomasa['modifier'] > 1.5)
                                            <span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Propagación MUY RÁPIDA</span>
                                        @elseif($biomasa['modifier'] > 1.0)
                                            <span class="badge badge-warning"><i class="fas fa-arrow-up"></i> Propagación Acelerada</span>
                                        @elseif($biomasa['modifier'] < 0.8)
                                            <span class="badge badge-success"><i class="fas fa-arrow-down"></i> Propagación Reducida</span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-equals"></i> Propagación Normal</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recursos Necesarios -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-users"></i> Recursos Estimados Necesarios</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-warning"><i class="fas fa-user-friends"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Bomberos</span>
                                    <span class="info-box-number">{{ $resources['firefighters'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-danger"><i class="fas fa-truck"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Camiones</span>
                                    <span class="info-box-number">{{ $resources['fire_trucks'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-info"><i class="fas fa-helicopter"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Helicópteros</span>
                                    <span class="info-box-number">{{ $resources['helicopters'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-primary"><i class="fas fa-tint"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Agua (Litros)</span>
                                    <span class="info-box-number">{{ number_format($resources['water_needed_liters'] ?? 0) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-dollar-sign"></i> <strong>Costo Estimado:</strong> 
                        ${{ number_format($resources['estimated_cost_usd'] ?? 0, 2) }} USD
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recomendaciones -->
    @if(!empty($recommendations))
    <div class="row">
        <div class="col-md-12">
            <div class="card card-{{ $meta['fire_risk_index'] > 70 ? 'danger' : ($meta['fire_risk_index'] > 40 ? 'warning' : 'info') }}">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-lightbulb"></i> Recomendaciones de Acción</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($recommendations as $recommendation)
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i> {{ $recommendation }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Mapa Interactivo de Propagación --}}
    @if(empty($trajectory))
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> No hay datos de trayectoria disponibles para mostrar el mapa interactivo.
                <br><small>Esto puede ocurrir si la predicción fue creada con una versión anterior del sistema.</small>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header bg-gradient-primary">
                    <h3 class="card-title"><i class="fas fa-map-marked-alt"></i> Simulación de Propagación del Incendio</h3>
                </div>
                <div class="card-body">
                    {{-- Controles de Reproducción --}}
                    <div class="map-controls">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <button class="btn btn-primary play-btn" id="playPauseBtn" onclick="togglePlayPause()">
                                    <i class="fas fa-play" id="playIcon"></i>
                                </button>
                            </div>
                            <div class="col-md-7">
                                <label class="mb-2"><i class="far fa-clock"></i> Línea de Tiempo</label>
                                <input type="range" class="form-control-range timeline-slider" id="timelineSlider" 
                                       min="0" max="{{ count($trajectory) - 1 }}" value="0" step="1">
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Hora 0</small>
                                    <small class="text-muted">Hora {{ count($trajectory) - 1 }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="current-hour-display" id="currentHourDisplay">
                                    Hora: 0
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="control-buttons">
                                    <button class="btn btn-outline-secondary" onclick="resetAnimation()">
                                        <i class="fas fa-undo"></i> Reiniciar
                                    </button>
                                    <button class="btn btn-outline-info" onclick="stepBackward()">
                                        <i class="fas fa-step-backward"></i> Anterior
                                    </button>
                                    <button class="btn btn-outline-info" onclick="stepForward()">
                                        <i class="fas fa-step-forward"></i> Siguiente
                                    </button>
                                    <div class="speed-control ml-auto">
                                        <label class="mb-0 mr-2"><i class="fas fa-tachometer-alt"></i> Velocidad:</label>
                                        <select class="form-control form-control-sm" id="speedControl" onchange="changeSpeed()" style="width: auto;">
                                            <option value="2000">0.5x (Lento)</option>
                                            <option value="1000" selected>1x (Normal)</option>
                                            <option value="500">2x (Rápido)</option>
                                            <option value="250">4x (Muy Rápido)</option>
                                        </select>
                                    </div>
                                    <button class="btn btn-success" onclick="showAllTrajectory()">
                                        <i class="fas fa-route"></i> Mostrar Ruta Completa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Mapa --}}
                    <div id="predictionMap"></div>
                    
                    {{-- Leyenda de Intensidad --}}
                    <div class="intensity-legend mt-3">
                        <h6><i class="fas fa-fire-alt"></i> Leyenda de Intensidad</h6>
                        <div class="legend-gradient"></div>
                        <div class="legend-labels">
                            <span>Baja (1-3)</span>
                            <span>Media (4-6)</span>
                            <span>Alta (7-8)</span>
                            <span>Extrema (9-10)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Panel de Estadísticas en Tiempo Real --}}
        <div class="col-lg-3">
            <div class="stats-panel">
                <h5 class="mb-3"><i class="fas fa-chart-line"></i> Datos en Tiempo Real</h5>
                
                <div class="stat-item">
                    <div class="stat-label">Hora Actual</div>
                    <div class="stat-value text-primary" id="statHour">0</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-label">Intensidad</div>
                    <div class="stat-value" id="statIntensity" style="color: #90EE90;">-</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-label">Radio de Alcance</div>
                    <div class="stat-value text-warning" id="statRadius">-</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-label">Área Afectada</div>
                    <div class="stat-value text-danger" id="statArea">-</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-label">Perímetro</div>
                    <div class="stat-value text-info" id="statPerimeter">-</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-label">Coordenadas</div>
                    <div class="stat-value" id="statCoords" style="font-size: 14px;">-</div>
                </div>
                
                <div class="alert alert-info mt-3" id="statAlert" style="display: none;">
                    <small><i class="fas fa-info-circle"></i> <span id="alertText"></span></small>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Trayectoria Hora por Hora -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-route"></i> Trayectoria Predicha (Hora por Hora)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                                <tr class="bg-light">
                                    <th>Hora</th>
                                    <th>Latitud</th>
                                    <th>Longitud</th>
                                    <th>Intensidad</th>
                                    <th>Radio (km)</th>
                                    <th>Área (km²)</th>
                                    <th>Perímetro (km)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prediction->path ?? [] as $point)
                                <tr>
                                    <td><strong>{{ $point['hour'] }}</strong></td>
                                    <td><code>{{ $point['lat'] }}</code></td>
                                    <td><code>{{ $point['lng'] }}</code></td>
                                    <td>
                                        <span class="badge badge-{{ $point['intensity'] > 7 ? 'danger' : ($point['intensity'] > 4 ? 'warning' : 'info') }}">
                                            {{ $point['intensity'] }}
                                        </span>
                                    </td>
                                    <td>{{ $point['spread_radius_km'] }}</td>
                                    <td>{{ $point['affected_area_km2'] }}</td>
                                    <td>{{ $point['perimeter_km'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">
                    <a href="{{ route('predictions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a la Lista
                    </a>
                    <a href="{{ route('predictions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Predicción
                    </a>
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="fas fa-print"></i> Imprimir Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    @media print {
        .btn, .card-tools { display: none; }
    }
    
    #predictionMap {
        height: 600px;
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    .map-controls {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .timeline-slider {
        width: 100%;
        margin: 15px 0;
    }
    
    .control-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .play-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .speed-control {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .current-hour-display {
        font-size: 24px;
        font-weight: bold;
        color: #007bff;
        padding: 10px 20px;
        background: #f8f9fa;
        border-radius: 8px;
        text-align: center;
    }
    
    .intensity-legend {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .legend-gradient {
        height: 20px;
        background: linear-gradient(to right, 
            #90EE90 0%, 
            #FFD700 25%, 
            #FFA500 50%, 
            #FF4500 75%, 
            #8B0000 100%);
        border-radius: 4px;
        margin: 10px 0;
    }
    
    .legend-labels {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #666;
    }
    
    .stats-panel {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: sticky;
        top: 20px;
    }
    
    .stat-item {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .stat-item:last-child {
        border-bottom: none;
    }
    
    .stat-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    
    .stat-value {
        font-size: 20px;
        font-weight: bold;
        color: #212529;
    }
    
    .leaflet-popup-content {
        min-width: 250px;
    }
    
    .popup-title {
        font-size: 16px;
        font-weight: bold;
        color: #007bff;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
    }
    
    .popup-stat {
        display: flex;
        justify-content: space-between;
        margin: 8px 0;
        padding: 5px 0;
    }
    
    .popup-stat-label {
        color: #6c757d;
        font-weight: 500;
    }
    
    .popup-stat-value {
        color: #212529;
        font-weight: bold;
    }
    
    .fire-trail {
        stroke-dasharray: 5, 5;
        animation: dash 20s linear infinite;
    }
    
    @keyframes dash {
        to {
            stroke-dashoffset: -100;
        }
    }
    
    .pulse-animation {
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 0.6;
        }
        50% {
            opacity: 1;
        }
    }
    
    .biomasa-tooltip {
        background-color: rgba(40, 167, 69, 0.9) !important;
        color: white !important;
        border: none !important;
        border-radius: 4px !important;
        padding: 5px 10px !important;
        font-weight: bold !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3) !important;
    }
    
    .biomasa-tooltip::before {
        border-top-color: rgba(40, 167, 69, 0.9) !important;
    }
</style>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Datos de la trayectoria
    const trajectoryData = @json($trajectory);
    const initialFoco = @json($foco);
    const biomasasData = @json($biomasas ?? []);
    
    // Variables globales
    let map;
    let currentHour = 0;
    let isPlaying = false;
    let animationInterval;
    let animationSpeed = 1000;
    let markers = [];
    let circles = [];
    let polyline;
    let allCircles = [];
    let biomasaLayers = [];
    
    // Inicializar mapa
    function initMap() {
        console.log('Initializing map...');
        console.log('Trajectory data:', trajectoryData);
        console.log('Initial foco:', initialFoco);
        
        // Parsear coordenadas de manera segura
        let lat, lng;
        
        if (typeof initialFoco.coordenadas === 'string') {
            const coords = JSON.parse(initialFoco.coordenadas);
            lat = parseFloat(coords[0] || coords.lat);
            lng = parseFloat(coords[1] || coords.lng);
        } else if (Array.isArray(initialFoco.coordenadas)) {
            lat = parseFloat(initialFoco.coordenadas[0] || initialFoco.coordenadas.lat);
            lng = parseFloat(initialFoco.coordenadas[1] || initialFoco.coordenadas.lng);
        } else if (typeof initialFoco.coordenadas === 'object' && initialFoco.coordenadas !== null) {
            // Manejar objeto {lat: ..., lng: ...}
            lat = parseFloat(initialFoco.coordenadas.lat || initialFoco.coordenadas[0]);
            lng = parseFloat(initialFoco.coordenadas.lng || initialFoco.coordenadas[1]);
        } else {
            console.error('Invalid coordinates format:', initialFoco.coordenadas);
            return;
        }
        
        console.log('Coordinates:', lat, lng);
        
        // Validar que tenemos coordenadas válidas
        if (isNaN(lat) || isNaN(lng)) {
            console.error('Invalid lat/lng values');
            document.getElementById('predictionMap').innerHTML = '<div class="alert alert-danger">Error: Coordenadas inválidas</div>';
            return;
        }
        
        // Validar que tenemos datos de trayectoria
        if (!trajectoryData || trajectoryData.length === 0) {
            console.error('No trajectory data available');
            document.getElementById('predictionMap').innerHTML = '<div class="alert alert-warning">No hay datos de trayectoria disponibles</div>';
            return;
        }
        
        map = L.map('predictionMap').setView([lat, lng], 12);
        console.log('Map created');
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);
        console.log('Tiles added');
        
        // Dibujar biomasas en el mapa
        drawBiomasas();
        
        // Marcador del foco inicial
        L.marker([lat, lng], {
            icon: L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#c30b82;width:30px;height:30px;border-radius:50%;border:3px solid white;box-shadow:0 0 10px rgba(0,0,0,0.5);'></div>",
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            })
        }).addTo(map)
        .bindPopup(`
            <div class="popup-title"><i class="fas fa-map-marker-alt"></i> Punto de Origen</div>
            <div class="popup-stat">
                <span class="popup-stat-label">Ubicación:</span>
                <span class="popup-stat-value">${initialFoco.ubicacion || 'N/A'}</span>
            </div>
            <div class="popup-stat">
                <span class="popup-stat-label">Intensidad Inicial:</span>
                <span class="popup-stat-value">${initialFoco.intensidad || 'N/A'}</span>
            </div>
            <div class="popup-stat">
                <span class="popup-stat-label">Fecha:</span>
                <span class="popup-stat-value">${initialFoco.fecha_deteccion ? new Date(initialFoco.fecha_deteccion).toLocaleDateString() : initialFoco.fecha ? new Date(initialFoco.fecha).toLocaleDateString() : 'N/A'}</span>
            </div>
        `);
        
        console.log('Initial marker added');
        
        // Mostrar primer punto
        updateMapToHour(0);
        console.log('Map initialization complete');
    }
    
    // Obtener color según intensidad
    function getIntensityColor(intensity) {
        if (intensity <= 3) return '#90EE90'; // Verde claro
        if (intensity <= 5) return '#FFD700'; // Amarillo
        if (intensity <= 7) return '#FFA500'; // Naranja
        if (intensity <= 8.5) return '#FF4500'; // Rojo naranja
        return '#8B0000'; // Rojo oscuro
    }
    
    // Actualizar mapa a una hora específica
    function updateMapToHour(hour) {
        currentHour = hour;
        
        // Limpiar marcadores y círculos anteriores
        markers.forEach(m => map.removeLayer(m));
        circles.forEach(c => map.removeLayer(c));
        markers = [];
        circles = [];
        
        const point = trajectoryData[hour];
        if (!point) return;
        
        // Crear círculo de propagación
        const circle = L.circle([point.lat, point.lng], {
            radius: point.spread_radius_km * 1000, // Convertir a metros
            color: getIntensityColor(point.intensity),
            fillColor: getIntensityColor(point.intensity),
            fillOpacity: 0.35,
            weight: 3,
            opacity: 0.8,
            className: 'pulse-animation'
        }).addTo(map);
        
        // Círculo de borde para mostrar claramente el radio
        const radiusCircle = L.circle([point.lat, point.lng], {
            radius: point.spread_radius_km * 1000,
            color: '#fff',
            fillColor: 'transparent',
            fillOpacity: 0,
            weight: 2,
            opacity: 0.9,
            dashArray: '10, 5'
        }).addTo(map);
        
        radiusCircle.bindTooltip(`Radio de propagación: ${point.spread_radius_km.toFixed(2)} km`, {
            permanent: false,
            direction: 'top',
            offset: [0, -10]
        });
        
        circles.push(circle);
        circles.push(radiusCircle);
        
        // Crear marcador para el punto
        const marker = L.circleMarker([point.lat, point.lng], {
            radius: 8,
            fillColor: getIntensityColor(point.intensity),
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 1
        }).addTo(map);
        
        marker.bindPopup(`
            <div class="popup-title"><i class="far fa-clock"></i> Hora ${point.hour}</div>
            ${point.extinguished ? '<div class="alert alert-success mb-2" style="padding: 5px; margin: 0;"><small><i class="fas fa-fire-extinguisher"></i> Fuego Extinguido</small></div>' : ''}
            ${point.biomasa ? `<div class="alert alert-info mb-2" style="padding: 5px; margin: 0; background: #17a2b8; color: white;">
                <small><i class="fas fa-leaf"></i> Zona de Biomasa: <strong>${point.biomasa.tipo}</strong></small><br>
                <small>Modificador: <strong>${point.biomasa.modifier}x</strong> | Densidad: ${point.biomasa.densidad || 'N/A'}</small>
            </div>` : ''}
            <div class="popup-stat">
                <span class="popup-stat-label">Intensidad:</span>
                <span class="popup-stat-value" style="color:${getIntensityColor(point.intensity)}">${point.intensity.toFixed(2)}</span>
            </div>
            <div class="popup-stat">
                <span class="popup-stat-label">Radio:</span>
                <span class="popup-stat-value">${point.spread_radius_km.toFixed(2)} km</span>
            </div>
            <div class="popup-stat">
                <span class="popup-stat-label">Área:</span>
                <span class="popup-stat-value">${point.affected_area_km2.toFixed(2)} km²</span>
            </div>
            <div class="popup-stat">
                <span class="popup-stat-label">Perímetro:</span>
                <span class="popup-stat-value">${point.perimeter_km.toFixed(2)} km</span>
            </div>
            <div class="popup-stat">
                <span class="popup-stat-label">Coordenadas:</span>
                <span class="popup-stat-value">${point.lat.toFixed(4)}, ${point.lng.toFixed(4)}</span>
            </div>
        `);
        
        markers.push(marker);
        
        // Dibujar trayectoria hasta este punto
        if (hour > 0) {
            const pathCoords = trajectoryData.slice(0, hour + 1).map(p => [p.lat, p.lng]);
            
            if (polyline) {
                map.removeLayer(polyline);
            }
            
            polyline = L.polyline(pathCoords, {
                color: '#007bff',
                weight: 3,
                opacity: 0.7,
                dashArray: '10, 10',
                className: 'fire-trail'
            }).addTo(map);
        }
        
        // Actualizar slider y display
        document.getElementById('timelineSlider').value = hour;
        document.getElementById('currentHourDisplay').textContent = `Hora: ${hour}`;
        
        // Actualizar panel de estadísticas
        updateStatsPanel(point);
        
        // Centrar mapa en el punto actual
        map.setView([point.lat, point.lng], map.getZoom());
    }
    
    // Actualizar panel de estadísticas
    function updateStatsPanel(point) {
        document.getElementById('statHour').textContent = point.hour;
        document.getElementById('statIntensity').textContent = point.intensity.toFixed(2);
        document.getElementById('statIntensity').style.color = getIntensityColor(point.intensity);
        document.getElementById('statRadius').textContent = point.spread_radius_km.toFixed(2) + ' km';
        document.getElementById('statArea').textContent = point.affected_area_km2.toFixed(2) + ' km²';
        document.getElementById('statPerimeter').textContent = point.perimeter_km.toFixed(2) + ' km';
        document.getElementById('statCoords').textContent = `${point.lat.toFixed(4)}, ${point.lng.toFixed(4)}`;
        
        // Mostrar alertas según intensidad
        const alertDiv = document.getElementById('statAlert');
        const alertText = document.getElementById('alertText');
        
        if (point.extinguished) {
            alertDiv.style.display = 'block';
            alertDiv.className = 'alert alert-success mt-3';
            alertText.innerHTML = '<i class="fas fa-fire-extinguisher"></i> Fuego extinguido - Combustible agotado';
        } else if (point.intensity >= 9) {
            alertDiv.style.display = 'block';
            alertDiv.className = 'alert alert-danger mt-3';
            alertText.textContent = '¡PELIGRO EXTREMO! Intensidad crítica';
        } else if (point.intensity >= 7) {
            alertDiv.style.display = 'block';
            alertDiv.className = 'alert alert-warning mt-3';
            alertText.textContent = 'Intensidad alta - Precaución extrema';
        } else {
            alertDiv.style.display = 'none';
        }
    }
    
    // Toggle play/pause
    function togglePlayPause() {
        isPlaying = !isPlaying;
        const playIcon = document.getElementById('playIcon');
        
        if (isPlaying) {
            playIcon.className = 'fas fa-pause';
            startAnimation();
        } else {
            playIcon.className = 'fas fa-play';
            stopAnimation();
        }
    }
    
    // Iniciar animación
    function startAnimation() {
        animationInterval = setInterval(() => {
            if (currentHour < trajectoryData.length - 1) {
                updateMapToHour(currentHour + 1);
            } else {
                stopAnimation();
                document.getElementById('playIcon').className = 'fas fa-play';
                isPlaying = false;
            }
        }, animationSpeed);
    }
    
    // Detener animación
    function stopAnimation() {
        if (animationInterval) {
            clearInterval(animationInterval);
            animationInterval = null;
        }
    }
    
    // Reiniciar animación
    function resetAnimation() {
        stopAnimation();
        isPlaying = false;
        document.getElementById('playIcon').className = 'fas fa-play';
        updateMapToHour(0);
    }
    
    // Paso adelante
    function stepForward() {
        if (currentHour < trajectoryData.length - 1) {
            stopAnimation();
            isPlaying = false;
            document.getElementById('playIcon').className = 'fas fa-play';
            updateMapToHour(currentHour + 1);
        }
    }
    
    // Paso atrás
    function stepBackward() {
        if (currentHour > 0) {
            stopAnimation();
            isPlaying = false;
            document.getElementById('playIcon').className = 'fas fa-play';
            updateMapToHour(currentHour - 1);
        }
    }
    
    // Cambiar velocidad
    function changeSpeed() {
        const speed = parseInt(document.getElementById('speedControl').value);
        animationSpeed = speed;
        
        if (isPlaying) {
            stopAnimation();
            startAnimation();
        }
    }
    
    // Mostrar toda la trayectoria
    function showAllTrajectory() {
        // Limpiar círculos anteriores
        allCircles.forEach(c => map.removeLayer(c));
        allCircles = [];
        
        // Dibujar todos los círculos con opacidad reducida
        trajectoryData.forEach((point, index) => {
            // Círculo de propagación (radio del fuego)
            const circle = L.circle([point.lat, point.lng], {
                radius: point.spread_radius_km * 1000, // Convertir a metros
                color: getIntensityColor(point.intensity),
                fillColor: getIntensityColor(point.intensity),
                fillOpacity: 0.2,
                weight: 2,
                opacity: 0.5,
                dashArray: '5, 5'
            }).addTo(map);
            
            circle.bindTooltip(`Hora ${point.hour} - Radio: ${point.spread_radius_km.toFixed(2)} km`, {
                permanent: false,
                direction: 'center'
            });
            
            // Marcador del punto central
            const marker = L.circleMarker([point.lat, point.lng], {
                radius: 5,
                fillColor: getIntensityColor(point.intensity),
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 1
            }).addTo(map);
            
            marker.bindPopup(`
                <strong>Hora ${point.hour}</strong><br>
                Intensidad: ${point.intensity.toFixed(2)}<br>
                Radio: ${point.spread_radius_km.toFixed(2)} km<br>
                Área: ${point.affected_area_km2.toFixed(2)} km²
            `);
            
            allCircles.push(circle);
            allCircles.push(marker);
        });
        
        // Ajustar vista para mostrar toda la trayectoria
        const bounds = L.latLngBounds(trajectoryData.map(p => [p.lat, p.lng]));
        map.fitBounds(bounds, { padding: [50, 50] });
    }
    
    // Dibujar biomasas en el mapa
    function drawBiomasas() {
        console.log('Drawing biomasas:', biomasasData.length);
        
        biomasasData.forEach((biomasa) => {
            if (!biomasa.coordenadas || biomasa.coordenadas.length < 3) {
                console.warn('Biomasa sin coordenadas válidas:', biomasa.id);
                return;
            }
            
            const tipo = biomasa.tipo_biomasa?.tipo_biomasa || 'Desconocido';
            const color = biomasa.tipo_biomasa?.color || '#808080';
            const modifier = biomasa.tipo_biomasa?.modificador_intensidad || 1.0;
            
            // Convertir coordenadas a formato Leaflet [[lat, lng], ...]
            const latLngs = biomasa.coordenadas.map(coord => {
                if (Array.isArray(coord)) {
                    return [parseFloat(coord[0]), parseFloat(coord[1])];
                }
                return [parseFloat(coord.lat || coord[0]), parseFloat(coord.lng || coord[1])];
            });
            
            // Crear polígono de biomasa
            const polygon = L.polygon(latLngs, {
                color: color,
                fillColor: color,
                fillOpacity: 0.15,
                weight: 2,
                opacity: 0.5,
                dashArray: '5, 5'
            }).addTo(map);
            
            // Tooltip y popup para la biomasa
            polygon.bindTooltip(`<strong>${tipo}</strong>`, {
                sticky: true,
                direction: 'center',
                className: 'biomasa-tooltip'
            });
            
            polygon.bindPopup(`
                <div class="popup-title">
                    <i class="fas fa-leaf"></i> Zona de Biomasa
                </div>
                <div class="popup-stat">
                    <span class="popup-stat-label">Tipo:</span>
                    <span class="popup-stat-value"><strong>${tipo}</strong></span>
                </div>
                <div class="popup-stat">
                    <span class="popup-stat-label">Modificador:</span>
                    <span class="popup-stat-value" style="color: ${modifier > 1.0 ? '#dc3545' : (modifier < 1.0 ? '#28a745' : '#6c757d')}">
                        <strong>${modifier}x</strong>
                    </span>
                </div>
                <div class="popup-stat">
                    <span class="popup-stat-label">Área:</span>
                    <span class="popup-stat-value">${(biomasa.area_m2 / 1000000).toFixed(2)} km²</span>
                </div>
                <div class="popup-stat">
                    <span class="popup-stat-label">Densidad:</span>
                    <span class="popup-stat-value">${biomasa.densidad || 'N/A'}</span>
                </div>
                <div class="popup-stat">
                    <span class="popup-stat-label">Efecto:</span>
                    <span class="popup-stat-value">
                        ${modifier > 1.5 ? '<span class="badge badge-danger">Propagación MUY RÁPIDA</span>' : 
                          modifier > 1.0 ? '<span class="badge badge-warning">Propagación Acelerada</span>' :
                          modifier < 0.8 ? '<span class="badge badge-success">Propagación Reducida</span>' :
                          '<span class="badge badge-secondary">Propagación Normal</span>'}
                    </span>
                </div>
            `);
            
            biomasaLayers.push(polygon);
        });
        
        console.log('Biomasas drawn:', biomasaLayers.length);
    }
    
    // Event listener para el slider
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded');
        
        // Verificar que el elemento del mapa existe
        const mapElement = document.getElementById('predictionMap');
        if (!mapElement) {
            console.error('Map element not found!');
            return;
        }
        
        console.log('Map element found, initializing...');
        
        try {
            initMap();
        } catch (error) {
            console.error('Error initializing map:', error);
            mapElement.innerHTML = `<div class="alert alert-danger">
                <strong>Error al cargar el mapa:</strong> ${error.message}
                <br><small>Revisa la consola para más detalles</small>
            </div>`;
        }
        
        const slider = document.getElementById('timelineSlider');
        if (slider) {
            slider.addEventListener('input', function(e) {
                stopAnimation();
                isPlaying = false;
                document.getElementById('playIcon').className = 'fas fa-play';
                updateMapToHour(parseInt(e.target.value));
            });
        }
    });
</script>
@endsection
