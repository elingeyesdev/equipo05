@extends('layouts.app')

@section('title', 'Editar respuesta')

@section('header')
    <h1>
        <i class="fas fa-edit icon-title"></i>
        Editar respuesta
    </h1>
    <p class="text-muted mb-0">
        Modifica el contenido de una respuesta existente.
    </p>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('respuestasmensajes.update', $respuesta->respuestaid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Mensaje</label>
                    <select name="mensajeid" class="form-control" required>
                        @foreach($mensajes as $m)
                            <option value="{{ $m->mensajeid }}"
                                {{ old('mensajeid',$respuesta->mensajeid) == $m->mensajeid ? 'selected' : '' }}>
                                #{{ $m->mensajeid }} — {{ \Illuminate\Support\Str::limit($m->asunto,40) }}
                            </option>
                        @endforeach
                    </select>
                    @error('mensajeid') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Usuario que responde</label>
                    <select name="usuarioid" class="form-control" required>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->usuarioid }}"
                                {{ old('usuarioid',$respuesta->usuarioid) == $u->usuarioid ? 'selected' : '' }}>
                                {{ $u->nombre }} {{ $u->apellido }} — {{ $u->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('usuarioid') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Fecha de respuesta</label>
                    <input type="datetime-local" name="fecharespuesta" class="form-control"
                           value="{{ old('fecharespuesta', \Carbon\Carbon::parse($respuesta->fecharespuesta)->format('Y-m-d\TH:i')) }}">
                    @error('fecharespuesta') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Contenido</label>
                    <textarea name="contenido" rows="4" class="form-control">{{ old('contenido',$respuesta->contenido) }}</textarea>
                    @error('contenido') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('respuestasmensajes.index') }}" class="btn btn-secondary">
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
