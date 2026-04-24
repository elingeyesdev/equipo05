@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-primary mb-0">
                <i class="fas fa-box-open"></i> Trazabilidad del Paquete
            </h1>
            <p class="text-muted">Código: <strong>{{ $codigo }}</strong></p>
        </div>
        <a href="{{ route('almacenes.estructura') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @if(isset($data['services']['donaciones']['paquete']))
        @php $pkg = $data['services']['donaciones']['paquete']; @endphp
        
        {{-- Tarjeta de Estado General --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 border-end">
                        <small class="text-muted text-uppercase">Estado</small>
                        <h4 class="fw-bold mt-1 text-info">{{ ucfirst($pkg['estado']) }}</h4>
                    </div>
                    <div class="col-md-3 border-end">
                        <small class="text-muted text-uppercase">Fecha Creación</small>
                        <h5 class="mt-1">{{ \Carbon\Carbon::parse($pkg['fecha_creacion'])->format('d/m/Y H:i') }}</h5>
                    </div>
                    <div class="col-md-3 border-end">
                        <small class="text-muted text-uppercase">Total Productos</small>
                        <h5 class="mt-1">{{ $pkg['total_productos'] }}</h5>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted text-uppercase">Registros Salida</small>
                        <h5 class="mt-1">{{ $pkg['total_registros_salida'] }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Columna Izquierda: Contenido --}}
            <div class="col-md-6">
                <h5 class="fw-bold mb-3"><i class="fas fa-cubes"></i> Contenido del Paquete</h5>
                <div class="list-group shadow-sm">
                    @foreach($data['services']['donaciones']['detalles'] ?? [] as $det)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1 fw-bold">{{ $det['producto']['nombre'] }}</h6>
                                <span class="badge bg-primary rounded-pill">{{ $det['cantidad_usada'] }}</span>
                            </div>
                            <small class="text-muted">{{ $det['producto']['descripcion'] }}</small>
                            <div class="mt-1" style="font-size: 0.85rem;">
                                <span class="text-success"><i class="fas fa-user"></i> Donante: {{ $det['donacion']['donante']['nombre'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Columna Derecha: Salidas / Destinos --}}
            <div class="col-md-6">
                <h5 class="fw-bold mb-3"><i class="fas fa-shipping-fast"></i> Historial de Salidas (Gateway)</h5>
                
                @php $salidas = $data['services']['donaciones']['registros_salida'] ?? []; @endphp

                @if(count($salidas) > 0)
                    <div class="timeline">
                        @foreach($salidas as $salida)
                            <div class="card mb-3 border-start border-4 border-success shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold text-success">
                                        <i class="fas fa-map-marker-alt"></i> Destino: {{ $salida['destino'] }}
                                    </h6>
                                    <p class="card-text mb-1">
                                        <small class="text-muted"><i class="far fa-clock"></i> Fecha Salida:</small> 
                                        {{ \Carbon\Carbon::parse($salida['fecha_salida'])->format('d/m/Y H:i') }}
                                    </p>
                                    @if($salida['observaciones'])
                                        <div class="alert alert-light mt-2 mb-0 p-2 border">
                                            <small><em>"{{ $salida['observaciones'] }}"</em></small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> Este paquete aún no ha salido del almacén.
                    </div>
                @endif
            </div>
        </div>

    @else
        <div class="alert alert-danger">
            No se encontró información estructurada para este paquete en el Gateway.
        </div>
    @endif
</div>
@endsection