<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Solicitudes</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; }
        .header {
            background: linear-gradient(135deg, #1B263B 0%, #415A77 100%);
            color: white; padding: 10px 15px; text-align: center; margin-bottom: 15px;
        }
        .header h1 { margin: 0; font-size: 16px; font-weight: bold; }
        .header p { margin: 3px 0 0 0; font-size: 9px; opacity: 0.9; }
        .stats-grid { display: table; width: 100%; margin-bottom: 15px; }
        .stat-box {
            display: table-cell; width: 50%; padding: 10px; text-align: center;
            background: white; border: 2px solid #E0E1DD;
        }
        .stat-box .label { font-size: 8px; color: #666; text-transform: uppercase; }
        .stat-box .value { font-size: 14px; font-weight: bold; color: #1B263B; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table thead { background: #1B263B; color: white; }
        table thead th { padding: 6px 4px; text-align: left; font-size: 8px; font-weight: 600; }
        table tbody tr { border-bottom: 1px solid #ddd; }
        table tbody tr:nth-child(even) { background: #f9f9f9; }
        table tbody td { padding: 6px 4px; font-size: 8px; }
        .badge {
            display: inline-block; padding: 2px 6px; border-radius: 3px;
            font-size: 7px; font-weight: 600; text-transform: uppercase;
        }
        .badge-pendiente { background: #ffc107; color: #000; }
        .badge-en-proceso { background: #17a2b8; color: white; }
        .badge-completada { background: #28a745; color: white; }
        .badge-cancelada { background: #dc3545; color: white; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚚 REPORTE DE SOLICITUDES DE RECOLECCIÓN</h1>
        <p>
            @if($request->estado) Estado: {{ $request->estado }} | @endif
            @if($request->fecha_inicio) Desde: {{ $request->fecha_inicio }} @endif
            @if($request->fecha_fin) Hasta: {{ $request->fecha_fin }} @endif
            | Generado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">Total Solicitudes</div>
            <div class="value">{{ $totalSolicitudes }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Estados</div>
            <div class="value">{{ $solicitudesPorEstado->count() }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 6%;">ID</th>
                <th style="width: 18%;">Donante</th>
                <th style="width: 22%;">Dirección</th>
                <th style="width: 14%;">Fecha Prog.</th>
                <th style="width: 13%;">Estado</th>
                <th style="width: 15%;">Recolector</th>
                <th style="width: 12%;">Observ.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($solicitudes as $solicitud)
                <tr>
                    <td>#{{ $solicitud->id_solicitud }}</td>
                    <td>{{ $solicitud->donante->nombre ?? 'N/A' }}</td>
                    <td>{{ $solicitud->direccion_recoleccion }}</td>
                    <td>{{ \Carbon\Carbon::parse($solicitud->fecha_programada)->format('d/m/Y') }}</td>
                    <td>
                        @php
                            $clase = match($solicitud->estado) {
                                'Pendiente' => 'pendiente',
                                'En proceso' => 'en-proceso',
                                'Completada' => 'completada',
                                'Cancelada' => 'cancelada',
                                default => 'pendiente'
                            };
                        @endphp
                        <span class="badge badge-{{ $clase }}">{{ $solicitud->estado }}</span>
                    </td>
                    <td>
                        @if($solicitud->usuario)
                            {{ $solicitud->usuario->nombres }} {{ $solicitud->usuario->apellidos }}
                        @else
                            Sin asignar
                        @endif
                    </td>
                    <td style="font-size: 8px;">{{ Str::limit($solicitud->observaciones ?? '-', 30) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px; color: #999;">
                        No hay solicitudes con los filtros seleccionados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($solicitudesPorEstado->count() > 0)
        <div style="margin-top: 30px; page-break-inside: avoid;">
            <h3 style="color: #1B263B; border-bottom: 2px solid #FFB700; padding-bottom: 8px;">Distribución por Estado</h3>
            <table style="margin-top: 15px;">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitudesPorEstado as $estado => $cantidad)
                        <tr>
                            <td>
                                @php
                                    $clase = match($estado) {
                                        'Pendiente' => 'pendiente',
                                        'En proceso' => 'en-proceso',
                                        'Completada' => 'completada',
                                        'Cancelada' => 'cancelada',
                                        default => 'pendiente'
                                    };
                                @endphp
                                <span class="badge badge-{{ $clase }}">{{ $estado }}</span>
                            </td>
                            <td class="text-center"><strong>{{ $cantidad }}</strong></td>
                            <td class="text-center">{{ round(($cantidad / $totalSolicitudes) * 100, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>





