@extends('adminlte::page')

@section('title', 'Detalles de la Donación')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Detalles de la Donación</h1>
    </div>
    <div class="col-sm-6">
        <a class="btn btn-secondary float-right" href="{{ route('inventario.donaciones.index') }}">
            Volver al Listado
        </a>
    </div>
</div>
@stop

@section('content')
@include('inventario::partials.flash-messages')
{{-- Info Boxes Row --}}
<div class="row">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-hashtag"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">ID Donación</span>
                <span class="info-box-number">{{ $donacion->id_donacion }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-user"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Donante</span>
                <span class="info-box-number" style="font-size: 1rem;">{{ $donacion->donante->nombre ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon 
                    @if($donacion->tipo === 'dinero') bg-success
                    @elseif($donacion->tipo === 'especie') bg-warning
                    @else bg-purple
                    @endif">
                <i class="fas 
                        @if($donacion->tipo === 'dinero') fa-dollar-sign
                        @elseif($donacion->tipo === 'especie') fa-box-open
                        @else fa-tshirt
                        @endif">
                </i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Tipo de Donación</span>
                <span class="info-box-number" style="font-size: 1.2rem;">{{ ucfirst($donacion->tipo) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="far fa-calendar-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Fecha</span>
                <span class="info-box-number"
                    style="font-size: 0.9rem;">{{ \Carbon\Carbon::parse($donacion->fecha)->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Main Information Card --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Información General</h3>
        <div class="card-tools">
            <a href="{{ route('inventario.donaciones.edit', $donacion->id_donacion) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Tipo de Donación:</dt>
            <dd class="col-sm-9">
                @if($donacion->tipo === 'dinero')
                    <span class="badge badge-success badge-lg">
                        <i class="fas fa-dollar-sign"></i> Dinero
                    </span>
                @elseif($donacion->tipo === 'especie')
                    <span class="badge badge-warning badge-lg">
                        <i class="fas fa-box-open"></i> Especie
                    </span>
                @else
                    <span class="badge badge-purple badge-lg">
                        <i class="fas fa-tshirt"></i> Ropa
                    </span>
                @endif
            </dd>

            <dt class="col-sm-3">Donante:</dt>
            <dd class="col-sm-9">
                <strong>{{ $donacion->donante->nombre ?? 'N/A' }}</strong>
            </dd>

            <dt class="col-sm-3">Fecha y Hora:</dt>
            <dd class="col-sm-9">
                {{ \Carbon\Carbon::parse($donacion->fecha)->format('d/m/Y H:i') }}
            </dd>

            @if($donacion->observaciones)
                <dt class="col-sm-3">Observaciones:</dt>
                <dd class="col-sm-9">{{ $donacion->observaciones }}</dd>
            @endif

            @if($donacion->ci_usuario_registro)
                <dt class="col-sm-3">Registrado por (CI):</dt>
                <dd class="col-sm-9">
                    <span class="badge badge-info badge-lg">
                        <i class="fas fa-id-card"></i> {{ $donacion->ci_usuario_registro }}
                    </span>
                </dd>
            @endif
        </dl>
    </div>
</div>

{{-- Money Details Card --}}
@if($donacion->dinero)
    <div class="card card-success card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-dollar-sign"></i> Detalles de Donación en Dinero</h3>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Monto:</dt>
                <dd class="col-sm-9">
                    <h4 class="text-success">
                        Bs. {{ number_format($donacion->dinero->monto, 2) }}
                    </h4>
                </dd>

                @if($donacion->dinero->moneda)
                    <dt class="col-sm-3">Moneda:</dt>
                    <dd class="col-sm-9">
                        <span class="badge badge-info">{{ $donacion->dinero->moneda }}</span>
                    </dd>
                @endif

                @if($donacion->dinero->metodo_pago)
                    <dt class="col-sm-3">Método de Pago:</dt>
                    <dd class="col-sm-9">
                        <span class="badge badge-primary">{{ ucfirst($donacion->dinero->metodo_pago) }}</span>
                    </dd>
                @endif

                @if($donacion->dinero->referencia_pago)
                    <dt class="col-sm-3">Referencia:</dt>
                    <dd class="col-sm-9">
                        @if(Str::endsWith($donacion->dinero->referencia_pago, '.pdf'))
                            <a href="{{ asset($donacion->dinero->referencia_pago) }}" target="_blank"
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-file-pdf"></i> Ver Comprobante PDF
                            </a>
                        @else
                            <div class="mb-2">
                                <img src="{{ asset($donacion->dinero->referencia_pago) }}" alt="Comprobante de pago"
                                    class="img-thumbnail" style="max-width: 100%; max-height: 300px; cursor: pointer;"
                                    onclick="showImageModal('{{ asset($donacion->dinero->referencia_pago) }}', 'Comprobante de Pago')">
                            </div>
                            <button type="button" class="btn btn-sm btn-primary"
                                onclick="showImageModal('{{ asset($donacion->dinero->referencia_pago) }}', 'Comprobante de Pago')">
                                <i class="fas fa-search-plus"></i> Ver imagen completa
                            </button>
                        @endif
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-link"></i> URL:
                            <a href="{{ url($donacion->dinero->referencia_pago) }}" target="_blank" class="text-primary">
                                <code>{{ url($donacion->dinero->referencia_pago) }}</code>
                            </a>
                        </small>
                    </dd>
                @endif
            </dl>
        </div>
    </div>
@endif

{{-- Products Details Card --}}
@if($donacion->detalles && $donacion->detalles->count())
    <div class="card card-warning card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-box-open"></i> Detalles de Productos</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Producto</th>
                            <th width="15%">Cantidad Total</th>
                            <th width="15%">Unidad</th>
                            <th width="25%">Espacios</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Group products by id_producto
                            $productosAgrupados = [];
                            foreach ($donacion->detalles as $det) {
                                $idProducto = $det->id_producto;
                                if (!isset($productosAgrupados[$idProducto])) {
                                    $productosAgrupados[$idProducto] = [
                                        'nombre' => $det->producto->nombre ?? 'N/A',
                                        'cantidad' => 0,
                                        'unidad_medida' => $det->unidad_medida ?? $det->producto->unidad_medida ?? '',
                                        'espacios' => []
                                    ];
                                }
                                $productosAgrupados[$idProducto]['cantidad'] += $det->cantidad;

                                // Add space if exists
                                $espacio = $det->ubicaciones->first()->espacio->codigo_espacio ?? null;
                                if ($espacio && !in_array($espacio, $productosAgrupados[$idProducto]['espacios'])) {
                                    $productosAgrupados[$idProducto]['espacios'][] = $espacio;
                                }
                            }
                        @endphp

                        @foreach($productosAgrupados as $producto)
                            <tr>
                                <td><strong>{{ $producto['nombre'] }}</strong></td>
                                <td class="text-center">
                                    <h5 class="mb-0">{{ $producto['cantidad'] }}</h5>
                                </td>
                                <td><span class="badge badge-primary badge-lg">{{ $producto['unidad_medida'] }}</span></td>
                                <td>
                                    @foreach($producto['espacios'] as $espacio)
                                        <span class="badge badge-info mr-1">{{ $espacio }}</span>
                                    @endforeach
                                    @if(empty($producto['espacios']))
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

{{-- Action Buttons --}}
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('inventario.donaciones.index') }}" class="btn btn-secondary">
            Volver al Listado
        </a>
        <a href="{{ route('inventario.donaciones.edit', $donacion->id_donacion) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar Donación
        </a>
        <form action="{{ route('inventario.donaciones.destroy', $donacion->id_donacion) }}" method="POST" style="display: inline;"
            onsubmit="return confirm('¿Está seguro de eliminar esta donación?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
</div>
@stop

@section('css')
<style>
    .badge-lg {
        font-size: 0.9rem;
        padding: 0.4rem 0.6rem;
    }

    .bg-purple {
        background-color: #6f42c1 !important;
    }

    .badge-purple {
        background-color: #6f42c1;
        color: white;
    }

    .modal-image {
        width: 100%;
        height: auto;
        max-height: 80vh;
        object-fit: contain;
    }
</style>
@stop

@section('js')
<script>
    function showImageModal(imageUrl, imageTitle) {
        $('#imageModalLabel').text(imageTitle);
        $('#modalImage').attr('src', imageUrl);
        $('#imageModal').modal('show');
    }
</script>
@stop

<!-- Modal para ver imagen completa -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="imageModalLabel">Imagen</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" alt="Imagen" class="modal-image">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>




