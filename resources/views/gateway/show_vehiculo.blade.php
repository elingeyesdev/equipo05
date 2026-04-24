@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route('gateway.trazabilidad.index') }}" class="text-decoration-none">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>

            <h1 class="h4 mb-0 mt-2">
                Vehículo <span class="text-success">{{ $vehiculo->identificador }}</span>
            </h1>
            <small class="text-muted">Detalle desde Gateway externo (cache local).</small>
        </div>

        <span class="badge badge-secondary px-3 py-2">
            FUENTE: GATEWAY EXTERNO
        </span>
    </div>

    @php
        $raw = $vehiculo->response_detalle ?? [];

        // Estructura típica: services -> logistica
        $logistica = $raw['services']['logistica'] ?? $raw['services']['LOGISTICA'] ?? [];

        // Datos del vehículo (a veces viene como "vehiculo")
        $vehiculoInfo = $logistica['vehiculo'] ?? [];

        $placa = $logistica['placa'] ?? $raw['placa'] ?? $vehiculo->identificador;
        $marca = data_get($vehiculoInfo, 'marca_vehiculo.nombre_marca', $vehiculoInfo['marca'] ?? 'N/D');
        $tipo  = data_get($vehiculoInfo, 'tipo_vehiculo.nombre_tipo_vehiculo', $vehiculoInfo['tipo'] ?? 'N/D');
        $modelo = $vehiculoInfo['modelo'] ?? null;
        $anio = $vehiculoInfo['modelo_anio'] ?? null;
        $color = $vehiculoInfo['color'] ?? null;

        // Paquetes asociados (según tu JSON puede venir como "paquetes" o dentro de logistica)
        $paquetes = $logistica['paquetes'] ?? [];

        // Historial (según el JSON que pegaste: logistica.historial[])
        $historial = $logistica['historial'] ?? [];
    @endphp

    {{-- SI NO HAY DATA --}}
    @if(empty($raw))
        <div class="alert alert-light border shadow-sm">
            <i class="fas fa-info-circle mr-2"></i>
            No hay información adicional disponible en este momento.
        </div>
    @else

        {{-- RESUMEN VEHÍCULO --}}
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-4">
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-light"
                                 style="width:84px;height:84px;border-radius: 22px;">
                                <i class="fas fa-truck fa-2x text-secondary"></i>
                            </div>
                        </div>

                        <h2 class="mb-1">{{ $placa }}</h2>
                        <div class="text-muted">Placa del vehículo</div>

                        <div class="mt-3">
                            <span class="badge badge-primary mr-1">{{ $marca }}</span>
                            <span class="badge badge-info">{{ $tipo }}</span>
                        </div>

                        <hr class="my-4">

                        <div class="text-left small">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Modelo</span>
                                <span class="font-weight-bold">{{ $modelo ?? 'N/D' }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Año</span>
                                <span class="font-weight-bold">{{ $anio ?? 'N/D' }}</span>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Color</span>
                                <span class="font-weight-bold">{{ $color ?? 'N/D' }}</span>
                            </div>
                        </div>

                        <div class="mt-4 text-muted small">
                            Última sincronización: {{ $vehiculo->updated_at?->diffForHumans() }}
                        </div>
                    </div>
                </div>

                {{-- JSON CRUDO (COLAPSABLE) --}}
                <div class="card shadow-sm border-0 mt-3">
                    <div class="card-body">
                        <button class="btn btn-outline-secondary btn-sm w-100"
                                type="button"
                                data-toggle="collapse"
                                data-target="#jsonRaw"
                                aria-expanded="false"
                                aria-controls="jsonRaw">
                            <i class="fas fa-code mr-1"></i> Ver JSON completo
                        </button>

                        <div class="collapse mt-3" id="jsonRaw">
                            <pre class="bg-light p-3 rounded border small mb-0"
                                 style="max-height: 320px; overflow:auto;">{{ json_encode($raw, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                </div>

            </div>

            {{-- PAQUETES + HISTORIAL --}}
            <div class="col-lg-8">

                {{-- PAQUETES ASOCIADOS --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-box mr-1 text-primary"></i> Paquetes asociados
                            </h3>
                            <span class="badge badge-light">{{ is_array($paquetes) ? count($paquetes) : 0 }}</span>
                        </div>
                    </div>

                    <div class="card-body">
                        @if(empty($paquetes))
                            <div class="text-muted">No hay paquetes asociados a este vehículo.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Estado actual</th>
                                            <th>Comunidad / Provincia</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paquetes as $p)
                                            @php
                                                $codigo = $p['codigo'] ?? $p['codigo_paquete'] ?? null;
                                                $estado = $p['estado_actual'] ?? $p['estado'] ?? 'N/D';
                                                $comunidad = data_get($p, 'solicitud.comunidad', '');
                                                $provincia = data_get($p, 'solicitud.provincia', '');

                                                $badge = 'badge-secondary';
                                                if (strtolower($estado) === 'en camino') $badge = 'badge-info';
                                                if (strtolower($estado) === 'armado') $badge = 'badge-warning';
                                                if (strtolower($estado) === 'entregado') $badge = 'badge-success';
                                            @endphp
                                            <tr>
                                                <td class="font-weight-bold text-primary">
                                                    {{ $codigo ?? '—' }}
                                                </td>
                                                <td>
                                                    <span class="badge {{ $badge }}">{{ $estado }}</span>
                                                </td>
                                                <td class="text-muted">
                                                    {{ trim($comunidad) !== '' ? $comunidad : '—' }}
                                                    @if(trim($provincia) !== '')
                                                        <span class="text-muted">/ {{ $provincia }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(!empty($codigo))
                                                        <a href="{{ route('gateway.trazabilidad.paquete', ['codigo' => $codigo]) }}"
                                                           class="btn btn-sm btn-info">
                                                            <i class="fas fa-route mr-1"></i> Rastrear
                                                        </a>
                                                    @else
                                                        <span class="text-muted small">Sin código</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- HISTORIAL DE UBICACIÓN / EVENTOS --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-history mr-1 text-secondary"></i> Historial de movimientos
                        </h3>
                    </div>

                    <div class="card-body">
                        @if(empty($historial))
                            <div class="text-muted">No hay historial registrado.</div>
                        @else
                            <div class="timeline timeline-inverse">

                                @foreach($historial as $h)
                                    @php
                                        $fecha = $h['fecha'] ?? null;
                                        $estadoH = $h['estado'] ?? 'Evento';
                                        $zona = $h['zona'] ?? $h['ubicacion'] ?? null;
                                        $lat = $h['lat'] ?? null;
                                        $lng = $h['lng'] ?? null;

                                        $conductor = trim(($h['conductor']['nombre'] ?? '') . ' ' . ($h['conductor']['ci'] ?? ''));
                                        $vehPlaca = $h['vehiculo']['placa'] ?? null;

                                        $timeLabel = $fecha ? \Carbon\Carbon::parse($fecha)->format('d/m/Y H:i') : '';
                                    @endphp

                                    <div>
                                        <i class="fas fa-map-marker-alt bg-info"></i>
                                        <div class="timeline-item">
                                            <span class="time text-muted">
                                                <i class="far fa-clock"></i> {{ $timeLabel }}
                                            </span>

                                            <h3 class="timeline-header">
                                                <strong>{{ $estadoH }}</strong>
                                                @if(!empty($vehPlaca))
                                                    <span class="text-muted">- {{ $vehPlaca }}</span>
                                                @endif
                                            </h3>

                                            <div class="timeline-body">
                                                @if(!empty($zona))
                                                    <div class="mb-2">
                                                        <i class="fas fa-location-arrow mr-1 text-muted"></i>
                                                        <span class="text-muted">{{ $zona }}</span>
                                                    </div>
                                                @endif

                                                @if(!empty($conductor))
                                                    <div class="mb-2">
                                                        <i class="fas fa-user mr-1 text-muted"></i>
                                                        <span class="text-muted">{{ $conductor }}</span>
                                                    </div>
                                                @endif

                                                @if(!is_null($lat) && !is_null($lng))
                                                    <a class="btn btn-outline-primary btn-sm"
                                                       target="_blank"
                                                       href="https://www.google.com/maps/search/?api=1&query={{ $lat }},{{ $lng }}">
                                                        <i class="fas fa-map-marked-alt mr-1"></i> Ver en mapa
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div>
                                    <i class="far fa-clock bg-gray"></i>
                                </div>

                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    @endif

</div>
@endsection
