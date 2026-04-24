@extends('layouts.app')

@section('title', 'Detalles de Asignación')

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Asignación #{{ $asignacion->asignacionid }}</h1>
            <small class="text-muted">Campaña: {{ optional($asignacion->campania)->titulo ?? 'N/A' }}</small>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('asignaciones.index') }}">Asignaciones</a></li>
                <li class="breadcrumb-item active">Detalles</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    @php
        $totalDetalles = $detalles->sum(fn($d) => $d->cantidad * $d->preciounitario);
        $totalAsignado = $donacionesAsignadas->sum('montoasignado');
        $faltante = max(0, $totalDetalles - $totalAsignado);
    @endphp

    <div class="row">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="card-title text-primary mb-1">Total ítems</h6>
                    <div class="h4 mb-0">Bs {{ number_format($totalDetalles,2,'.',',') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="card-title text-success mb-1">Donaciones asignadas</h6>
                    <div class="h4 mb-0">Bs {{ number_format($totalAsignado,2,'.',',') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="card-title text-warning mb-1">Faltante</h6>
                    <div class="h4 mb-0">Bs {{ number_format($faltante,2,'.',',') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulario para agregar ítem --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Agregar Ítem</h3>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger"><strong>Revisa los errores:</strong>
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('asignaciones.detalles.store', $asignacion->asignacionid) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Concepto <span class="text-danger">*</span></label>
                        <input type="text" name="concepto" class="form-control @error('concepto') is-invalid @enderror"
                               value="{{ old('concepto') }}" required>
                        @error('concepto')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Cantidad <span class="text-danger">*</span></label>
                        <input type="number" name="cantidad" min="1"
                               class="form-control @error('cantidad') is-invalid @enderror"
                               value="{{ old('cantidad',1) }}" required>
                        @error('cantidad')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Precio Unitario <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="preciounitario"
                               class="form-control @error('preciounitario') is-invalid @enderror"
                               value="{{ old('preciounitario') }}" required>
                        @error('preciounitario')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label>Imagen (opcional)</label>
                    <input type="file" name="imagen" class="form-control @error('imagen') is-invalid @enderror" accept="image/*">
                    @error('imagen')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-success"><i class="fas fa-plus"></i> Agregar Ítem</button>
                    <a href="{{ route('asignaciones.asignar', $asignacion->asignacionid) }}" class="btn btn-warning">
                        <i class="fas fa-donate"></i> Asignar Donaciones
                    </a>
                    <a href="{{ route('asignaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de ítems --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Ítems agregados</h3></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Concepto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th>Imagen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detalles as $d)
                            <tr>
                                <td>{{ $d->detalleid }}</td>
                                <td>{{ $d->concepto }}</td>
                                <td>{{ $d->cantidad }}</td>
                                <td>Bs {{ number_format($d->preciounitario,2) }}</td>
                                <td>Bs {{ number_format($d->cantidad * $d->preciounitario,2) }}</td>
                                <td>
                                    @if($d->imagenurl)
                                        <a href="{{ $d->imagenurl }}" target="_blank" class="btn btn-sm btn-outline-secondary">Ver</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">Aún no hay ítems.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-right">
            <strong>Total:</strong> Bs {{ number_format($totalDetalles,2) }}
        </div>
    </div>

    {{-- Donaciones aplicadas --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Donaciones aplicadas</h3></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Registro</th>
                            <th>Donación</th>
                            <th>Monto Asignado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donacionesAsignadas as $r)
                            <tr>
                                <td>{{ $r->donacionasignacionid }}</td>
                                <td>#{{ $r->donacionid }}</td>
                                <td>Bs {{ number_format($r->montoasignado,2) }}</td>
                                <td>{{ $r->fechaasignacion }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">No hay donaciones asignadas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-right">
            <strong>Total asignado:</strong> Bs {{ number_format($totalAsignado,2) }}
        </div>
    </div>
@endsection
