<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Donaciones</title>
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
        .info-section {
            background: #F8F9FA;
            border-left: 3px solid #FFB700;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 0 3px 3px 0;
        }
        .info-section h3 {
            margin: 0 0 5px 0;
            color: #1B263B;
            font-size: 10px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .stat-box {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            text-align: center;
            background: white;
            border: 2px solid #E0E1DD;
            margin: 0 3px;
        }
        .stat-box .label {
            font-size: 7px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-box .value {
            font-size: 12px;
            font-weight: bold;
            color: #1B263B;
            margin-top: 3px;
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
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 2px;
            font-size: 6px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-dinero { background: #28a745; color: white; }
        .badge-especie { background: #007bff; color: white; }
        .badge-ropa { background: #17a2b8; color: white; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .amount {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 REPORTE DE DONACIONES</h1>
        <p>Período: {{ $request->fecha_inicio }} al {{ $request->fecha_fin }} | Generado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info-section">
        <h3>Resumen Ejecutivo</h3>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="label">Total Donaciones</div>
                <div class="value">{{ $totalDonaciones }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Monto Recaudado</div>
                <div class="value" style="color: #28a745;">Bs. {{ number_format($totalMonto, 2) }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Tipos Donación</div>
                <div class="value">{{ $donacionesPorTipo->count() }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 15%;">Fecha</th>
                <th style="width: 22%;">Donante</th>
                <th style="width: 22%;">Campaña</th>
                <th style="width: 13%;">Tipo</th>
                <th style="width: 20%;" class="text-right">Monto/Items</th>
            </tr>
        </thead>
        <tbody>
            @forelse($donaciones as $donacion)
                <tr>
                    <td>#{{ $donacion->id_donacion }}</td>
                    <td>{{ \Carbon\Carbon::parse($donacion->fecha)->format('d/m/Y H:i') }}</td>
                    <td>{{ $donacion->donante->nombre ?? 'Anónimo' }}</td>
                    <td>{{ $donacion->campana->nombre ?? 'General' }}</td>
                    <td>
                        <span class="badge badge-{{ $donacion->tipo }}">{{ ucfirst($donacion->tipo) }}</span>
                    </td>
                    <td class="text-right">
                        @if($donacion->tipo === 'dinero' && $donacion->dinero)
                            <span class="amount">Bs. {{ number_format($donacion->dinero->monto, 2) }}</span>
                        @else
                            {{ $donacion->detalles->count() }} items
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 30px; color: #999;">
                        No hay donaciones registradas en este período
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($donacionesPorTipo->count() > 0)
        <div style="margin-top: 30px; page-break-inside: avoid;">
            <h3 style="color: #1B263B; border-bottom: 2px solid #FFB700; padding-bottom: 8px;">Distribución por Tipo</h3>
            <table style="margin-top: 15px;">
                <thead>
                    <tr>
                        <th>Tipo de Donación</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-right">Monto</th>
                        <th class="text-center">Items</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donacionesPorTipo as $tipo => $datos)
                        <tr>
                            <td><span class="badge badge-{{ $tipo }}">{{ ucfirst($tipo) }}</span></td>
                            <td class="text-center"><strong>{{ $datos['cantidad'] }}</strong></td>
                            <td class="text-right">
                                @if($datos['monto'] > 0)
                                    <span class="amount">Bs. {{ number_format($datos['monto'], 2) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">{{ $datos['items'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>




