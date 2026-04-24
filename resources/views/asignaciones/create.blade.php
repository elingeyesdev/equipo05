@extends('layouts.app')

@section('title', 'Nueva Asignación')

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Nueva Asignación</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('asignaciones.index') }}">Asignaciones</a></li>
                <li class="breadcrumb-item active">Nueva</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Formulario de Nueva Asignación</h3>
                </div>

                <form action="{{ route('asignaciones.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        
                        {{-- FILA 1: Campaña --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="campaniaid">
                                        <i class="fas fa-bullhorn"></i> Campaña <span class="text-danger">*</span>
                                    </label>
                                    <select name="campaniaid" id="campaniaid" class="form-control select2" required>
                                        <option value="">-- Seleccione una campaña --</option>
                                        @foreach($campanias as $c)
                                            <option value="{{ $c->campaniaid }}" @selected(old('campaniaid') == $c->campaniaid)>
                                                #{{ $c->campaniaid }} - {{ $c->titulo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('campaniaid')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- FILA 2: Descripción --}}
                        <div class="form-group">
                            <label for="descripcion">
                                <i class="fas fa-align-left"></i> Descripción <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="descripcion" 
                                   id="descripcion" 
                                   class="form-control @error('descripcion') is-invalid @enderror" 
                                   value="{{ old('descripcion') }}" 
                                   placeholder="Ingrese la descripción de la asignación"
                                   required>
                            @error('descripcion')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- FILA 3: Monto y Fecha --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monto">
                                        <i class="fas fa-money-bill-wave"></i> Monto Estimado (Bs) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Bs</span>
                                        </div>
                                        <input type="number" 
                                               step="0.01" 
                                               name="monto" 
                                               id="monto" 
                                               class="form-control @error('monto') is-invalid @enderror" 
                                               value="{{ old('monto') }}" 
                                               placeholder="0.00"
                                               required>
                                    </div>
                                    @error('monto')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Este monto se ajustará automáticamente al agregar detalles.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fechaasignacion">
                                        <i class="fas fa-calendar-alt"></i> Fecha de Asignación
                                    </label>
                                    <input type="datetime-local" 
                                           name="fechaasignacion" 
                                           id="fechaasignacion" 
                                           class="form-control @error('fechaasignacion') is-invalid @enderror" 
                                           value="{{ old('fechaasignacion') }}">
                                    @error('fechaasignacion')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- FILA 4: Comprobante e Imagen --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="comprobante">
                                        <i class="fas fa-receipt"></i> Comprobante / Referencia
                                    </label>
                                    <input type="text" 
                                           name="comprobante" 
                                           id="comprobante" 
                                           class="form-control @error('comprobante') is-invalid @enderror" 
                                           value="{{ old('comprobante') }}"
                                           placeholder="Ej: Factura #1234">
                                    @error('comprobante')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="imagenurl">URL de Imagen (Opcional)</label>
                                    <input type="url" 
                                           name="imagenurl" 
                                           id="imagenurl" 
                                           class="form-control @error('imagenurl') is-invalid @enderror" 
                                           value="{{ old('imagenurl') }}"
                                           placeholder="https://...">
                                    @error('imagenurl')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Crear Asignación
                        </button>
                        <a href="{{ route('asignaciones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
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
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Seleccione una opción'
        });
    });
</script>
@endpush