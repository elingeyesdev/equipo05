@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Editar Detalle</h2>

    <form action="{{ route('detallesasignacion.update', $detalle->detalleid) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Asignación:</label>
            <select name="asignacionid" class="form-select" required>
                @foreach($asignaciones as $a)
                    <option value="{{ $a->asignacionid }}" @selected(old('asignacionid', $detalle->asignacionid) == $a->asignacionid)>
                        {{ $a->asignacionid }} — {{ $a->descripcion }}
                    </option>
                @endforeach
            </select>
            @error('asignacionid') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="row">
            <div class="mb-3 col-md-6">
                <label>Concepto:</label>
                <input type="text" name="concepto" class="form-control" value="{{ old('concepto', $detalle->concepto) }}" required>
                @error('concepto') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-3 col-md-3">
                <label>Cantidad:</label>
                <input type="number" name="cantidad" class="form-control" value="{{ old('cantidad', $detalle->cantidad) }}" required>
                @error('cantidad') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-3 col-md-3">
                <label>Precio Unitario:</label>
                <input type="number" step="0.01" name="preciounitario" class="form-control" value="{{ old('preciounitario', $detalle->preciounitario) }}" required>
                @error('preciounitario') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label>Imagen URL:</label>
            <input type="text" name="imagenurl" class="form-control" value="{{ old('imagenurl', $detalle->imagenurl) }}">
            @error('imagenurl') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button class="btn btn-primary">Actualizar</button>
        <a href="{{ route('detallesasignacion.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
