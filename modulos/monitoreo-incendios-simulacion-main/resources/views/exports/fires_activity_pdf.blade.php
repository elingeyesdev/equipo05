<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Actividad de Incendios</title>
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
            border-bottom: 4px solid #e74c3c;
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
        .stat-card.total { background: #fff3e0; }
        .stat-card.total::before { background: #ff9800; }
        .stat-card.avg { background: #e3f2fd; }
        .stat-card.avg::before { background: #2196f3; }
        .stat-card.max { background: #ffebee; }
        .stat-card.max::before { background: #f44336; }
        .stat-card.min { background: #e8f5e9; }
        .stat-card.min::before { background: #4caf50; }
        
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
        .stat-card.total .value { color: #f57c00; }
        .stat-card.avg .value { color: #1976d2; }
        .stat-card.max .value { color: #d32f2f; }
        .stat-card.min .value { color: #388e3c; }
        
        /* Sección de gráficos */
        .charts-section {
            margin: 25px 0;
            page-break-inside: avoid;
        }
        .chart-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 10px;
        }
        .chart-container {
            display: table-cell;
            width: 50%;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .chart-title {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3498db;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Gráfico de barras ASCII art mejorado */
        .bar-chart {
            margin: 10px 0;
        }
        .bar-item {
            margin-bottom: 8px;
            position: relative;
        }
        .bar-label {
            font-size: 8px;
            font-weight: 600;
            margin-bottom: 3px;
            color: #34495e;
        }
        .bar-container {
            background: #ecf0f1;
            height: 18px;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
        }
        .bar-fill {
            height: 100%;
            border-radius: 4px;
            background: #e74c3c;
            position: relative;
            transition: width 0.3s ease;
        }
        .bar-value {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 8px;
            font-weight: bold;
            color: #2c3e50;
            background: rgba(255, 255, 255, 0.9);
            padding: 2px 6px;
            border-radius: 3px;
        }
        
        /* Distribución por nivel */
        .level-distribution {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
        }
        .level-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 12px;
            border-radius: 6px;
        }
        .level-item.high {
            background: #ffebee;
            border-left: 4px solid #e74c3c;
        }
        .level-item.medium {
            background: #fff3e0;
            border-left: 4px solid #f39c12;
        }
        .level-item.low {
            background: #e3f2fd;
            border-left: 4px solid #3498db;
        }
        .level-item .level-label {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .level-item.high .level-label { color: #c0392b; }
        .level-item.medium .level-label { color: #d68910; }
        .level-item.low .level-label { color: #2980b9; }
        .level-item .level-value {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }
        .level-item.high .level-value { color: #e74c3c; }
        .level-item.medium .level-value { color: #f39c12; }
        .level-item.low .level-value { color: #3498db; }
        .level-item .level-percent {
            font-size: 8px;
            color: #7f8c8d;
        }
        
        /* Tabla de datos */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin: 25px 0 15px 0;
            padding: 8px 0;
            border-bottom: 3px solid #3498db;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        thead {
            background: #34495e;
        }
        th {
            background: #34495e;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 8px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 8px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e8f4f8;
        }
        
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        .badge-danger { 
            background: #e74c3c;
            color: #ffffff;
        }
        .badge-warning { 
            background: #f39c12;
            color: #2c3e50;
        }
        .badge-info { 
            background: #3498db;
            color: #ffffff;
        }
        
        /* Footer profesional */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #ecf0f1;
            text-align: center;
        }
        .footer .brand {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .footer .info {
            font-size: 8px;
            color: #95a5a6;
            font-style: italic;
        }
        
        /* Separador visual */
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, #3498db 50%, transparent 100%);
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>🔥 REPORTE DE INCENDIOS</h1>
            <div class="subtitle">Análisis Detallado de Actividad de Focos de Calor</div>
            <div class="date">📅 Generado: {{ now()->format('d/m/Y H:i') }}</div>
            @if(isset($filters['fechaInicio']) || isset($filters['fechaFin']))
            <div class="date" style="margin-top: 3px;">
                📊 Período: {{ isset($filters['fechaInicio']) ? date('d/m/Y', strtotime($filters['fechaInicio'])) : 'N/A' }} 
                al 
                {{ isset($filters['fechaFin']) ? date('d/m/Y', strtotime($filters['fechaFin'])) : 'N/A' }}
            </div>
            @endif
        </div>

        <!-- Dashboard de Estadísticas -->
        <div class="stats-dashboard">
            <div class="stat-card total">
                <div class="icon">🔥</div>
                <div class="label">Total de Focos</div>
                <div class="value">{{ $statistics['total'] ?? 0 }}</div>
            </div>
            <div class="stat-card avg">
                <div class="icon">📊</div>
                <div class="label">Intensidad Promedio</div>
                <div class="value">{{ number_format($statistics['avg_intensity'] ?? 0, 1) }}</div>
            </div>
            <div class="stat-card max">
                <div class="icon">⚠️</div>
                <div class="label">Intensidad Máxima</div>
                <div class="value">{{ number_format($statistics['max_intensity'] ?? 0, 1) }}</div>
            </div>
            <div class="stat-card min">
                <div class="icon">✅</div>
                <div class="label">Intensidad Mínima</div>
                <div class="value">{{ number_format($statistics['min_intensity'] ?? 0, 1) }}</div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Sección de Gráficos -->
        <div class="charts-section">
            @php
                // Calcular distribución por nivel
                $nivelesCount = ['Alta' => 0, 'Media' => 0, 'Baja' => 0];
                foreach ($fires as $fire) {
                    if ($fire->intensidad >= 7) $nivelesCount['Alta']++;
                    elseif ($fire->intensidad >= 4) $nivelesCount['Media']++;
                    else $nivelesCount['Baja']++;
                }
                
                // Top 5 ubicaciones
                $topUbicaciones = [];
                foreach ($fires as $fire) {
                    $ubicacion = $fire->ubicacion ?? 'Sin ubicación';
                    if (!isset($topUbicaciones[$ubicacion])) {
                        $topUbicaciones[$ubicacion] = 0;
                    }
                    $topUbicaciones[$ubicacion]++;
                }
                arsort($topUbicaciones);
                $topUbicaciones = array_slice($topUbicaciones, 0, 5, true);
                $maxUbicacion = !empty($topUbicaciones) ? max($topUbicaciones) : 1;
            @endphp

            <div class="chart-row">
                <!-- Distribución por Nivel de Intensidad -->
                <div class="chart-container">
                    <div class="chart-title">📊 Distribución por Nivel de Intensidad</div>
                    <div class="level-distribution">
                        <div class="level-item high">
                            <div class="level-label">🔴 Alta</div>
                            <div class="level-value">{{ $nivelesCount['Alta'] }}</div>
                            <div class="level-percent">
                                {{ $statistics['total'] > 0 ? number_format(($nivelesCount['Alta'] / $statistics['total']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="level-item medium">
                            <div class="level-label">🟡 Media</div>
                            <div class="level-value">{{ $nivelesCount['Media'] }}</div>
                            <div class="level-percent">
                                {{ $statistics['total'] > 0 ? number_format(($nivelesCount['Media'] / $statistics['total']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="level-item low">
                            <div class="level-label">🔵 Baja</div>
                            <div class="level-value">{{ $nivelesCount['Baja'] }}</div>
                            <div class="level-percent">
                                {{ $statistics['total'] > 0 ? number_format(($nivelesCount['Baja'] / $statistics['total']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Ubicaciones -->
                <div class="chart-container">
                    <div class="chart-title">📍 Top 5 Ubicaciones con Más Focos</div>
                    <div class="bar-chart">
                        @foreach($topUbicaciones as $ubicacion => $count)
                        <div class="bar-item">
                            <div class="bar-label">{{ Str::limit($ubicacion, 30) }}</div>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: {{ ($count / $maxUbicacion) * 100 }}%;">
                                    <span class="bar-value">{{ $count }} focos</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Tabla de Datos Detallados -->
        <div class="section-title">📋 Detalle de Focos de Incendio</div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 10%;">Fecha/Hora</th>
                    <th style="width: 28%;">Ubicación</th>
                    <th style="width: 12%;">Latitud</th>
                    <th style="width: 12%;">Longitud</th>
                    <th style="width: 10%;">Intensidad</th>
                    <th style="width: 10%;">Nivel</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fires as $fire)
                <tr>
                    <td><strong>{{ $fire->id }}</strong></td>
                    <td>{{ $fire->fecha->format('d/m/Y H:i') }}</td>
                    <td>{{ Str::limit($fire->ubicacion ?? 'Sin ubicación', 40) }}</td>
                    @php
                        $coords = $fire->coordenadas;
                        $lat = $lng = 'N/A';
                        if ($coords && is_array($coords)) {
                            $lat = $coords['lat'] ?? $coords['latitude'] ?? $coords[0] ?? 'N/A';
                            $lng = $coords['lng'] ?? $coords['lon'] ?? $coords['longitude'] ?? $coords[1] ?? 'N/A';
                            if ($lat !== 'N/A') $lat = number_format((float)$lat, 6);
                            if ($lng !== 'N/A') $lng = number_format((float)$lng, 6);
                        }
                    @endphp
                    <td>{{ $lat }}</td>
                    <td>{{ $lng }}</td>
                    <td><strong>{{ number_format($fire->intensidad, 2) }}</strong></td>
                    <td>
                        @php
                            $nivel = 'Baja';
                            $badge = 'info';
                            if ($fire->intensidad >= 7) {
                                $nivel = 'Alta';
                                $badge = 'danger';
                            } elseif ($fire->intensidad >= 4) {
                                $nivel = 'Media';
                                $badge = 'warning';
                            }
                        @endphp
                        <span class="badge badge-{{ $badge }}">{{ $nivel }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #95a5a6;">
                        <strong>📭 No se encontraron focos de incendio con los filtros aplicados</strong>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <div class="brand">🛡️ SIPII - Sistema Integral de Prevención de Incendios</div>
            <div class="info">Este reporte contiene {{ $fires->count() }} registro(s) de focos detectados | Confidencial</div>
        </div>
    </div>
</body>
</html>
