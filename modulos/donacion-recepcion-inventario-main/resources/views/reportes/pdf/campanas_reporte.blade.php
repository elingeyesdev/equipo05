<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Campañas</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; }
        .header {
            background: linear-gradient(135deg, #1B263B 0%, #415A77 100%);
            color: white; padding: 10px 15px; text-align: center; margin-bottom: 15px;
        }
        .header h1 { margin: 0; font-size: 16px; font-weight: bold; }
        .header p { margin: 3px 0 0 0; font-size: 9px; opacity: 0.9; }
        .stats-grid { display: table; width: 100%; margin-bottom: 10px; }
        .stat-box {
            display: table-cell; width: 50%; padding: 8px; text-align: center;
            background: white; border: 2px solid #E0E1DD;
        }
        .stat-box .label { font-size: 7px; color: #666; text-transform: uppercase; }
        .stat-box .value { font-size: 12px; font-weight: bold; color: #1B263B; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table thead { background: #1B263B; color: white; }
        table thead th { padding: 6px 4px; text-align: left; font-size: 8px; font-weight: 600; }
        table tbody tr { border-bottom: 1px solid #ddd; }
        table tbody tr:nth-child(even) { background: #f9f9f9; }
        table tbody td { padding: 6px 4px; font-size: 8px; }
        .badge {
            display: inline-block; padding: 2px 5px; border-radius: 2px;
            font-size: 6px; font-weight: 600; text-transform: uppercase;
        }
        .badge-activa { background: #28a745; color: white; }
        .badge-proxima { background: #17a2b8; color: white; }
        .badge-finalizada { background: #6c757d; color: white; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .amount { font-weight: bold; color: #28a745; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📢 REPORTE DE CAMPAÑAS</h1>
        <p>Estado: {{ ucfirst($request->estado ?? 'Todas') }} | Generado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">Total Campañas</div>
            <div class="value">{{ count($campanas) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Recaudación Total</div>
            <div class="value" style="color: #28a745;">BOB {{ number_format($montoTotalRecaudado, 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">ID</th>
                <th style="width: 30%;">Nombre</th>
                <th style="width: 15%;">Inicio</th>
                <th style="width: 15%;">Fin</th>
                <th style="width: 15%;">Estado</th>
                <th style="width: 15%;">Recaudado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($campanas as $campana)
                @php
                    $hoy = \Carbon\Carbon::now();
                    $inicio = \Carbon\Carbon::parse($campana->fecha_inicio);
                    $fin = \Carbon\Carbon::parse($campana->fecha_fin);
                    
                    if ($hoy->between($inicio, $fin)) {
                        $estado = 'Activa';
                        $badgeClass = 'badge-activa';
                    } elseif ($hoy < $inicio) {
                        $estado = 'Próxima';
                        $badgeClass = 'badge-proxima';
                    } else {
                        $estado = 'Finalizada';
                        $badgeClass = 'badge-finalizada';
                    }

                    $montoRecaudado = $campana->donaciones
                        ->filter(fn($d) => $d->tipo === 'dinero' && $d->dinero)
                        ->sum(fn($d) => $d->dinero->monto ?? 0);
                @endphp
                <tr>
                    <td>#{{ $campana->id_campana }}</td>
                    <td>{{ $campana->nombre }}</td>
                    <td>{{ $inicio->format('d/m/Y') }}</td>
                    <td>{{ $fin->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ $estado }}</span>
                    </td>
                    <td class="text-right">
                        <span class="amount">BOB {{ number_format($montoRecaudado, 2) }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #999;">
                        No hay campañas registradas
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>




