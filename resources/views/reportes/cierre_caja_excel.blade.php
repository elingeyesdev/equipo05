<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="7" style="font-size: 18px; font-weight: bold; text-align: center; background-color: #000000; color: #ffffff; height: 40px; vertical-align: middle;">
                    REPORTE DE CAJA POR CAMPAÑAS
                </th>
            </tr>
            <tr>
                <th colspan="7" style="text-align: center; font-size: 11px; color: #555555;">
                    Generado: {{ date('d/m/Y H:i') }} | Usuario: {{ auth()->user()->name ?? 'Sistema' }}
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach($grupos as $nombreCampania => $listaDonaciones)

                {{-- ✅ SEPARADOR (en columna A para que tu AfterSheet lo detecte sí o sí) --}}
                <tr>
                    <td>[[SEPARADOR]]</td>
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>

                {{-- Banda de campaña --}}
                <tr>
                    <td colspan="7" style="background-color: #002060; color: #ffffff; font-weight: bold; font-size: 14px; border: 2px solid #000000; vertical-align: middle;">
                        CAMPAÑA: {{ mb_strtoupper($nombreCampania) }}
                    </td>
                </tr>

                {{-- Encabezados (✅ SIN width en px) --}}
                <tr>
                    <th style="background-color: #D9D9D9; border: 1px solid #000000; font-weight: bold; text-align: center;">ID</th>
                    <th style="background-color: #D9D9D9; border: 1px solid #000000; font-weight: bold; text-align: center;">Fecha</th>
                    <th style="background-color: #D9D9D9; border: 1px solid #000000; font-weight: bold; text-align: left;">Donante</th>
                    <th style="background-color: #D9D9D9; border: 1px solid #000000; font-weight: bold; text-align: center;">Tipo</th>
                    <th style="background-color: #D9D9D9; border: 1px solid #000000; font-weight: bold; text-align: center;">Estado</th>
                    <th style="background-color: #D9D9D9; border: 1px solid #000000; font-weight: bold; text-align: center;">Priv.</th>
                    <th style="background-color: #D9D9D9; border: 1px solid #000000; font-weight: bold; text-align: right;">Monto (Bs)</th>
                </tr>

                @foreach($listaDonaciones as $d)
                    <tr>
                        <td style="border: 1px solid #bfbfbf; text-align: center;">
                            {{ $d->donacionid }}
                        </td>

                        <td style="border: 1px solid #bfbfbf; text-align: center;">
                            {{ \Carbon\Carbon::parse($d->fechadonacion)->format('d/m/Y') }}
                        </td>

                        <td style="border: 1px solid #bfbfbf;">
                            {{ $d->esanonima ? 'ANÓNIMO' : (optional($d->usuario)->nombre . ' ' . optional($d->usuario)->apellido) }}
                        </td>

                        <td style="border: 1px solid #bfbfbf; text-align: center;">
                            {{ ucfirst($d->tipodonacion) }}
                        </td>

                        <td style="border: 1px solid #bfbfbf; text-align: center; font-weight: bold; color: {{ $d->estadoid == 2 ? '#006100' : '#9C5700' }};">
                            {{ optional($d->estado)->nombre }}
                        </td>

                        <td style="border: 1px solid #bfbfbf; text-align: center;">
                            {{ $d->esanonima ? 'Sí' : 'No' }}
                        </td>

                        <td style="border: 1px solid #bfbfbf; text-align: right; font-weight: bold; color: #0000FF;">
                            {{ number_format($d->monto, 2, '.', '') }}
                        </td>
                    </tr>

                    {{-- Detalle de asignaciones --}}
                    @if($d->asignacionesPivot->count() > 0)
                        @foreach($d->asignacionesPivot as $pivot)
                            <tr>
                                <td style="background-color: #FFFFFF; border-right: 1px solid #bfbfbf;"></td>

                                <td colspan="5" style="background-color: #E2EFDA; color: #375623; border: 1px dashed #375623; font-style: italic;">
                                    ↳ Asignado a: (ID {{ $pivot->asignacionid }}) {{ $pivot->asignacion->descripcion }}
                                </td>

                                <td style="background-color: #E2EFDA; color: #375623; border: 1px dashed #375623; text-align: right;">
                                    - {{ number_format($pivot->montoasignado, 2, '.', '') }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach

                {{-- Total campaña --}}
                <tr>
                    <td colspan="6" style="background-color: #BDD7EE; border: 2px solid #000000; font-weight: bold; text-align: right;">
                        TOTAL {{ strtoupper($nombreCampania) }}:
                    </td>
                    <td style="background-color: #BDD7EE; border: 2px solid #000000; font-weight: bold; text-align: right; color: #000000;">
                        {{ number_format($listaDonaciones->sum('monto'), 2, '.', '') }}
                    </td>
                </tr>

            @endforeach

            {{-- ✅ Separador final --}}
            <tr>
                <td>[[SEPARADOR]]</td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>

            {{-- Total general --}}
            <tr>
                <td colspan="6" style="background-color: #FFC000; border: 2px solid #000000; font-size: 14px; font-weight: bold; text-align: right; height: 35px; vertical-align: middle;">
                    TOTAL GENERAL RECAUDADO:
                </td>
                <td style="background-color: #FFC000; border: 2px solid #000000; font-size: 14px; font-weight: bold; text-align: right; vertical-align: middle;">
                    {{ number_format($totalGeneral, 2, '.', '') }}
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
