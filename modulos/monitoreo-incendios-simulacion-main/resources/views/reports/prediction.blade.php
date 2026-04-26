<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Predicción - {{ $prediction->id }}</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            color: #2563eb;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            color: #64748b;
            font-size: 1.1em;
        }

        .info-section {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #2563eb;
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
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
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
            background: #f8fafc;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody td {
            padding: 12px 15px;
        }

        .variation {
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .variation.positive {
            background: #dcfce7;
            color: #166534;
        }

        .variation.negative {
            background: #fee2e2;
            color: #991b1b;
        }

        .variation.neutral {
            background: #f1f5f9;
            color: #475569;
        }

        .print-button {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.1em;
            border-radius: 8px;
            cursor: pointer;
            display: block;
            margin: 30px auto;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .print-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
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
            <h1>📊 INFORME DE PREDICCIÓN</h1>
            <p>Sistema Integrado de Prevención de Incendios Forestales - SIPII</p>
        </div>

        <div class="info-section">
            <h2>📍 Información del Foco</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>ID Predicción:</label>
                    <span>#{{ $prediction->id }}</span>
                </div>
                <div class="info-item">
                    <label>Fecha de Creación:</label>
                    <span>{{ \Carbon\Carbon::parse($prediction->created_at)->format('d/m/Y H:i') }}</span>
                </div>
                @php
                    $meta = $prediction->meta ?? [];
                    $inputParams = $meta['input_parameters'] ?? [];
                @endphp
                <div class="info-item">
                    <label>Latitud Inicial:</label>
                    <span>{{ number_format($inputParams['initial_lat'] ?? 0, 6) }}°</span>
                </div>
                <div class="info-item">
                    <label>Longitud Inicial:</label>
                    <span>{{ number_format($inputParams['initial_lng'] ?? 0, 6) }}°</span>
                </div>
                @if($prediction->foco_incendio_id)
                <div class="info-item">
                    <label>Foco Asociado:</label>
                    <span>{{ $prediction->focoIncendio->ubicacion ?? "Foco #{$prediction->foco_incendio_id}" }}</span>
                </div>
                @endif
                <div class="info-item">
                    <label>Duración Total:</label>
                    <span>{{ $inputParams['prediction_hours'] ?? 'N/A' }} horas</span>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h2>🔥 Parámetros de Predicción</h2>
            @php
                $meta = $prediction->meta ?? [];
                $inputParams = $meta['input_parameters'] ?? [];
                $finalConditions = $meta['final_conditions'] ?? [];
            @endphp
            
            @if(!empty($inputParams))
                <table>
                    <thead>
                        <tr>
                            <th>Parámetro</th>
                            <th>Valor Inicial</th>
                            <th>Valor Final</th>
                            <th>Variación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $params = [
                                'Temperatura' => ['temperature', '°C'],
                                'Velocidad del Viento' => ['wind_speed', 'km/h'],
                                'Dirección del Viento' => ['wind_direction', '°'],
                                'Humedad' => ['humidity', '%'],
                                'Tipo de Terreno' => ['terrain_type', ''],
                            ];
                        @endphp

                        @foreach($params as $label => $data)
                            @php
                                [$key, $unit] = $data;
                                $initial = $inputParams[$key] ?? 0;
                                $final = $finalConditions[$key] ?? $initial;
                                
                                // Para terrain_type no calculamos variación numérica
                                if ($key === 'terrain_type') {
                                    $variation = $initial === $final ? 'Sin cambios' : 'Cambió';
                                    $varClass = 'neutral';
                                } else {
                                    $variation = is_numeric($final) && is_numeric($initial) ? $final - $initial : 0;
                                    $variationPercent = $initial != 0 ? ($variation / $initial) * 100 : 0;
                                    
                                    if ($variation > 0) {
                                        $varClass = 'positive';
                                        $varSign = '+';
                                    } elseif ($variation < 0) {
                                        $varClass = 'negative';
                                        $varSign = '';
                                    } else {
                                        $varClass = 'neutral';
                                        $varSign = '';
                                    }
                                }
                            @endphp
                            <tr>
                                <td><strong>{{ $label }}</strong></td>
                                <td>
                                    @if(is_numeric($initial))
                                        {{ number_format($initial, 2) }} {{ $unit }}
                                    @else
                                        {{ ucfirst($initial) }}
                                    @endif
                                </td>
                                <td>
                                    @if(is_numeric($final))
                                        {{ number_format($final, 2) }} {{ $unit }}
                                    @else
                                        {{ ucfirst($final) }}
                                    @endif
                                </td>
                                <td>
                                    @if($key === 'terrain_type')
                                        <span class="variation {{ $varClass }}">{{ $variation }}</span>
                                    @else
                                        <span class="variation {{ $varClass }}">
                                            {{ $varSign ?? '' }}{{ number_format($variation, 2) }} {{ $unit }}
                                            @if(isset($variationPercent))
                                                ({{ $varSign ?? '' }}{{ number_format($variationPercent, 1) }}%)
                                            @endif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color: #64748b; font-style: italic;">No hay datos de parámetros disponibles</p>
            @endif
        </div>

        @if($prediction->path)
        <div class="info-section">
            <h2>🗺️ Trayectoria de Propagación</h2>
            <p style="margin-bottom: 15px; color: #64748b;">
                Primeros 20 puntos de la trayectoria predicha del incendio
            </p>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Hora</th>
                        <th>Latitud</th>
                        <th>Longitud</th>
                        <th>Intensidad</th>
                        <th>Área Afectada</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $path = is_string($prediction->path) ? json_decode($prediction->path, true) : $prediction->path;
                        $path = is_array($path) ? $path : [];
                        $points = array_slice($path, 0, 20);
                    @endphp

                    @if(count($points) > 0)
                        @foreach($points as $index => $point)
                            @if(is_array($point) && isset($point['lat']) && isset($point['lng']))
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>Hora {{ $point['hour'] ?? $index }}</td>
                                    <td>{{ number_format($point['lat'], 6) }}°</td>
                                    <td>{{ number_format($point['lng'], 6) }}°</td>
                                    <td>{{ $point['intensity'] ?? 'N/A' }}</td>
                                    <td>{{ isset($point['affected_area_km2']) ? number_format($point['affected_area_km2'], 2) . ' km²' : 'N/A' }}</td>
                                </tr>
                            @endif
                        @endforeach

                        @if(count($path) > 20)
                            <tr>
                                <td colspan="6" style="text-align: center; color: #64748b; font-style: italic;">
                                    ... y {{ count($path) - 20 }} puntos más en la trayectoria completa
                                </td>
                            </tr>
                        @endif
                    @else
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; font-style: italic;">
                                No hay datos de trayectoria disponibles
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
