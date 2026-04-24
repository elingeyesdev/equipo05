@extends('layouts.app')

@section('title', 'Reasignar donación')

@section('header')
    <h1>
        <i class="fas fa-random icon-title"></i>
        Reasignar donación a otra campaña
    </h1>
    <p class="text-muted mb-0">
        Mueve el monto de esta donación de su campaña actual a otra campaña activa.
    </p>
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-2">Resumen de la donación</h5>
            <div class="row">
                <div class="col-md-4">
                    <p class="mb-1"><strong>ID:</strong> #{{ $donacion->donacionid }}</p>
                    <p class="mb-1"><strong>Monto:</strong> Bs {{ number_format($donacion->monto,2,',','.') }}</p>
                    <p class="mb-0"><strong>Tipo:</strong> {{ $donacion->tipodonacion }}</p>
                </div>
                <div class="col-md-4">
                    @php $usuario = optional($donacion->usuario); @endphp
                    <p class="mb-1"><strong>Donante:</strong>
                        @if($donacion->esanonima)
                            Donación anónima
                        @else
                            {{ $usuario->nombre }} {{ $usuario->apellido }}
                        @endif
                    </p>
                    <p class="mb-0 text-muted">
                        {{ $usuario->email ?? '' }}
                    </p>
                </div>
                <div class="col-md-4">
                    @php $campaniaActual = optional($donacion->campania); @endphp
                    <p class="mb-1"><strong>Campaña actual:</strong> {{ $campaniaActual->titulo ?? '—' }}</p>
                    <p class="mb-0 text-muted">
                        Meta: Bs {{ $campaniaActual ? number_format($campaniaActual->metarecaudacion,2,',','.') : '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('donaciones.reasignar', $donacion->donacionid) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Nueva campaña</label>
                    <select name="campaniaid" class="form-control" required>
                        <option value="">Seleccione una campaña activa</option>
                        @foreach($campanias as $c)
                            <option value="{{ $c->campaniaid }}">
                                {{ $c->titulo }} (Meta: Bs {{ number_format($c->metarecaudacion,2,',','.') }},
                                Recaudado: Bs {{ number_format($c->montorecaudado,2,',','.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('campaniaid') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Esta acción actualizará los totales recaudados en ambas campañas
                    (la anterior y la nueva).
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('donaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-random mr-1"></i> Confirmar reasignación
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
