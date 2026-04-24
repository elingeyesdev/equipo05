@extends('layouts.app')

@section('title', 'Nuevo estado')

@section('header')
    <h1>
        <i class="fas fa-flag icon-title"></i>
        Nuevo estado
    </h1>
    <p class="text-muted mb-0">
        Selecciona un estado predefinido para las donaciones.
    </p>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('estados.store') }}" method="POST">
                @csrf

                @php
                    $opciones = [
                        'Pendiente'  => 'La donación está registrada pero aún no se ha confirmado',
                        'Confirmada' => 'La donación ha sido confirmada y está disponible',
                        'Asignada'   => 'La donación ha sido asignada a un gasto o asignación',
                        'Utilizada'  => 'La donación ya fue utilizada parcialmente o totalmente',
                        'Cancelada'  => 'La donación fue anulada o revertida'
                    ];
                @endphp

                <div class="form-group">
                    <label>Nombre del estado</label>
                    <select name="nombre" class="form-control" required>
                        <option value="">Seleccione un estado...</option>

                        @foreach($opciones as $nombre => $desc)
                            <option value="{{ $nombre }}"
                                {{ old('nombre') == $nombre ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Descripción (automática, editable)</label>
                    <textarea name="descripcion" rows="3" class="form-control">
                        {{ old('descripcion') }}
                    </textarea>
                    @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
                    <small class="form-text text-muted">
                        Puedes dejar esta descripción tal como está o editarla.
                    </small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('estados.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar estado
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script para autocompletar la descripción según el estado --}}
    <script>
        document.querySelector('select[name="nombre"]').addEventListener('change', function () {
            let descField = document.querySelector('textarea[name="descripcion"]');

            const descripciones = {
                'Pendiente': 'La donación está registrada pero aún no se ha confirmado',
                'Confirmada': 'La donación ha sido confirmada y está disponible',
                'Asignada': 'La donación ha sido asignada a un gasto o asignación',
                'Utilizada': 'La donación ya fue utilizada parcialmente o totalmente',
                'Cancelada': 'La donación fue anulada o revertida',
            };

            descField.value = descripciones[this.value] || '';
        });
    </script>
@endsection
