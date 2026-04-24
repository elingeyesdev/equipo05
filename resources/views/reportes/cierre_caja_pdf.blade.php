<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte por Campañas</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; }
        
        /* Encabezado General */
        .header { width: 100%; border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .info-filtros { font-size: 9px; color: #666; margin-bottom: 15px; background: #f4f4f4; padding: 5px; }

        /* ESTILO DE CAMPAÑA (El Bloque Principal) */
        .bloque-campania {
            margin-bottom: 30px;
            page-break-inside: avoid; /* Intenta no cortar campañas a la mitad */
        }
        
        .titulo-campania {
            background-color: #2c3e50; /* Azul Oscuro */
            color: white;
            padding: 8px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 3px solid #1a252f;
        }

        /* Tabla de Donaciones */
        .tabla-donaciones { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .tabla-donaciones th { background-color: #ecf0f1; border: 1px solid #bdc3c7; padding: 5px; text-align: left; }
        .tabla-donaciones td { border: 1px solid #bdc3c7; padding: 5px; vertical-align: top; }
        
        /* Detalles de Asignación (Anidado) */
        .box-asignacion {
            background-color: #f9fff9;
            border: 1px dashed #28a745;
            padding: 5px;
            margin-top: 5px;
            font-size: 9px;
        }
        .titulo-asignacion { color: #28a745; font-weight: bold; display: block; margin-bottom: 3px; }
        
        /* Totales */
        .total-campania {
            background-color: #bdc3c7;
            text-align: right;
            padding: 5px;
            font-weight: bold;
            border: 1px solid #95a5a6;
            margin-top: -1px; /* Pegado a la tabla */
        }

        .badges {
            color: #fff; padding: 2px 4px; border-radius: 3px; font-size: 8px; font-weight: bold; display: inline-block;
        }
    </style>
</head>
<body>

    <div class="header">
        <table width="100%">
            <tr>
                <td>
                    <h1>Reporte Detallado por Campaña</h1>
                    <small>Sistema de Donaciones</small>
                </td>
                <td style="text-align: right; font-size: 9px;">
                    Fecha: {{ date('d/m/Y H:i') }}<br>
                    Usuario: {{ auth()->user()->name ?? 'Sistema' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="info-filtros">
        <strong>Filtros:</strong> 
        Campaña: {{ $filtrosAplicados['campania'] }} | 
        Fecha: {{ $filtrosAplicados['desde'] ?? 'Inicio' }} - {{ $filtrosAplicados['hasta'] ?? 'Fin' }} |
        Donante: {{ $filtrosAplicados['donante'] ?: 'Todos' }}
    </div>

    {{-- LÓGICA DE AGRUPACIÓN POR CAMPAÑA --}}
    @php
        $grupos = $donaciones->groupBy(function($item) {
            return $item->campania ? $item->campania->titulo : 'Donaciones Generales (Sin Campaña)';
        });
    @endphp

    @foreach($grupos as $nombreCampania => $listaDonaciones)
        
        <div class="bloque-campania">
            <div class="titulo-campania">
                 CAMPAÑA: {{ $nombreCampania }}
            </div>

            <table class="tabla-donaciones">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="30%">Donante</th>
                        <th width="15%">Tipo / Fecha</th>
                        <th width="15%" style="text-align: center">Estado</th>
                        <th width="30%" style="text-align: right">Monto / Asignación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listaDonaciones as $d)
                    <tr>
                        <td>#{{ $d->donacionid }}</td>
                        <td>
                            @if($d->esanonima)
                                <em>Anónimo</em>
                            @else
                                {{ optional($d->usuario)->nombre }} {{ optional($d->usuario)->apellido }}
                            @endif
                        </td>
                        <td>
                            {{ ucfirst($d->tipodonacion) }}<br>
                            <small>{{ \Carbon\Carbon::parse($d->fechadonacion)->format('d/m/Y') }}</small>
                        </td>
                        <td style="text-align: center">
                            <span class="badges" style="background-color: {{ $d->estadoid == 2 ? '#27ae60' : ($d->estadoid == 1 ? '#f39c12' : '#7f8c8d') }}">
                                {{ optional($d->estado)->nombre }}
                            </span>
                        </td>
                        <td style="text-align: right">
                            <div style="font-size: 11px; font-weight: bold; color: #2980b9;">
                                Bs {{ number_format($d->monto, 2) }}
                            </div>

                            @if($d->asignacionesPivot->count() > 0)
                                @foreach($d->asignacionesPivot as $pivot)
                                    <div class="box-asignacion" style="text-align: left;">
                                        <span class="titulo-asignacion">⬇ Asignado a:</span>
                                        ID #{{ $pivot->asignacionid }} - {{ Str::limit($pivot->asignacion->descripcion ?? '', 30) }}
                                        <div style="text-align: right; font-weight: bold; margin-top: 2px;">
                                            - Bs {{ number_format($pivot->montoasignado, 2) }}
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="total-campania">
                TOTAL {{ strtoupper($nombreCampania) }}: Bs {{ number_format($listaDonaciones->sum('monto'), 2) }}
            </div>
        </div>

    @endforeach

    <div style="margin-top: 30px; border-top: 2px solid #000; padding-top: 10px; text-align: right; font-size: 14px; font-weight: bold;">
        TOTAL GENERAL DEL REPORTE: Bs {{ number_format($totalGeneral, 2) }}
    </div>

</body>
</html>