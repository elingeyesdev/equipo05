@extends('layouts.app')

@section('title', 'Nuevo mensaje')

@section('header')
    <h1>
        <i class="fas fa-paper-plane icon-title"></i>
        Nuevo mensaje
    </h1>
    <p class="text-muted mb-0">
        Redacta un nuevo mensaje para cualquier usuario del sistema.
    </p>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('mensajes.store') }}" method="POST">
                @csrf

                <div class="row">
                    {{-- Columna izquierda: Destinatario --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Destinatario (opcional)</label>
                            <select name="destinatarioid" class="form-control select2">
                                <option value="">Sin destinatario (Anuncio general)</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->usuarioid }}" {{ old('destinatarioid') == $u->usuarioid ? 'selected' : '' }}>
                                        {{ $u->nombre }} {{ $u->apellido }} — {{ $u->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('destinatarioid') <small class="text-danger">{{ $message }}</small> @enderror
                            <small class="form-text text-muted">
                                Si lo dejas vacío, el mensaje no tendrá un destinatario específico.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Asunto <span class="text-danger">*</span></label>
                            <input type="text" name="asunto" class="form-control"
                                   value="{{ old('asunto') }}" placeholder="Asunto del mensaje" required>
                            @error('asunto') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- Columna derecha: Fechas y Opciones --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de envío</label>
                            <input type="datetime-local" name="fechaenvio" class="form-control"
                                   value="{{ old('fechaenvio') }}">
                            @error('fechaenvio') <small class="text-danger">{{ $message }}</small> @enderror
                            <small class="form-text text-muted">
                                Dejar vacío para usar la fecha/hora actual.
                            </small>
                        </div>

                        <div class="form-group mt-4">
                            <div class="custom-control custom-checkbox mb-2">
                                <input class="custom-control-input" type="checkbox" name="leido" id="leidoCheck" value="1" {{ old('leido') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="leidoCheck">Marcar como leído</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="respondido" id="respCheck" value="1" {{ old('respondido') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="respCheck">Marcar como respondido</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contenido --}}
                <div class="form-group mt-3">
                    <label>Contenido del mensaje <span class="text-danger">*</span></label>
                    <textarea name="contenido" rows="6" class="form-control"
                              placeholder="Escribe aquí el contenido..." required>{{ old('contenido') }}</textarea>
                    @error('contenido') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('mensajes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i> Enviar mensaje
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