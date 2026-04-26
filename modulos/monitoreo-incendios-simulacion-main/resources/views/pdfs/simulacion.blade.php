<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Simulación - SIPII</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 0; padding: 20px; font-size: 12px; }
        .header { text-align: center; border-bottom: 3px solid #16a34a; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #15803d; margin: 0; font-size: 24px; }
        .header h2 { color: #64748b; margin: 10px 0 0 0; font-size: 16px; font-weight: normal; }
        .section { margin-bottom: 25px; }
        .section-title { background-color: #dcfce7; color: #15803d; padding: 10px; font-size: 14px; font-weight: bold; border-left: 4px solid #16a34a; margin-bottom: 15px; }
        .info-row { padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .info-label { display: inline-block; width: 180px; font-weight: bold; color: #374151; }
        .info-value { color: #1f2937; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th { background-color: #e5e7eb; padding: 10px; text-align: left; font-weight: bold; color: #374151; border: 1px solid #d1d5db; }
        table td { padding: 8px 10px; border: 1px solid #e5e7eb; color: #1f2937; }
        table tr:nth-child(even) { background-color: #f9fafb; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SIPII - Sistema de Simulación de Incendios</h1>
        <h2>Informe de Simulación</h2>
    </div>

    <div class="section">
        <div class="section-title">Información de la Simulación</div>
        <div class="info-row">
            <span class="info-label">ID de Simulación:</span>
            <span class="info-value">{{ $simulacion->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Creación:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($simulacion->created_at)->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Generación del Informe:</span>
            <span class="info-value">{{ now()->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>

    @php
        $resultado = is_string($simulacion->resultado) ? json_decode($simulacion->resultado, true) : $simulacion->resultado;
    @endphp

    @if($resultado)
    <div class="section">
        <div class="section-title">Parámetros de Entrada</div>
        <table>
            <thead>
                <tr>
                    <th>Parámetro</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($resultado['input']))
                    @foreach($resultado['input'] as $key => $value)
                    <tr>
                        <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Resultados de la Simulación</div>
        <table>
            <thead>
                <tr>
                    <th>Métrica</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($resultado['summary']))
                    @foreach($resultado['summary'] as $key => $value)
                    <tr>
                        <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                        <td>{{ is_numeric($value) ? number_format($value, 2) : $value }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    @if(isset($resultado['path']) && is_array($resultado['path']))
    <div class="section">
        <div class="section-title">Trayectoria de Propagación (Primeros 20 puntos)</div>
        <table>
            <thead>
                <tr>
                    <th>Punto</th>
                    <th>Latitud</th>
                    <th>Longitud</th>
                    <th>Intensidad</th>
                    <th>Área (km²)</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($resultado['path'], 0, 20) as $index => $point)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ number_format($point['lat'] ?? 0, 4) }}</td>
                    <td>{{ number_format($point['lng'] ?? 0, 4) }}</td>
                    <td>{{ number_format($point['intensity'] ?? 0, 2) }}</td>
                    <td>{{ number_format($point['area'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($resultado['path']) > 20)
        <p style="margin-top: 10px; font-style: italic; color: #64748b;">
            + {{ count($resultado['path']) - 20 }} puntos adicionales
        </p>
        @endif
    </div>
    @endif
    @endif

    <div class="footer">
        <p><strong>SIPII - Sistema Integrado de Predicción de Incendios</strong></p>
        <p>Este informe fue generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>San José de Chiquitos, Bolivia</p>
    </div>
</body>
</html>
