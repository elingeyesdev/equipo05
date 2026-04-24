<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Inventario</title>
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
        .footer .page-number:after {
            content: counter(page);
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-box {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            background: white;
            border: 2px solid #E0E1DD;
        }
        .stat-box .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-box .value {
            font-size: 18px;
            font-weight: bold;
            color: #1B263B;
            margin-top: 5px;
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
            font-size: 9px;
        }
        .ubicacion {
            font-size: 8px;
            color: #666;
            display: block;
            margin: 2px 0;
        }
        .ubicacion-badge {
            background: #007bff;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            margin-left: 5px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📦 REPORTE DE INVENTARIO</h1>
        <p>Almacén: {{ $almacenId ? \Modules\Inventario\Models\Almacene::find($almacenId)->nombre : 'Todos los almacenes' }}</p>
    </div>

    <div class="footer">
        <div style="float: left;">Generado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
        <div style="float: right;">Página <span class="page-number"></span></div>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">Productos Diferentes</div>
            <div class="value">{{ $totalProductos }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Cantidad Total</div>
            <div class="value" style="color: #28a745;">{{ $cantidadTotal }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Almacenes</div>
            <div class="value">{{ $almacenes->count() }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 25%;">Producto</th>
                <th style="width: 17%;">Categoría</th>
                <th style="width: 12%;" class="text-center">Cantidad</th>
                <th style="width: 38%;">Ubicaciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productosAgrupados as $producto)
                <tr>
                    <td>#{{ $producto->id_producto }}</td>
                    <td><strong>{{ $producto->nombre_producto }}</strong></td>
                    <td>{{ $producto->categoria }}</td>
                    <td class="text-center">
                        <strong style="font-size: 11px; color: #1B263B;">{{ $producto->cantidad_total }}</strong>
                    </td>
                    <td>
                        @foreach($producto->ubicaciones as $ubicacion)
                            <span class="ubicacion">
                                📍 {{ $ubicacion['almacen'] }} / {{ $ubicacion['estante'] }} / {{ $ubicacion['espacio'] }}
                                <span class="ubicacion-badge">{{ $ubicacion['cantidad'] }}</span>
                            </span>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 30px; color: #999;">
                        No hay productos en inventario
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($productosCategoria->count() > 0)
        <div style="margin-top: 30px; page-break-inside: avoid;">
            <h3 style="color: #1B263B; border-bottom: 2px solid #FFB700; padding-bottom: 8px;">Distribución por Categoría</h3>
            <table style="margin-top: 15px;">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th class="text-center">Productos Diferentes</th>
                        <th class="text-right">Cantidad Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productosCategoria as $categoria => $datos)
                        <tr>
                            <td><strong>{{ $categoria }}</strong></td>
                            <td class="text-center">{{ $datos['items'] }}</td>
                            <td class="text-right"><strong>{{ $datos['cantidad'] }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>




