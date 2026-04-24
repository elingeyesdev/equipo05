@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Integración Gateway Externo</h1>
            <p class="text-muted small mb-0">
                Visualización de datos sincronizados con <em>gatealas.dasalas.shop</em>
            </p>
        </div>
        <a href="{{ route('gateway.trazabilidad.sync') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-sync-alt fa-sm text-white-50 mr-1"></i> Actualizar datos
        </a>
    </div>

    {{-- CARDS RESUMEN --}}
    <div class="row mb-4">

        {{-- PAQUETES --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="#" class="text-decoration-none js-open-tab" data-link="#tab-paquetes">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Paquetes / Solicitudes
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $solicitudes->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-box fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- VEHICULOS --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="#" class="text-decoration-none js-open-tab" data-link="#tab-vehiculos">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Flota Vehicular
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $vehiculos->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-truck fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- ESPECIES --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="#" class="text-decoration-none js-open-tab" data-link="#tab-especies">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Especies Registradas
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $especies->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-paw fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

    {{-- TABS --}}
    <div class="card shadow mb-4" id="gatewayTabsCard">
        <div class="card-header bg-white border-bottom-0">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">

                <li class="nav-item">
                    <a class="nav-link active" id="tab-paquetes" data-toggle="tab" href="#paquetes">
                        <i class="fas fa-box mr-1"></i> Paquetes
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="tab-vehiculos" data-toggle="tab" href="#vehiculos">
                        <i class="fas fa-truck mr-1"></i> Vehículos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="tab-especies" data-toggle="tab" href="#especies">
                        <i class="fas fa-paw mr-1"></i> Especies
                    </a>
                </li>

            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">

                {{-- TAB PAQUETES --}}
                <div class="tab-pane fade show active" id="paquetes">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Estado</th>
                                    <th>Última act.</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($solicitudes as $s)
                                    <tr>
                                        <td class="font-weight-bold text-primary">{{ $s->identificador }}</td>
                                        <td>
                                            @php $estado = $s->datos_extra['estado'] ?? 'N/A'; @endphp
                                            <span class="badge
                                                {{ $estado=='aprobada'?'badge-success':($estado=='pendiente'?'badge-warning':'badge-secondary') }}">
                                                {{ strtoupper($estado) }}
                                            </span>
                                        </td>
                                        <td>{{ $s->updated_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('gateway.trazabilidad.paquete', ['codigo'=>$s->identificador]) }}"
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Rastrear
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            No hay paquetes sincronizados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB VEHICULOS --}}
                <div class="tab-pane fade" id="vehiculos">
                    <div class="row">
                        @forelse($vehiculos as $v)
                            <div class="col-md-4 mb-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-truck fa-3x text-secondary mb-3"></i>
                                        <h5>{{ $v->identificador }}</h5>
                                        <a href="{{ route('gateway.trazabilidad.vehiculo', ['placa'=>$v->identificador]) }}"
                                           class="btn btn-outline-primary btn-sm mt-2">
                                            Ver detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">
                                No hay vehículos registrados.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- TAB ESPECIES --}}
                <div class="tab-pane fade" id="especies">
                    <div class="row">
                        @forelse($especies as $e)
                            <div class="col-md-3 mb-3">
                                <div class="card shadow-sm h-100 text-center">
                                    <div class="card-body">
                                        <i class="fas fa-leaf fa-3x text-success mb-3"></i>
                                        <h5>{{ $e->identificador }}</h5>
                                        <a href="{{ route('gateway.trazabilidad.especie', ['nombre'=>$e->identificador]) }}"
                                           class="btn btn-outline-success btn-sm w-100 mt-2">
                                            Ver mapa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">
                                No hay especies registradas.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('.js-open-tab').on('click', function (e) {
        e.preventDefault();

        const tab = $(this).data('link');
        if (tab) {
            $(tab).tab('show');
        }

        document.getElementById('gatewayTabsCard')
            .scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});
</script>
@endpush
