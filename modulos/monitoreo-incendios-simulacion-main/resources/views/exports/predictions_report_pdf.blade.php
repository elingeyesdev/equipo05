<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Predicciones</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 9px;
            color: #2c3e50;
            line-height: 1.4;
        }
        .page {
            padding: 15px;
        }
        
        /* Header elegante */
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            margin: -15px -15px 20px -15px;
            text-align: center;
            border-bottom: 4px solid #667eea;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .header .subtitle {
            font-size: 11px;
            opacity: 0.95;
            font-weight: 300;
        }
        .header .date {
            font-size: 10px;
            margin-top: 8px;
            opacity: 0.9;
            font-style: italic;
        }
        
        /* Dashboard de estadísticas moderno */
        .stats-dashboard {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-collapse: separate;
            border-spacing: 10px;
        }
        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }
        .stat-card.purple { background: #faf5ff; }
        .stat-card.purple::before { background: #667eea; }
        .stat-card.blue { background: #e3f2fd; }
        .stat-card.blue::before { background: #2196f3; }
        .stat-card.green { background: #e8f5e9; }
        .stat-card.green::before { background: #4caf50; }
        .stat-card.orange { background: #fff3e0; }
        .stat-card.orange::before { background: #ff9800; }
        
        .stat-card .icon {
            font-size: 20px;
            margin-bottom: 8px;
            opacity: 0.8;
        }
        .stat-card .label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #7f8c8d;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            line-height: 1;
        }
        .stat-card.purple .value { color: #667eea; }
        .stat-card.blue .value { color: #1976d2; }
        .stat-card.green .value { color: #388e3c; }
        .stat-card.orange .value { color: #f57c00; }
        
        /* Filtros aplicados */
        .filters {
            background: #ecf0f1;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 8px;
            border-left: 4px solid #667eea;
        }
        .filters strong {
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Sección de gráfico */
        .chart-section {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .chart-title {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Pie chart items */
        .pie-items {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        .pie-item {
            display: table-row;
        }
        .pie-item > div {
            display: table-cell;
            padding: 8px 5px;
            vertical-align: middle;
        }
        .pie-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }
        .pie-color.high { background-color: #e74c3c; }
        .pie-color.medium { background-color: #f39c12; }
        .pie-color.low { background-color: #27ae60; }
        .pie-label {
            font-size: 9px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        /* Tabla principal */
        .data-table-section {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #667eea;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-weight: 600;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 6px 5px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 7px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e8f4f8;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 6px;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-high {
            background-color: #e74c3c;
            color: white;
        }
        .badge-medium {
            background-color: #f39c12;
            color: white;
        }
        .badge-low {
            background-color: #27ae60;
            color: white;
        }
        
        /* Footer */
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 2px solid #ecf0f1;
            text-align: center;
            font-size: 8px;
            color: #95a5a6;
        }
        .footer strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
            font-size: 9px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>🔮 REPORTE DE PREDICCIONES</h1>
            <div class="subtitle">Análisis de Propagación y Riesgo de Incendios</div>
            <div class="date">Generado el {{ date('d/m/Y H:i:s') }}</div>
        </div>

        @if(!empty($filters['fechaInicio']) || !empty($filters['fechaFin']) || !empty($filters['riskMin']) || !empty($filters['riskMax']))
        <div class="filters">
            <strong>Filtros aplicados:</strong>
            @if(!empty($filters['fechaInicio']))
                Fecha desde: {{ $filters['fechaInicio'] }}
            @endif
            @if(!empty($filters['fechaFin']))
                | Fecha hasta: {{ $filters['fechaFin'] }}
            @endif
            @if(!empty($filters['riskMin']))
                | Riesgo mínimo: {{ $filters['riskMin'] }}
            @endif
            @if(!empty($filters['riskMax']))
                | Riesgo máximo: {{ $filters['riskMax'] }}
            @endif
        </div>
        @endif

        <div class="stats-dashboard">
            <div class="stat-card purple">
                <div class="icon">📊</div>
                <div class="label">Total Predicciones</div>
                <div class="value">{{ $statistics['total'] ?? 0 }}</div>
            </div>
            <div class="stat-card blue">
                <div class="icon">⚠️</div>
                <div class="label">Riesgo Promedio</div>
                <div class="value">{{ number_format($statistics['avg_risk'] ?? 0, 2) }}</div>
            </div>
            <div class="stat-card green">
                <div class="icon">🗺️</div>
                <div class="label">Área Total Afectada</div>
                <div class="value">{{ number_format($statistics['total_area'] ?? 0, 1) }}<span style="font-size: 14px;"> km²</span></div>
            </div>
            <div class="stat-card orange">
                <div class="icon">📍</div>
                <div class="label">Puntos Promedio</div>
                <div class="value">{{ number_format($statistics['avg_path_points'] ?? 0, 0) }}</div>
            </div>
        </div>

        <div class="chart-section">
            <div class="chart-title">📊 Distribución por Nivel de Riesgo</div>
            @php
                $riskStats = $statistics['risk_distribution'] ?? ['high' => 0, 'medium' => 0, 'low' => 0];
            @endphp
            <div class="pie-items">
                <div class="pie-item">
                    <div><div class="pie-color high"></div></div>
                    <div class="pie-label">Alto (≥0.7): <strong>{{ $riskStats['high'] }}</strong> predicciones</div>
                </div>
                <div class="pie-item">
                    <div><div class="pie-color medium"></div></div>
                    <div class="pie-label">Medio (0.4-0.7): <strong>{{ $riskStats['medium'] }}</strong> predicciones</div>
                </div>
                <div class="pie-item">
                    <div><div class="pie-color low"></div></div>
                    <div class="pie-label">Bajo (&lt;0.4): <strong>{{ $riskStats['low'] }}</strong> predicciones</div>
                </div>
            </div>
        </div>

        <div class="data-table-section">
            <div class="section-title">📋 Detalle de Predicciones</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 9%;">Fecha</th>
                        <th style="width: 7%;">Hora</th>
                        <th style="width: 15%;">Coordenadas</th>
                        <th style="width: 10%;">Riesgo</th>
                        <th style="width: 10%;">Área</th>
                        <th style="width: 8%;">Puntos</th>
                        <th style="width: 8%;">Temp.</th>
                        <th style="width: 10%;">Viento</th>
                        <th style="width: 8%;">Dir.</th>
                        <th style="width: 10%;">Humedad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($predictions as $prediction)
                        @php
                            $meta = $prediction->meta ?? [];
                            $path = $prediction->path ?? [];
                            $inputs = $meta['input_parameters'] ?? [];
                            
                            // Get coordinates - try foco first, then path[0]
                            $lat = null;
                            $lng = null;
                            
                            $foco = $prediction->focoIncendio;
                            if ($foco && $foco->coordenadas) {
                                $coords = $foco->coordenadas;
                                if (is_array($coords)) {
                                    $lat = $coords[0] ?? null;
                                    $lng = $coords[1] ?? null;
                                }
                            } elseif (isset($path[0])) {
                                $lat = $path[0]['lat'] ?? null;
                                $lng = $path[0]['lng'] ?? null;
                            }

                            // Get max area from path
                            $maxArea = 0;
                            if (is_array($path)) {
                                foreach ($path as $point) {
                                    if (isset($point['affected_area_km2'])) {
                                        $maxArea = max($maxArea, $point['affected_area_km2']);
                                    }
                                }
                            }

                            // Calculate risk from fire_risk_index (0-100 scale)
                            $riskIndex = $meta['fire_risk_index'] ?? 0;
                            $risk = $riskIndex / 100;
                            $riskBadge = 'low';
                            if ($risk >= 0.7) $riskBadge = 'high';
                            elseif ($risk >= 0.4) $riskBadge = 'medium';
                        @endphp
                    <tr>
                        <td><strong>#{{ $prediction->id }}</strong></td>
                        <td>{{ $prediction->predicted_at ? $prediction->predicted_at->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ $prediction->predicted_at ? $prediction->predicted_at->format('H:i') : 'N/A' }}</td>
                        <td>
                            @if($lat && $lng)
                                {{ number_format($lat, 4) }}, {{ number_format($lng, 4) }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $riskBadge }}">
                                {{ number_format($risk, 2) }}
                            </span>
                        </td>
                        <td>{{ number_format($maxArea, 2) }} km²</td>
                        <td style="text-align: center;">{{ count($path) }}</td>
                        <td>{{ isset($inputs['temperature']) ? number_format($inputs['temperature'], 1) : 'N/A' }}°C</td>
                        <td>{{ isset($inputs['wind_speed']) ? number_format($inputs['wind_speed'], 1) : 'N/A' }} km/h</td>
                        <td>{{ $inputs['wind_direction'] ?? 'N/A' }}°</td>
                        <td>{{ isset($inputs['humidity']) ? number_format($inputs['humidity'], 1) : 'N/A' }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer">
            <strong>SIPII - Sistema Integral de Prevención de Incendios</strong>
            <p>Total de registros: {{ $predictions->count() }} | Generado el {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
