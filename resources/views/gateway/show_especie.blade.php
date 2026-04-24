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
                Animales liberados <span class="text-muted">-</span>
                <span class="text-success">{{ $nombre ?? $especie->identificador }}</span>
            </h1>
            <small class="text-muted">
                Visualización desde el Gateway externo (cache local).
            </small>
        </div>

        <span class="badge badge-secondary px-3 py-2">
            FUENTE: GATEWAY EXTERNO
        </span>
    </div>

    @if(empty($especie->response_detalle))
        <div class="alert alert-info shadow-sm">
            <i class="fas fa-sync fa-spin mr-2"></i>
            Sin información detallada todavía. Vuelve a sincronizar o recarga en unos segundos.
        </div>
    @else
        @php
            // Estructura real del endpoint: services -> animales -> data
            $data = $especie->response_detalle['services']['animales']['data'] ?? [];
            $filtro = strtolower(trim($nombre ?? $especie->identificador));

            // Filtramos por especie.nombre si viene en el payload
            $animales = collect($data)->filter(function ($row) use ($filtro) {
                $esp = strtolower(trim($row['especie']['nombre'] ?? ''));
                return $filtro === '' ? true : $esp === $filtro;
            })->values();
        @endphp

        @if($animales->isEmpty())
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                    <p class="mb-0 text-muted">
                        No se encontraron animales liberados para la especie seleccionada.
                    </p>
                </div>
            </div>
        @else

            @foreach($animales as $item)
                @php
                    $animal   = $item['animal'] ?? [];
                    $especieI = $item['especie'] ?? [];
                    $estado   = $item['estado'] ?? [];
                    $historial = $item['historial'] ?? [];
                    $ruta = $item['ruta']['points'] ?? [];
                    $fechaCreacion = $item['fecha_creacion'] ?? null;

                    $nombreAnimal = $animal['nombre'] ?? 'Sin nombre';
                    $sexo = $animal['sexo'] ?? 'N/D';
                    $desc = $animal['descripcion'] ?? null;

                    $estadoNombre = $estado['nombre'] ?? 'N/D';
                    $especieNombre = $especieI['nombre'] ?? 'N/D';

                    // Badge estado
                    $badgeEstado = 'badge-secondary';
                    if (strtolower($estadoNombre) === 'estable') $badgeEstado = 'badge-success';
                @endphp

                <div class="card shadow-sm mb-4 border-0">

                    {{-- CABECERA ANIMAL --}}
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <span class="badge badge-success" style="font-size: 14px;">
                                        <i class="fas fa-paw mr-1"></i> {{ $nombreAnimal }}
                                    </span>
                                </div>

                                <div class="text-muted small">
                                    <span class="mr-2"><strong>Sexo:</strong> {{ $sexo }}</span>
                                    <span class="mr-2"><strong>Especie:</strong> {{ $especieNombre }}</span>
                                    <span><strong>Estado:</strong>
                                        <span class="badge {{ $badgeEstado }}">{{ $estadoNombre }}</span>
                                    </span>
                                </div>
                            </div>

                            <div class="text-muted small mt-2 mt-md-0">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ $fechaCreacion ? \Carbon\Carbon::parse($fechaCreacion)->format('d/m/Y H:i') : 'Fecha no disponible' }}
                            </div>
                        </div>
                    </div>

                    <div class="card-body">

                        {{-- DESCRIPCION --}}
                        @if(!empty($desc))
                            <div class="alert alert-light border shadow-sm mb-4">
                                <div class="d-flex">
                                    <div class="mr-3">
                                        <i class="fas fa-align-left text-muted"></i>
                                    </div>
                                    <div>
                                        <strong>Descripción:</strong>
                                        <span class="text-muted">{{ $desc }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">

                            {{-- HISTORIAL --}}
                            <div class="col-lg-7 mb-4 mb-lg-0">
                                <h6 class="text-uppercase text-muted mb-3">
                                    <i class="fas fa-history mr-1"></i> Historial
                                </h6>

                                @if(empty($historial))
                                    <div class="text-muted">No hay historial disponible.</div>
                                @else
                                    <div class="timeline" style="position: relative;">
                                        @foreach($historial as $h)
                                            @php
                                                $titulo = $h['title'] ?? 'Evento';
                                                $fechaLabel = $h['changed_at_label'] ?? ($h['changed_at'] ?? '');
                                                $detalles = $h['details'] ?? [];
                                                $img = $h['image_url'] ?? null;
                                            @endphp

                                            <div class="card mb-3 border-0 shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <div class="font-weight-bold text-dark">
                                                            {{ $titulo }}
                                                        </div>
                                                        <div class="text-muted small">
                                                            {{ $fechaLabel }}
                                                        </div>
                                                    </div>

                                                    @if(!empty($detalles))
                                                        <ul class="mt-2 mb-0 pl-3">
                                                            @foreach($detalles as $d)
                                                                <li class="text-muted small mb-1">
                                                                    <strong class="text-dark">{{ $d['label'] ?? 'Dato' }}:</strong>
                                                                    {{ $d['value'] ?? '-' }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif

                                                    @if(!empty($img))
                                                        <div class="mt-3">
                                                            <small class="text-muted d-block mb-1">Archivo asociado</small>
                                                            <code class="small">{{ $img }}</code>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- RUTA --}}
                            <div class="col-lg-5">
                                <h6 class="text-uppercase text-muted mb-3">
                                    <i class="fas fa-route mr-1"></i> Ruta
                                </h6>

                                @if(empty($ruta))
                                    <div class="text-muted">No hay ruta disponible.</div>
                                @else
                                    @foreach($ruta as $p)
                                        @php
                                            $type = $p['type'] ?? 'point';
                                            $label = $p['label'] ?? 'Punto';
                                            $address = $p['address'] ?? 'Ubicación no especificada';
                                            $date = $p['date'] ?? '';
                                            $lat = $p['lat'] ?? null;
                                            $lon = $p['lon'] ?? null;

                                            $icon = 'fa-map-marker-alt';
                                            $bg = 'bg-info';
                                            if ($type === 'release') { $icon = 'fa-dove'; $bg = 'bg-success'; }
                                            if ($type === 'report') { $icon = 'fa-flag'; $bg = 'bg-primary'; }
                                        @endphp

                                        <div class="card border-0 shadow-sm mb-3">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="mr-3">
                                                        <div class="d-flex align-items-center justify-content-center {{ $bg }} text-white"
                                                             style="width:42px;height:42px;border-radius: 12px;">
                                                            <i class="fas {{ $icon }}"></i>
                                                        </div>
                                                    </div>

                                                    <div class="w-100">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="font-weight-bold">{{ $label }}</div>
                                                            <div class="text-muted small">{{ $date }}</div>
                                                        </div>

                                                        <div class="text-muted small mt-1">
                                                            {{ $address }}
                                                        </div>

                                                        @if(!is_null($lat) && !is_null($lon))
                                                            <div class="mt-2">
                                                                <a class="btn btn-outline-primary btn-sm"
                                                                   target="_blank"
                                                                   href="https://www.google.com/maps/search/?api=1&query={{ $lat }},{{ $lon }}">
                                                                    <i class="fas fa-map-marked-alt mr-1"></i> Ver en mapa
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                        </div>

                    </div>
                </div>
            @endforeach

        @endif
    @endif

</div>
@endsection
