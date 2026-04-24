<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte de Distribución de Paquetes</title>
    <style>
        @page {
            margin: 15mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #1B263B 0%, #415A77 100%);
            color: white;
            padding: 10px 15px;
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .header p {
            margin: 3px 0 0 0;
            font-size: 9px;
            opacity: 0.9;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            border-spacing: 5px;
        }

        .stat-box {
            display: table-cell;
            width: 33.33%;
            padding: 8px;
            text-align: center;
            background: white;
            border: 2px solid #E0E1DD;
        }

        .stat-box .label {
            font-size: 7px;
            color: #666;
            text-transform: uppercase;
        }

        .stat-box .value {
            font-size: 12px;
            font-weight: bold;
            color: #1B263B;
            margin-top: 2px;
        }

        .stat-box.green .value {
            color: #28a745;
        }

        .stat-box.yellow .value {
            color: #ffc107;
        }

        .stat-box.blue .value {
            color: #17a2b8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background: #1B263B;
            color: white;
        }

        table thead th {
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
            font-weight: 600;
        }

        table tbody tr {
            border-bottom: 1px solid #ddd;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        table tbody td {
            padding: 6px 4px;
            font-size: 8px;
        }

        .producto-item {
            font-size: 7px;
            color: #666;
            display: block;
            margin: 1px 0;
        }

        .cantidad-badge {
            background: #007bff;
            color: white;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 6px;
            font-weight: bold;
            margin-left: 3px;
        }

        .text-center {
            text-align: center;
        }

        code {
            background: #FFB700;
            color: #1B263B;
            padding: 2px 4px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>🚚 REPORTE DE DISTRIBUCIÓN DE PAQUETES</h1>
        <p>
            @if($request && $request->fecha_inicio && $request->fecha_fin)
                Período: {{ $request->fecha_inicio }} al {{ $request->fecha_fin }}
            @else
                Todas las distribuciones
            @endif
            | Generado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <div class="stats-grid">
        <div class="stat-box green">
            <div class="label">Total Distribuido</div>
            <div class="value">{{ $totalDistribuido }}</div>
        </div>
        <div class="stat-box yellow">
            <div class="label">Pendiente</div>
            <div class="value">{{ $pendienteDistribucion }}</div>
        </div>
        <div class="stat-box blue">
            <div class="label">Destino + Frecuente</div>
            <div class="value" style="font-size: 9px;">{{ $destinoMasFrecuente }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 14%;">Fecha</th>
                <th style="width: 18%;">Destino</th>
                <th style="width: 14%;">Encargado</th>
                <th style="width: 12%;">Paquete</th>
                <th style="width: 34%;">Productos</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salidasDetalladas as $salida)
                <tr>
                    <td>#{{ $salida['id_salida'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($salida['fecha_salida'])->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $salida['destino'] ?? '-' }}</strong></td>
                    <td>{{ $salida['encargado'] ?? '-' }}</td>
                    <td><code>{{ $salida['codigo_paquete'] }}</code></td>
                    <td>
                        @foreach($salida['productos'] as $producto)
                            <span class="producto-item">
                                📦 {{ $producto['nombre'] }}
                                <span class="cantidad-badge">{{ $producto['cantidad'] }}</span>
                            </span>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #999;">
                        No hay distribuciones registradas en este período
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>



