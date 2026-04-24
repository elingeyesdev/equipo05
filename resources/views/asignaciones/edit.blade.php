@extends('layouts.app')

@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp

@section('title', 'Editar Asignación')

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Editar Asignación</h1>
            <small class="text-muted">#{{ $asignacion->asignacionid }}</small>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('asignaciones.index') }}">Asignaciones</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Formulario de Edición</h3>
            </div>

            <form action="{{ route('asignaciones.update', $asignacion->asignacionid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">

                    {{-- Campaña --}}
                    <div class="form-group">
                        <label for="campaniaid">Campaña <span class="text-danger">*</span></label>
                        <select name="campaniaid" id="campaniaid" class="form-control select2 @error('campaniaid') is-invalid @enderror" required>
                            <option value="">-- Seleccione una campaña --</option>
                            @foreach($campanias as $c)
                                <option value="{{ $c->campaniaid }}"
                                    @selected(old('campaniaid', $asignacion->campaniaid) == $c->campaniaid)>
                                    {{ $c->titulo }}
                                </option>
                            @endforeach
                        </select>
                        @error('campaniaid') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="form-group">
                        <label for="descripcion">Descripción <span class="text-danger">*</span></label>
                        <input type="text" name="descripcion" id="descripcion"
                               class="form-control @error('descripcion') is-invalid @enderror"
                               value="{{ old('descripcion', $asignacion->descripcion) }}"
                               placeholder="Ingrese la descripción" required>
                        @error('descripcion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- Monto y Fecha --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="monto">Monto <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">Bs</span></div>
                                    <input type="number" step="0.01" name="monto" id="monto"
                                           class="form-control @error('monto') is-invalid @enderror"
                                           value="{{ old('monto', $asignacion->monto) }}" required>
                                </div>
                                @error('monto') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fechaasignacion">Fecha de Asignación</label>
                                <input type="datetime-local" name="fechaasignacion" id="fechaasignacion"
                                       class="form-control @error('fechaasignacion') is-invalid @enderror"
                                       value="{{ old('fechaasignacion',
                                            $asignacion->fechaasignacion
                                                ? Carbon::parse($asignacion->fechaasignacion)->format('Y-m-d\TH:i')
                                                : null
                                       ) }}">
                                @error('fechaasignacion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Comprobante e Imagen --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="comprobante">Comprobante</label>
                                <input type="text" name="comprobante" id="comprobante"
                                       class="form-control @error('comprobante') is-invalid @enderror"
                                       value="{{ old('comprobante', $asignacion->comprobante) }}"
                                       placeholder="Número o referencia">
                                @error('comprobante') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="imagenurl">URL de Imagen</label>
                                <input type="url" name="imagenurl" id="imagenurl"
                                       class="form-control @error('imagenurl') is-invalid @enderror"
                                       value="{{ old('imagenurl', $asignacion->imagenurl) }}">
                                @error('imagenurl') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    {{-- Input oculto de usuario para mantenerlo si no se cambia (aunque el controlador no lo pide en update normalmente, es bueno tenerlo presente si cambiaras la lógica) --}}
                    <input type="hidden" name="usuarioid" value="{{ $asignacion->usuarioid }}">

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="{{ route('asignaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap4' });
    });
</script>
@endpush