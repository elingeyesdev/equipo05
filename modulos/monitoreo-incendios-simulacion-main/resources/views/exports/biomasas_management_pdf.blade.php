<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Gestión de Biomasas</title>
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
            background: #27ae60;
            color: white;
            padding: 20px;
            margin: -15px -15px 20px -15px;
            text-align: center;
            border-bottom: 4px solid #229954;
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
        .stat-card.total { background: #e8f8f5; }
        .stat-card.total::before { background: #27ae60; }
        .stat-card.approved { background: #ebf5fb; }
        .stat-card.approved::before { background: #3498db; }
        .stat-card.pending { background: #fef9e7; }
        .stat-card.pending::before { background: #f39c12; }
        .stat-card.rejected { background: #fadbd8; }
        .stat-card.rejected::before { background: #e74c3c; }
        
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
        .stat-card.total .value { color: #229954; }
        .stat-card.approved .value { color: #2874a6; }
        .stat-card.pending .value { color: #d68910; }
        .stat-card.rejected .value { color: #c0392b; }
        
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
            border-bottom: 2px solid #27ae60;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Distribución por estado */
        .state-distribution {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
        }
        .state-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 12px;
            border-radius: 6px;
        }
        .state-item.approved {
            background: #ebf5fb;
            border-left: 4px solid #3498db;
        }
        .state-item.pending {
            background: #fef9e7;
            border-left: 4px solid #f39c12;
        }
        .state-item.rejected {
            background: #fadbd8;
            border-left: 4px solid #e74c3c;
        }
        .state-item .state-label {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .state-item.approved .state-label { color: #2874a6; }
        .state-item.pending .state-label { color: #d68910; }
        .state-item.rejected .state-label { color: #c0392b; }
        .state-item .state-value {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }
        .state-item.approved .state-value { color: #3498db; }
        .state-item.pending .state-value { color: #f39c12; }
        .state-item.rejected .state-value { color: #e74c3c; }
        .state-item .state-percent {
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
            background: #27ae60;
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
            border-bottom: 3px solid #27ae60;
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
            background: #229954;
        }
        th {
            background: #229954;
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
        .badge-success { 
            background: #27ae60;
            color: #ffffff;
        }
        .badge-warning { 
            background: #f39c12;
            color: #2c3e50;
        }
        .badge-danger { 
            background: #e74c3c;
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
            background: linear-gradient(90deg, transparent 0%, #27ae60 50%, transparent 100%);
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>🌳 REPORTE DE BIOMASAS</h1>
            <div class="subtitle">Gestión y Control de Áreas de Biomasa</div>
            <div class="date">📅 Generado: {{ now()->format('d/m/Y H:i') }}</div>
        </div>

        <!-- Dashboard de Estadísticas -->
        <div class="stats-dashboard">
            <div class="stat-card total">
                <div class="icon">🌲</div>
                <div class="label">Total de Biomasas</div>
                <div class="value">{{ $statistics['total'] ?? 0 }}</div>
            </div>
            <div class="stat-card approved">
                <div class="icon">✅</div>
                <div class="label">Aprobadas</div>
                <div class="value">{{ $statistics['approved'] ?? 0 }}</div>
            </div>
            <div class="stat-card pending">
                <div class="icon">⏳</div>
                <div class="label">Pendientes</div>
                <div class="value">{{ $statistics['pending'] ?? 0 }}</div>
            </div>
            <div class="stat-card rejected">
                <div class="icon">❌</div>
                <div class="label">Rechazadas</div>
                <div class="value">{{ $statistics['rejected'] ?? 0 }}</div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Sección de Gráficos -->
        <div class="charts-section">
            @php
                // Calcular distribución por estado
                $estadosCount = ['aprobada' => 0, 'pendiente' => 0, 'rechazada' => 0];
                foreach ($biomasas as $biomasa) {
                    $estado = strtolower($biomasa->estado);
                    if (isset($estadosCount[$estado])) {
                        $estadosCount[$estado]++;
                    }
                }
                
                // Top 5 tipos de biomasa
                $tiposBiomasa = [];
                foreach ($biomasas as $biomasa) {
                    $tipo = $biomasa->tipoBiomasa->tipo_biomasa ?? 'Sin tipo';
                    if (!isset($tiposBiomasa[$tipo])) {
                        $tiposBiomasa[$tipo] = 0;
                    }
                    $tiposBiomasa[$tipo]++;
                }
                arsort($tiposBiomasa);
                $tiposBiomasa = array_slice($tiposBiomasa, 0, 5, true);
                $maxTipo = max($tiposBiomasa) ?: 1;
            @endphp

            <div class="chart-row">
                <!-- Distribución por Estado -->
                <div class="chart-container">
                    <div class="chart-title">📊 Distribución por Estado</div>
                    <div class="state-distribution">
                        <div class="state-item approved">
                            <div class="state-label">✅ Aprobadas</div>
                            <div class="state-value">{{ $estadosCount['aprobada'] }}</div>
                            <div class="state-percent">
                                {{ $statistics['total'] > 0 ? number_format(($estadosCount['aprobada'] / $statistics['total']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="state-item pending">
                            <div class="state-label">⏳ Pendientes</div>
                            <div class="state-value">{{ $estadosCount['pendiente'] }}</div>
                            <div class="state-percent">
                                {{ $statistics['total'] > 0 ? number_format(($estadosCount['pendiente'] / $statistics['total']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="state-item rejected">
                            <div class="state-label">❌ Rechazadas</div>
                            <div class="state-value">{{ $estadosCount['rechazada'] }}</div>
                            <div class="state-percent">
                                {{ $statistics['total'] > 0 ? number_format(($estadosCount['rechazada'] / $statistics['total']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Tipos de Biomasa -->
                <div class="chart-container">
                    <div class="chart-title">🌿 Top 5 Tipos de Biomasa</div>
                    <div class="bar-chart">
                        @foreach($tiposBiomasa as $tipo => $count)
                        <div class="bar-item">
                            <div class="bar-label">{{ Str::limit($tipo, 30) }}</div>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: {{ ($count / $maxTipo) * 100 }}%;">
                                    <span class="bar-value">{{ $count }} biomasas</span>
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
        <div class="section-title">📋 Detalle de Biomasas</div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 30%;">Ubicación</th>
                    <th style="width: 10%;">Área (ha)</th>
                    <th style="width: 12%;">Densidad</th>
                    <th style="width: 18%;">Tipo</th>
                    <th style="width: 12%;">Estado</th>
                    <th style="width: 13%;">Creado Por</th>
                </tr>
            </thead>
            <tbody>
                @forelse($biomasas as $biomasa)
                <tr>
                    <td><strong>{{ $biomasa->id }}</strong></td>
                    <td>{{ Str::limit($biomasa->ubicacion ?? 'Sin ubicación', 45) }}</td>
                    <td>{{ number_format($biomasa->area_m2 / 10000, 2) }}</td>
                    <td>{{ ucfirst($biomasa->densidad ?? 'N/A') }}</td>
                    <td>{{ Str::limit($biomasa->tipoBiomasa->tipo_biomasa ?? 'N/A', 25) }}</td>
                    <td>
                        @php
                            $estado = strtolower($biomasa->estado);
                            $badge = 'warning';
                            $label = ucfirst($biomasa->estado);
                            if ($estado === 'aprobada') {
                                $badge = 'success';
                            } elseif ($estado === 'rechazada') {
                                $badge = 'danger';
                            }
                        @endphp
                        <span class="badge badge-{{ $badge }}">{{ $label }}</span>
                    </td>
                    <td>{{ Str::limit($biomasa->user->name ?? 'N/A', 20) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #95a5a6;">
                        <strong>📭 No se encontraron biomasas con los filtros aplicados</strong>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <div class="brand">🛡️ SIPII - Sistema Integral de Prevención de Incendios</div>
            <div class="info">Este reporte contiene {{ $biomasas->count() }} registro(s) de biomasas | Confidencial</div>
        </div>
    </div>
</body>
</html>
