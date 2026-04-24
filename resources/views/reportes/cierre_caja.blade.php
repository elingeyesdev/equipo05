@extends('layouts.app')

@section('title','Reporte Financiero y Cierre de Caja')

@section('header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark">Reporte Financiero / Cierre de Caja</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Reportes</li>
        </ol>
    </div>
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-12">

        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filtros de Búsqueda Avanzada</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            
            <form method="GET" action="{{ route('reporte.cierreCaja') }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-bullhorn mr-1 text-primary"></i>Campaña</label>
                                <select name="campaniaid" class="form-control select2">
                                    <option value="">-- Todas las campañas --</option>
                                    @foreach($campanias as $c)
                                        <option value="{{ $c->campaniaid }}" {{ request('campaniaid') == $c->campaniaid ? 'selected' : '' }}>
                                            {{ Str::limit($c->titulo, 30) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label><i class="fas fa-calendar-alt mr-1 text-primary"></i>Rango de Fechas</label>
                            <div class="input-group">
                                <input type="date" name="desde" class="form-control" placeholder="Desde" value="{{ request('desde') }}">
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text"><i class="fas fa-arrow-right"></i></span>
                                </div>
                                <input type="date" name="hasta" class="form-control" placeholder="Hasta" value="{{ request('hasta') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label><i class="fas fa-info-circle mr-1 text-primary"></i>Estado</label>
                                <select name="estadoid" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1" {{ request('estadoid') == 1 ? 'selected' : '' }}>Pendiente</option>
                                    <option value="2" {{ request('estadoid') == 2 ? 'selected' : '' }}>Confirmada</option>
                                    <option value="3" {{ request('estadoid') == 3 ? 'selected' : '' }}>Asignada</option>
                                    <option value="4" {{ request('estadoid') == 4 ? 'selected' : '' }}>Utilizada</option>
                                    <option value="5" {{ request('estadoid') == 5 ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-money-bill-wave mr-1 text-primary"></i>Tipo</label>
                                <select name="tipodonacion" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    @foreach($tiposDonacion as $tipo)
                                        <option value="{{ $tipo }}" {{ request('tipodonacion') == $tipo ? 'selected' : '' }}>
                                            {{ ucfirst($tipo) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-user mr-1 text-primary"></i>Buscar Donante</label>
                                <input type="text" name="donante" class="form-control" 
                                       placeholder="Nombre o Apellido..." value="{{ request('donante') }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label><i class="fas fa-coins mr-1 text-primary"></i>Rango Montos (Bs)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="min_monto" class="form-control" placeholder="Mín 0.00" value="{{ request('min_monto') }}">
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text">-</span>
                                </div>
                                <input type="number" step="0.01" name="max_monto" class="form-control" placeholder="Máx" value="{{ request('max_monto') }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-eye-slash mr-1 text-primary"></i>Privacidad</label>
                                <select name="esanonima" class="form-control">
                                    <option value="">Todo (Público y Anónimo)</option>
                                    <option value="0" {{ request('esanonima') === '0' ? 'selected' : '' }}>Solo Públicas</option>
                                    <option value="1" {{ request('esanonima') === '1' ? 'selected' : '' }}>Solo Anónimas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="submit" class="btn btn-primary shadow-sm px-4">
                                <i class="fas fa-search mr-1"></i> <strong>Filtrar Resultados</strong>
                            </button>
                            <a href="{{ route('reporte.cierreCaja') }}" class="btn btn-default ml-2">
                                <i class="fas fa-eraser mr-1"></i> Limpiar Filtros
                            </a>
                        </div>
                        
                        <div>
                            {{-- AQUÍ ESTÁ LA CLAVE: request()->all() Pasa los filtros al PDF --}}
                            <a href="{{ route('reporte.cierreCaja.pdf', request()->all()) }}" target="_blank" class="btn btn-danger shadow-sm px-3">
                                <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                            </a>
                            <a href="{{ route('reporte.cierreCaja.excel', request()->all()) }}" target="_blank" class="btn btn-success shadow-sm ml-2">
                                <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="info-box bg-gradient-info shadow-sm">
                    <span class="info-box-icon"><i class="fas fa-coins"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Recaudado (Filtrado)</span>
                        <span class="info-box-number">Bs {{ number_format($totalGeneral, 2) }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">
                            {{ $donaciones->count() }} operaciones encontradas
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box bg-gradient-success shadow-sm">
                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Confirmadas</span>
                        <span class="info-box-number">Bs {{ number_format($totalConfirmadas, 2) }}</span>
                        <div class="progress">
                             <div class="progress-bar" style="width: {{ $totalGeneral > 0 ? ($totalConfirmadas/$totalGeneral)*100 : 0 }}%"></div>
                        </div>
                        <span class="progress-description">
                            {{ $totalGeneral > 0 ? number_format(($totalConfirmadas/$totalGeneral)*100, 1) : 0 }}% del monto total
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box bg-gradient-warning shadow-sm">
                    <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pendientes</span>
                        <span class="info-box-number">Bs {{ number_format($totalPendientes, 2) }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $totalGeneral > 0 ? ($totalPendientes/$totalGeneral)*100 : 0 }}%"></div>
                        </div>
                        <span class="progress-description">
                            {{ $totalGeneral > 0 ? number_format(($totalPendientes/$totalGeneral)*100, 1) : 0 }}% del monto total
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @php
            $donacionesPorCampania = $donaciones->groupBy('campaniaid');
        @endphp

        @forelse($donacionesPorCampania as $campaniaId => $donacionesCampania)
            @php
                $campania = $donacionesCampania->first()->campania;
                $totalCampania = $donacionesCampania->sum('monto');
                $cantidadDonaciones = $donacionesCampania->count();
            @endphp

            <div class="card card-outline card-primary mb-4 shadow-sm">
                <div class="card-header">
                    <h3 class="card-title text-primary">
                        <i class="fas fa-bullhorn mr-2"></i>
                        <strong>{{ $campania ? $campania->titulo : 'Sin Campaña Asignada / General' }}</strong>
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-light border mr-2">
                            <i class="fas fa-list mr-1"></i> {{ $cantidadDonaciones }} Ops.
                        </span>
                        <span class="badge badge-primary">
                            Total: Bs {{ number_format($totalCampania, 2) }}
                        </span>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px" class="text-center">ID</th>
                                    <th>Donante</th>
                                    <th>Tipo</th>
                                    <th class="text-right">Monto</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Privacidad</th>
                                    <th class="text-right">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($donacionesCampania as $d)
                                
                                <tr>
                                    <td class="text-center text-muted text-sm">{{ $d->donacionid }}</td>
                                    <td>
                                        @if($d->esanonima)
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-user-secret mr-1"></i>Anónimo
                                            </span>
                                        @else
                                            <div class="font-weight-bold text-dark">
                                                {{ optional($d->usuario)->nombre }} {{ optional($d->usuario)->apellido }}
                                            </div>
                                            <small class="text-muted">{{ optional($d->usuario)->email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-light border">{{ ucfirst($d->tipodonacion) }}</span>
                                    </td>
                                    <td class="text-right">
                                        <strong class="text-primary">Bs {{ number_format($d->monto, 2) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $estadoColor = match($d->estadoid) {
                                                1 => 'warning',
                                                2 => 'success',
                                                3 => 'info',
                                                4 => 'primary',
                                                5 => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $estadoColor }} px-2">
                                            {{ optional($d->estado)->nombre }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($d->esanonima)
                                            <i class="fas fa-eye-slash text-muted" title="Anónimo"></i>
                                        @else
                                            <i class="fas fa-eye text-success" title="Público"></i>
                                        @endif
                                    </td>
                                    <td class="text-right text-muted text-sm">
                                        {{ \Carbon\Carbon::parse($d->fechadonacion)->format('d/m/Y H:i') }}
                                    </td>
                                </tr>

                                @if($d->asignacionesPivot->count())
                                <tr>
                                    <td colspan="7" class="p-0 bg-light">
                                        <div class="p-3 border-left border-info ml-4 mr-2 my-2 bg-white shadow-sm rounded">
                                            <h6 class="text-info mb-2 text-sm font-weight-bold">
                                                <i class="fas fa-share-square mr-1"></i> Distribución de fondos (Uso de la donación):
                                            </h6>
                                            
                                            @foreach($d->asignacionesPivot as $pivot)
                                                @php $asig = $pivot->asignacion; @endphp
                                                
                                                <div class="card card-outline card-success mb-2 collapsed-card">
                                                    <div class="card-header py-1 px-3">
                                                        <div class="d-flex justify-content-between align-items-center w-100">
                                                            <span class="text-sm">
                                                                <strong>Asignación #{{ $asig->asignacionid }}:</strong>
                                                                {{ Str::limit($asig->descripcion, 60) }}
                                                            </span>
                                                            <div>
                                                                <span class="badge badge-success mr-2">
                                                                    Bs {{ number_format($pivot->montoasignado, 2) }}
                                                                </span>
                                                                <button type="button" class="btn btn-tool btn-xs" data-card-widget="collapse">
                                                                    <i class="fas fa-plus"></i> Detalles
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-2 bg-light">
                                                        @if($asig->detalles->count())
                                                            <table class="table table-sm table-striped mb-0 text-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Concepto</th>
                                                                        <th class="text-center">Cant.</th>
                                                                        <th class="text-right">Unitario</th>
                                                                        <th class="text-right">Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($asig->detalles as $det)
                                                                    <tr>
                                                                        <td>{{ $det->concepto }}</td>
                                                                        <td class="text-center">{{ $det->cantidad }}</td>
                                                                        <td class="text-right">{{ number_format($det->preciounitario, 2) }}</td>
                                                                        <td class="text-right font-weight-bold">{{ number_format($det->cantidad * $det->preciounitario, 2) }}</td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        @else
                                                            <small class="text-muted font-italic">Sin desglose de items registrado.</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @empty
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="fas fa-filter fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No se encontraron resultados</h4>
                        <p class="text-muted">Intenta ajustar los filtros de búsqueda para ver información.</p>
                        <a href="{{ route('reporte.cierreCaja') }}" class="btn btn-outline-primary btn-sm mt-2">
                            Limpiar Filtros
                        </a>
                    </div>
                </div>
            </div>
        @endforelse

    </div>
</div>

@endsection