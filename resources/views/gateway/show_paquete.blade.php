@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('gateway.trazabilidad.index') }}" class="text-decoration-none mb-3 d-inline-block">
        <i class="fas fa-arrow-left"></i> Volver al listado
    </a>

    @php
        $json = $paquete->response_detalle ?? [];

        $services  = $json['services'] ?? [];

        $donaciones = $services['donaciones'] ?? [];
        $logistica  = $services['logistica'] ?? [];

        $paqDon = $donaciones['paquete'] ?? [];
        $detalles = $donaciones['detalles'] ?? [];
        $regSalidas = $donaciones['registros_salida'] ?? [];

        $solicitud = $logistica['solicitud'] ?? [];
        $paqLog = $logistica['paquete'] ?? [];
        $historial = $logistica['historial'] ?? [];

        // Estado: prioridad a donaciones.paquete.estado, si no, datos_extra.estado
        $estado = $paqDon['estado'] ?? ($paquete->datos_extra['estado'] ?? 'N/A');

        // Destino (si existe)
        $dest = $solicitud['destino'] ?? [];
        $destinoTxt = trim(
            ($dest['comunidad'] ?? '') .
            (isset($dest['provincia']) ? ' - '.$dest['provincia'] : '')
        );

        $direccion = $dest['direccion'] ?? null;

        // Ubicación actual (si existe)
        $ubicacionActual = $paqLog['ubicacion_actual'] ?? null;

        // coordenadas destino
        $lat = $dest['latitud'] ?? null;
        $lng = $dest['longitud'] ?? null;
    @endphp

    <div class="row">
        {{-- PANEL IZQUIERDO --}}
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Paquete: {{ $paquete->identificador }}</h5>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Estado Actual</label>
                        <div>
                            <span class="badge
                                {{ in_array(strtolower($estado), ['aprobada','entregado','despachado']) ? 'bg-success' : (strtolower($estado)=='pendiente' ? 'bg-warning text-dark' : 'bg-secondary') }}
                                fs-6">
                                {{ ucfirst($estado) }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Destino</label>
                        <div class="fw-bold">{{ $destinoTxt ?: 'Sin datos' }}</div>
                        @if($direccion)
                            <div class="text-muted small">{{ $direccion }}</div>
                        @endif

                        @if($lat && $lng)
                            <a class="btn btn-sm btn-outline-primary mt-2"
                               target="_blank"
                               href="https://www.google.com/maps/search/?api=1&query={{ $lat }},{{ $lng }}">
                                Ver destino en mapa
                            </a>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Ubicación actual</label>
                        <div class="fw-bold">{{ $ubicacionActual ?: 'Sin datos' }}</div>
                    </div>

                    <div class="mb-0">
                        <label class="text-muted small">Referencia</label>
                        <div class="fw-bold">{{ $solicitud['nombre_referencia'] ?? 'Sin datos' }}</div>
                        <div class="text-muted small">{{ $solicitud['celular_referencia'] ?? '' }}</div>
                    </div>

                    {{-- si el gateway no devolvió detalle --}}
                    @if(empty($paquete->response_detalle))
                        <div class="alert alert-warning small mt-3 mb-0">
                            Datos detallados pendientes de sincronización.
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-light small text-muted">
                    Actualizado: {{ $paquete->updated_at?->format('d/m/Y H:i') }}
                </div>
            </div>

            {{-- PRODUCTOS / DETALLES DE DONACIONES --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Productos / Donaciones</h6>
                </div>

                <div class="card-body">
                    @if(!empty($detalles))
                        <div class="list-group">
                            @foreach($detalles as $d)
                                @php
                                    $prod = $d['producto'] ?? [];
                                    $don  = $d['donacion'] ?? [];
                                    $donante = $don['donante'] ?? [];
                                @endphp
                                <div class="list-group-item">
                                    <div class="fw-bold">{{ $prod['nombre'] ?? 'Producto' }}</div>
                                    <div class="text-muted small">{{ $prod['descripcion'] ?? '' }}</div>
                                    <div class="small mt-1">
                                        Cantidad usada: <span class="fw-bold">{{ $d['cantidad_usada'] ?? '-' }}</span>
                                    </div>
                                    <div class="small">
                                        Donante: <span class="fw-bold">{{ $donante['nombre'] ?? '—' }}</span>
                                        <span class="text-muted">({{ $don['tipo'] ?? '—' }})</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted small">Sin detalles de donaciones.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- PANEL DERECHO: HISTORIAL --}}
        <div class="col-md-8">
            <h4 class="mb-3">Historial de Movimientos</h4>

            @if(!empty($historial))
                <div class="list-group">
                    @foreach($historial as $h)
                        @php
                            $fecha = $h['fecha'] ?? null;
                            $estadoH = $h['estado'] ?? 'Evento';
                            $zona = $h['zona'] ?? null;
                            $latH = $h['lat'] ?? null;
                            $lngH = $h['lng'] ?? null;
                            $veh = $h['vehiculo']['placa'] ?? null;
                            $cond = $h['conductor']['nombre'] ?? null;
                        @endphp

                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div class="fw-bold text-primary">{{ $estadoH }}</div>
                                <small class="text-muted">{{ $fecha }}</small>
                            </div>

                            @if($zona)
                                <div class="text-muted">{{ $zona }}</div>
                            @endif

                            <div class="small mt-1">
                                @if($cond)
                                    Conductor: <span class="fw-bold">{{ $cond }}</span>
                                @endif
                                @if($veh)
                                    <span class="ms-2">Vehículo: <span class="fw-bold">{{ $veh }}</span></span>
                                @endif
                            </div>

                            @if($latH && $lngH)
                                <a class="btn btn-sm btn-outline-primary mt-2"
                                   target="_blank"
                                   href="https://www.google.com/maps/search/?api=1&query={{ $latH }},{{ $lngH }}">
                                    Ver en mapa
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-light border text-center py-5">
                    <i class="fas fa-search fa-2x text-muted mb-3"></i>
                    <p class="mb-0">No se encontró historial detallado en el Gateway para este código.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
