<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Simulación - {{ $simulacion->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #16a34a;
        }

        .header h1 {
            color: #16a34a;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            color: #64748b;
            font-size: 1.1em;
        }

        .info-section {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #16a34a;
        }

        .info-section h2 {
            color: #1e293b;
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .info-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .info-item label {
            display: block;
            font-weight: 600;
            color: #475569;
            margin-bottom: 5px;
            font-size: 0.9em;
        }

        .info-item span {
            display: block;
            color: #1e293b;
            font-size: 1.1em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        thead {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
        }

        thead th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 1em;
        }

        tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.2s;
        }

        tbody tr:hover {
            background: #f0fdf4;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody td {
            padding: 12px 15px;
        }

        .result-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9em;
        }

        .result-high {
            background: #fee2e2;
            color: #991b1b;
        }

        .result-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .result-low {
            background: #dcfce7;
            color: #166534;
        }

        .print-button {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.1em;
            border-radius: 8px;
            cursor: pointer;
            display: block;
            margin: 30px auto;
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .print-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.4);
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            color: #64748b;
            font-size: 0.9em;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .container {
                max-width: 100%;
                padding: 20px;
                box-shadow: none;
            }

            .print-button {
                display: none;
            }

            table {
                page-break-inside: avoid;
            }

            .info-section {
                page-break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }

            tbody tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 INFORME DE SIMULACIÓN</h1>
            <p>Sistema Integrado de Prevención de Incendios Forestales - SIPII</p>
        </div>

        <div class="info-section">
            <h2>ℹ️ Información General</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>ID Simulación:</label>
                    <span>#{{ $simulacion->id }}</span>
                </div>
                <div class="info-item">
                    <label>Fecha de Creación:</label>
                    <span>{{ \Carbon\Carbon::parse($simulacion->created_at)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <label>Usuario:</label>
                    <span>{{ $simulacion->user->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <label>Estado:</label>
                    <span>{{ ucfirst($simulacion->estado ?? 'completada') }}</span>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h2>📊 Parámetros de Entrada</h2>
            <table>
                <thead>
                    <tr>
                        <th>Parámetro</th>
                        <th>Valor</th>
                        <th>Unidad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Latitud Centro</strong></td>
                        <td>{{ number_format($simulacion->map_center_lat ?? 0, 6) }}</td>
                        <td>°</td>
                    </tr>
                    <tr>
                        <td><strong>Longitud Centro</strong></td>
                        <td>{{ number_format($simulacion->map_center_lng ?? 0, 6) }}</td>
                        <td>°</td>
                    </tr>
                    <tr>
                        <td><strong>Temperatura</strong></td>
                        <td>{{ number_format($simulacion->temperature ?? 0, 2) }}</td>
                        <td>°C</td>
                    </tr>
                    <tr>
                        <td><strong>Velocidad del Viento</strong></td>
                        <td>{{ number_format($simulacion->wind_speed ?? 0, 2) }}</td>
                        <td>km/h</td>
                    </tr>
                    <tr>
                        <td><strong>Dirección del Viento</strong></td>
                        <td>{{ number_format($simulacion->wind_direction ?? 0, 0) }}</td>
                        <td>°</td>
                    </tr>
                    <tr>
                        <td><strong>Humedad</strong></td>
                        <td>{{ number_format($simulacion->humidity ?? 0, 2) }}</td>
                        <td>%</td>
                    </tr>
                    <tr>
                        <td><strong>Velocidad de Simulación</strong></td>
                        <td>{{ number_format($simulacion->simulation_speed ?? 1, 1) }}</td>
                        <td>x</td>
                    </tr>
                    @if($simulacion->duracion)
                    <tr>
                        <td><strong>Duración</strong></td>
                        <td>{{ $simulacion->duracion }}</td>
                        <td>minutos</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Focos Activos</strong></td>
                        <td>{{ $simulacion->focos_activos ?? 0 }}</td>
                        <td>focos</td>
                    </tr>
                    <tr>
                        <td><strong>Voluntarios Enviados</strong></td>
                        <td>{{ $simulacion->num_voluntarios_enviados ?? 0 }}</td>
                        <td>personas</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($simulacion->fire_risk || $simulacion->mitigation_strategies)
        <div class="info-section">
            <h2>📈 Resultados de la Simulación</h2>
            <table>
                <thead>
                    <tr>
                        <th>Métrica</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @if($simulacion->fire_risk)
                    <tr>
                        <td><strong>Índice de Riesgo de Incendio</strong></td>
                        <td>
                            <span class="result-badge result-{{ $simulacion->fire_risk > 70 ? 'high' : ($simulacion->fire_risk > 40 ? 'medium' : 'low') }}">
                                {{ $simulacion->fire_risk }}%
                            </span>
                        </td>
                    </tr>
                    @endif
                    @if($simulacion->mitigation_strategies)
                        @php
                            $strategies = is_string($simulacion->mitigation_strategies) 
                                ? json_decode($simulacion->mitigation_strategies, true) 
                                : $simulacion->mitigation_strategies;
                        @endphp
                        @if(is_array($strategies))
                            <tr>
                                <td><strong>Estrategias de Mitigación</strong></td>
                                <td>{{ count($strategies) }} estrategias aplicadas</td>
                            </tr>
                        @endif
                    @endif
                    @if($simulacion->auto_stopped)
                    <tr>
                        <td><strong>Estado de Finalización</strong></td>
                        <td>
                            <span class="result-badge result-low">
                                Detenida automáticamente
                            </span>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @endif

        <button class="print-button" onclick="window.print()">
            🖨️ Imprimir o Guardar como PDF
        </button>

        <div class="footer">
            <p><strong>Sistema SIPII</strong> - Sistema Integrado de Prevención de Incendios Forestales</p>
            <p>Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
            <p>Ubicación: Ibex, Bolivia</p>
        </div>
    </div>
</body>
</html>
