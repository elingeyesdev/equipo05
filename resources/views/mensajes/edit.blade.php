@extends('layouts.app')

@section('title', 'Editar mensaje')

@section('header')
    <h1>
        <i class="fas fa-edit icon-title"></i>
        Editar mensaje
    </h1>
    <p class="text-muted mb-0">
        Modifica el contenido o los datos del mensaje seleccionado.
    </p>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('mensajes.update', $mensaje->mensajeid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Columna izquierda --}}
                    <div class="col-md-6">
                        {{-- Remitente Fijo (No editable) --}}
                        <div class="form-group">
                            <label>Remitente</label>
                            <input type="text" class="form-control" 
                                   value="{{ optional($mensaje->remitente)->nombre }} {{ optional($mensaje->remitente)->apellido }}" 
                                   disabled>
                            {{-- Enviamos el ID oculto para que pase la validación del update --}}
                            <input type="hidden" name="usuarioid" value="{{ $mensaje->remitenteid }}">
                        </div>

                        <div class="form-group">
                            <label>Destinatario</label>
                            <select name="destinatarioid" class="form-control select2">
                                <option value="">Sin destinatario específico</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->usuarioid }}"
                                        {{ old('destinatarioid', $mensaje->destinatarioid) == $u->usuarioid ? 'selected' : '' }}>
                                        {{ $u->nombre }} {{ $u->apellido }} — {{ $u->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('destinatarioid') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- Columna derecha --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Asunto <span class="text-danger">*</span></label>
                            <input type="text" name="asunto" class="form-control"
                                   value="{{ old('asunto', $mensaje->asunto) }}" required>
                            @error('asunto') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Fecha de envío</label>
                            <input type="datetime-local" name="fechaenvio" class="form-control"
                                   value="{{ old('fechaenvio', \Carbon\Carbon::parse($mensaje->fechaenvio)->format('Y-m-d\TH:i')) }}">
                            @error('fechaenvio') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group mt-4">
                            <div class="custom-control custom-checkbox mb-2">
                                <input class="custom-control-input" type="checkbox" name="leido" id="leidoCheck" 
                                       value="1" {{ old('leido', $mensaje->leido) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="leidoCheck">Marcar como leído</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="respondido" id="respCheck" 
                                       value="1" {{ old('respondido', $mensaje->respondido) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="respCheck">Marcar como respondido</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label>Contenido</label>
                    <textarea name="contenido" rows="5" class="form-control" required>{{ old('contenido', $mensaje->contenido) }}</textarea>
                    @error('contenido') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('mensajes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar cambios
                    </button>
                </div>
            </form>
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