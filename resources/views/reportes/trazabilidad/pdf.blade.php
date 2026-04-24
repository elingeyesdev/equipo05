<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Trazabilidad</title>
    <style>
        @page { margin: 1cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        .header-container {
            width: 100%;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-title {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
        }
        .header-meta {
            text-align: right;
            font-size: 9px;
            color: #7f8c8d;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 10px;
            margin-bottom: 15px;
        }
        .info-box p { margin: 2px 0; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed;
        }
        th {
            background-color: #2c3e50;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px;
            font-size: 9px;
            border: 1px solid #2c3e50;
        }
        td {
            border: 1px solid #e0e0e0;
            padding: 5px;
            vertical-align: middle;
            font-size: 9px;
            word-wrap: break-word;
        }
        tr:nth-child(even) { background-color: #fcfcfc; }

        .group-header {
            background-color: #dfe6e9;
            color: #2d3436;
            font-weight: bold;
            padding: 8px;
            font-size: 11px;
            margin-top: 15px;
            border: 1px solid #b2bec3;
            border-bottom: none;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .small { font-size: 8px; color: #666; }

        .badge {
            padding: 2px 5px;
            border-radius: 4px;
            color: white;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
        }
        .bg-success { background-color: #27ae60; }
        .bg-warning { background-color: #f39c12; }
        .bg-secondary { background-color: #95a5a6; }

        /* Badges para paquete */
        .bg-pkg { background-color: #8e44ad; } /* morado */
        .bg-pkg2 { background-color: #16a085; } /* verde agua */

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 30px;
            font-size: 8px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 5px;
            text-align: center;
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>

    <div class="footer">
        Sistema de Gestión de Donaciones | Reporte generado automáticamente | Pág. <span class="page-number"></span>
    </div>

    <div class="header-container">
        <table style="border:none; margin:0;">
            <tr style="background:none;">
                <td style="border:none; width: 70%;">
                    <div class="header-title">Reporte de Trazabilidad</div>
                    <div style="color: #7f8c8d; margin-top: 5px;">Control de Inventario y Donaciones</div>
                </td>
                <td style="border:none; text-align: right;">
                    <div class="header-meta">
                        <strong>Fecha:</strong> {{ $fechaGeneracion }}<br>
                        <strong>Usuario:</strong> {{ auth()->user()->name ?? 'Sistema' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="info-box">
        <p><strong>Campaña Visualizada:</strong> {{ $campaniaNombre }}</p>
        <p><strong>Total Ítems Listados:</strong> {{ $items->count() }} registros</p>
        <p class="small"><strong>Nota:</strong> Los campos de Paquete/Gateway se leen desde caché local (ext_paquetes).</p>
    </div>

    @php
        $groupedItems = $items->groupBy(function($item) {
            return $item->almacen_nombre ?: 'Sin Ubicación Física Asignada';
        });
    @endphp

    @forelse($groupedItems as $almacen => $grupoItems)

        <div class="group-header">
            ALMACÉN: {{ strtoupper($almacen) }}
            <span style="float:right; font-weight:normal;">Cant. Ítems: {{ count($grupoItems) }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="7%">Cód.</th>
                    <th width="16%">Producto / Descripción</th>
                    <th width="9%">Categoría</th>
                    <th width="10%">Ubicación</th>
                    <th width="7%">Cant.</th>
                    <th width="12%">Donante</th>
                    <th width="8%">Estado</th>
                    <th width="6%">Ingreso</th>
                    <th width="6%">Salida</th>

                    {{-- ✅ NUEVAS (Gateway / Paquete) --}}
                    <th width="8%">Paquete</th>
                    <th width="7%">Estado PKG</th>
                    <th width="7%">Fecha PKG</th>
                    <th width="7%">Destino</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grupoItems as $item)
                    @php
                        $gw = $item->datos_gateway ?? null;
                        if (is_string($gw)) {
                            $gw = json_decode($gw, true);
                        }

                        $estadoPkg = data_get($gw, 'services.donaciones.paquete.estado');
                        $fechaPkg  = data_get($gw, 'services.donaciones.paquete.fecha_creacion');
                        $destino   = data_get($gw, 'services.donaciones.registros_salida.0.destino');

                        // badge de item
                        $badgeClass = 'bg-secondary';
                        if(\Illuminate\Support\Str::contains(strtolower($item->estado_actual), ['almacen', 'stock'])) $badgeClass = 'bg-warning';
                        if(\Illuminate\Support\Str::contains(strtolower($item->estado_actual), ['entregado', 'salida'])) $badgeClass = 'bg-success';

                        // badge de pkg (solo para que se vea bonito)
                        $pkgBadge = $item->codigo_paquete ? 'bg-pkg' : 'bg-secondary';
                    @endphp

                    <tr>
                        <td class="text-center text-bold">{{ $item->codigo_unico }}</td>
                        <td>
                            {{ $item->nombre_producto }}
                            @if($item->talla)
                                <div class="small">Talla: {{ $item->talla }} | {{ $item->genero }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ \Illuminate\Support\Str::limit($item->categoria_producto, 15) }}</td>
                        <td>
                            @if($item->estante_codigo)
                                <div style="font-weight:bold; color: #2c3e50;">Est: {{ $item->estante_codigo }}</div>
                                <div class="small">Esp: {{ $item->espacio_codigo }}</div>
                            @else
                                <span style="color:#999;">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $item->cantidad_donada }} <br>
                            <span class="small">{{ strtolower($item->unidad_empaque) }}</span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($item->nombre_donante, 20) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $badgeClass }}">{{ $item->estado_actual }}</span>
                        </td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->fecha_donacion)->format('d/m/y') }}</td>
                        <td class="text-center">
                            @if($item->fecha_salida)
                                {{ \Carbon\Carbon::parse($item->fecha_salida)->format('d/m/y') }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- ✅ PAQUETE / GATEWAY (caché local) --}}
                        <td class="text-center">
                            @if($item->codigo_paquete)
                                <span class="badge {{ $pkgBadge }}">{{ $item->codigo_paquete }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $estadoPkg ?? '-' }}
                        </td>
                        <td class="text-center">
                            @if($fechaPkg)
                                {{ \Carbon\Carbon::parse($fechaPkg)->format('d/m/y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $destino ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <div style="text-align: center; padding: 40px; color: #777; border: 1px dashed #ccc;">
            No se encontraron registros para los filtros seleccionados.
        </div>
    @endforelse

</body>
</html>
