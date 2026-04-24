@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Asignaciones</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Asignaciones</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- ALERTAS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="icon fas fa-check"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- TARJETAS DE INFORMACIÓN --}}
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $asignaciones->total() }}</h3>
                        <p>Total Asignaciones</p>
                    </div>
                    <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($asignaciones->sum('monto'), 2) }}</h3>
                        <p>Monto Total (Bs)</p>
                    </div>
                    <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $campanias->count() }}</h3>
                        <p>Campañas Activas</p>
                    </div>
                    <div class="icon"><i class="fas fa-bullhorn"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $asignaciones->sum('donaciones_count') }}</h3>
                        <p>Donaciones Asignadas</p>
                    </div>
                    <div class="icon"><i class="fas fa-hand-holding-heart"></i></div>
                </div>
            </div>
        </div>

        {{-- FORMULARIO DE NUEVA ASIGNACIÓN (COLLAPSIBLE EN EL INDEX) --}}
        <div class="card card-primary collapsed-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus-circle"></i> Nueva Asignación (Rápida)
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: none;">
                <form action="{{ route('asignaciones.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        {{-- Campaña --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="campaniaid">
                                    <i class="fas fa-bullhorn"></i> Campaña <span class="text-danger">*</span>
                                </label>
                                <select name="campaniaid" id="campaniaid" class="form-control select2" required style="width: 100%;">
                                    <option value="">-- Seleccione campaña --</option>
                                    @foreach($campanias as $c)
                                        <option value="{{ $c->campaniaid }}" {{ old('campaniaid') == $c->campaniaid ? 'selected' : '' }}>
                                            #{{ $c->campaniaid }} - {{ $c->titulo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Descripción --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="descripcion">
                                    <i class="fas fa-align-left"></i> Descripción <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="descripcion" class="form-control" 
                                       placeholder="Ingrese descripción" value="{{ old('descripcion') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Monto --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="monto">
                                    <i class="fas fa-money-bill-wave"></i> Monto (Bs) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" name="monto" class="form-control" 
                                       placeholder="0.00" value="{{ old('monto', 0) }}" required>
                                <small class="text-muted">Se ajustará con los detalles.</small>
                            </div>
                        </div>

                        {{-- Fecha --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fechaasignacion"><i class="fas fa-calendar-alt"></i> Fecha</label>
                                <input type="datetime-local" name="fechaasignacion" class="form-control" 
                                       value="{{ old('fechaasignacion') }}">
                            </div>
                        </div>

                        {{-- Comprobante --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="comprobante"><i class="fas fa-receipt"></i> Comprobante</label>
                                <input type="text" name="comprobante" class="form-control" 
                                       placeholder="Referencia" value="{{ old('comprobante') }}">
                            </div>
                        </div>
                    </div>

                    {{-- URL Imagen (Opcional) --}}
                    <input type="hidden" name="imagenurl" value="">

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Asignación
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLA DE ASIGNACIONES --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Listado de Asignaciones
                </h3>
                <div class="card-tools">
                    {{-- Buscador simple (opcional) --}}
                    <form action="{{ route('asignaciones.index') }}" method="GET" class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="q" class="form-control float-right" placeholder="Buscar..." value="{{ request('q') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Campaña</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Responsable</th>
                            <th class="text-center">Detalles</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asignaciones as $a)
                            <tr>
                                <td><span class="badge badge-info">{{ $a->asignacionid }}</span></td>
                                <td><strong>{{ $a->campania->titulo ?? 'N/D' }}</strong></td>
                                <td>{{ \Illuminate\Support\Str::limit($a->descripcion, 40) }}</td>
                                <td><span class="badge badge-success">{{ number_format($a->monto, 2) }} Bs</span></td>
                                <td>
                                    <small><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($a->fechaasignacion)->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <small><i class="fas fa-user"></i> {{ $a->usuario->email ?? 'N/D' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-secondary" title="Ítems">{{ $a->detalles_count }}</span>
                                    <span class="badge badge-warning" title="Donaciones">{{ $a->donaciones_count }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('asignaciones.show', $a->asignacionid) }}" class="btn btn-info btn-sm" title="Ver detalle"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('asignaciones.asignar', $a->asignacionid) }}" class="btn btn-warning btn-sm" title="Asignar fondos"><i class="fas fa-hand-holding-usd"></i></a>
                                        <a href="{{ route('asignaciones.edit', $a->asignacionid) }}" class="btn btn-secondary btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarAsignacion({{ $a->asignacionid }})" title="Eliminar"><i class="fas fa-trash"></i></button>
                                    </div>
                                    <form id="delete-form-{{ $a->asignacionid }}" action="{{ route('asignaciones.destroy', $a->asignacionid) }}" method="POST" style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay asignaciones registradas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $asignaciones->links() }}
            </div>
        </div>

    </div>
</section>

@push('scripts')
<script>
function eliminarAsignacion(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// Inicializar Select2
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Seleccione una opción'
    });
});
</script>
@endpush

@endsection