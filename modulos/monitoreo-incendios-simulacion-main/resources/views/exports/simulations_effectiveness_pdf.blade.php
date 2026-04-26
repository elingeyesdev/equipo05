<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Efectividad de Simulaciones</title>
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
        
        /* Header */
        .header {
            background: #8e44ad;
            color: white;
            padding: 20px;
            margin: -15px -15px 20px -15px;
            text-align: center;
            border-bottom: 4px solid #6c3483;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
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
        
        /* Dashboard de estadísticas */
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
        .stat-card.total { background: #f4ecf7; }
        .stat-card.total::before { background: #8e44ad; }
        .stat-card.avg-risk { background: #fadbd8; }
        .stat-card.avg-risk::before { background: #e74c3c; }
        .stat-card.volunteers { background: #ebf5fb; }
        .stat-card.volunteers::before { background: #3498db; }
        .stat-card.duration { background: #fef9e7; }
        .stat-card.duration::before { background: #f39c12; }
        
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
        .stat-card.total .value { color: #6c3483; }
        .stat-card.avg-risk .value { color: #c0392b; }
        .stat-card.volunteers .value { color: #2874a6; }
        .stat-card.duration .value { color: #d68910; }
        
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
            border-bottom: 2px solid #8e44ad;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Distribución por riesgo */
        .risk-distribution {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
        }
        .risk-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 12px;
            border-radius: 6px;
        }
        .risk-item.high {
            background: #fadbd8;
            border-left: 4px solid #e74c3c;
        }
        .risk-item.medium {
            background: #fef9e7;
            border-left: 4px solid #f39c12;
        }
        .risk-item.low {
            background: #e8f8f5;
            border-left: 4px solid #27ae60;
        }
        .risk-item .risk-label {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .risk-item.high .risk-label { color: #c0392b; }
        .risk-item.medium .risk-label { color: #d68910; }
        .risk-item.low .risk-label { color: #229954; }
        .risk-item .risk-value {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }
        .risk-item.high .risk-value { color: #e74c3c; }
        .risk-item.medium .risk-value { color: #f39c12; }
        .risk-item.low .risk-value { color: #27ae60; }
        .risk-item .risk-percent {
            font-size: 8px;
            color: #7f8c8d;
        }
        
        /* Gráfico de barras */
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
            background: #8e44ad;
            position: relative;
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
        
        /* Tabla */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin: 25px 0 15px 0;
            padding: 8px 0;
            border-bottom: 3px solid #8e44ad;
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
            background: #6c3483;
        }
        th {
            background: #6c3483;
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
        .badge-success { 
            background: #27ae60;
            color: #ffffff;
        }
        
        /* Footer */
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
        
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, #8e44ad 50%, transparent 100%);
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>🎯 REPORTE DE SIMULACIONES</h1>
            <div class="subtitle">Análisis de Efectividad de Simulaciones de Incendios</div>
            <div class="date">📅 Generado: {{ now()->format('d/m/Y H:i') }}</div>
        </div>

        <!-- Dashboard de Estadísticas -->
        <div class="stats-dashboard">
            <div class="stat-card total">
                <div class="icon">🎮</div>
                <div class="label">Total Simulaciones</div>
                <div class="value">{{ $statistics['total'] ?? 0 }}</div>
            </div>
            <div class="stat-card avg-risk">
                <div class="icon">⚠️</div>
                <div class="label">Riesgo Promedio</div>
                <div class="value">{{ number_format($statistics['avg_risk'] ?? 0, 1) }}</div>
            </div>
            <div class="stat-card volunteers">
                <div class="icon">👥</div>
                <div class="label">Voluntarios Totales</div>
                <div class="value">{{ $statistics['total_volunteers'] ?? 0 }}</div>
            </div>
            <div class="stat-card duration">
                <div class="icon">⏱️</div>
                <div class="label">Duración Prom. (min)</div>
                <div class="value">{{ number_format($statistics['avg_duration'] ?? 0, 0) }}</div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Sección de Gráficos -->
        <div class="charts-section">
            @php
                // Calcular distribución por nivel de riesgo
                $riesgoCount = ['Alto' => 0, 'Medio' => 0, 'Bajo' => 0];
                foreach ($simulations as $sim) {
                    if ($sim->fire_risk >= 0.7) $riesgoCount['Alto']++;
                    elseif ($sim->fire_risk >= 0.4) $riesgoCount['Medio']++;
                    else $riesgoCount['Bajo']++;
                }
                
                // Top 5 condiciones climáticas
                $condicionesClimaticas = [];
                foreach ($simulations as $sim) {
                    $condicion = 'Temp: ' . round($sim->temperature) . '°C | Hum: ' . round($sim->humidity) . '%';
                    if (!isset($condicionesClimaticas[$condicion])) {
                        $condicionesClimaticas[$condicion] = 0;
                    }
                    $condicionesClimaticas[$condicion]++;
                }
                arsort($condicionesClimaticas);
                $condicionesClimaticas = array_slice($condicionesClimaticas, 0, 5, true);
                $maxCondicion = max($condicionesClimaticas) ?: 1;
            @endphp

            <div class="chart-row">
                <!-- Distribución por Nivel de Riesgo -->
                <div class="chart-container">
                    <div class="chart-title">📊 Distribución por Nivel de Riesgo</div>
                    <div class="risk-distribution">
                        <div class="risk-item high">
                            <div class="risk-label">🔴 Alto</div>
                            <div class="risk-value">{{ $riesgoCount['Alto'] }}</div>
                            <div class="risk-percent">
                                {{ $statistics['total'] > 0 ? number_format(($riesgoCount['Alto'] / $statistics['total']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="risk-item medium">
                            <div class="risk-label">🟡 Medio</div>
                            <div class="risk-value">{{ $riesgoCount['Medio'] }}</div>
                            <div class="risk-percent">
                                {{ $statistics['total'] > 0 ? number_format(($riesgoCount['Medio'] / $statistics['total']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="risk-item low">
                            <div class="risk-label">🟢 Bajo</div>
                            <div class="risk-value">{{ $riesgoCount['Bajo'] }}</div>
                            <div class="risk-percent">
                                {{ $statistics['total'] > 0 ? number_format(($riesgoCount['Bajo'] / $statistics['total']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Condiciones Climáticas -->
                <div class="chart-container">
                    <div class="chart-title">🌡️ Top 5 Condiciones Climáticas</div>
                    <div class="bar-chart">
                        @foreach($condicionesClimaticas as $condicion => $count)
                        <div class="bar-item">
                            <div class="bar-label">{{ $condicion }}</div>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: {{ ($count / $maxCondicion) * 100 }}%;">
                                    <span class="bar-value">{{ $count }} sims</span>
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
        <div class="section-title">📋 Detalle de Simulaciones</div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 12%;">Fecha/Hora</th>
                    <th style="width: 8%;">Temp</th>
                    <th style="width: 8%;">Hum</th>
                    <th style="width: 8%;">Viento</th>
                    <th style="width: 8%;">Focos</th>
                    <th style="width: 10%;">Riesgo</th>
                    <th style="width: 8%;">Duración</th>
                    <th style="width: 8%;">Volunt.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($simulations as $sim)
                <tr>
                    <td><strong>{{ $sim->id }}</strong></td>
                    <td>{{ $sim->fecha->format('d/m/Y H:i') }}</td>
                    <td>{{ number_format($sim->temperature, 1) }}°C</td>
                    <td>{{ number_format($sim->humidity, 1) }}%</td>
                    <td>{{ number_format($sim->wind_speed, 1) }} km/h</td>
                    <td><strong>{{ $sim->focos_activos }}</strong></td>
                    <td>
                        @php
                            $riesgo = $sim->fire_risk;
                            $badge = 'success';
                            $label = 'Bajo';
                            if ($riesgo >= 0.7) {
                                $badge = 'danger';
                                $label = 'Alto';
                            } elseif ($riesgo >= 0.4) {
                                $badge = 'warning';
                                $label = 'Medio';
                            }
                        @endphp
                        <span class="badge badge-{{ $badge }}">{{ $label }}</span>
                    </td>
                    <td>{{ number_format($sim->duracion, 0) }} min</td>
                    <td>{{ $sim->num_voluntarios_enviados }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 30px; color: #95a5a6;">
                        <strong>📭 No se encontraron simulaciones con los filtros aplicados</strong>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <div class="brand">🛡️ SIPII - Sistema Integral de Prevención de Incendios</div>
            <div class="info">Este reporte contiene {{ $simulations->count() }} registro(s) de simulaciones | Confidencial</div>
        </div>
    </div>
</body>
</html>
