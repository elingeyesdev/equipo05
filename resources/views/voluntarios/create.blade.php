@extends('layouts.app')

@section('title', 'Registrar voluntario')

@section('content')
    <div class="row">
        <h1 style="margin:0;">Registrar voluntario</h1>
        <a class="btn btn-light" href="{{ route('voluntarios.index') }}">Volver a voluntarios</a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('voluntarios.store') }}">
            @csrf

            <div class="field">
                <label for="nombre">Nombre</label>
                <input id="nombre" name="nombre" type="text" value="{{ old('nombre') }}" required>
                @error('nombre') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="apellido">Apellido</label>
                <input id="apellido" name="apellido" type="text" value="{{ old('apellido') }}" required>
                @error('apellido') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="telefono">Telefono</label>
                <input id="telefono" name="telefono" type="text" value="{{ old('telefono') }}">
                @error('telefono') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}">
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <option value="activo" @selected(old('estado', 'activo') === 'activo')>Activo</option>
                    <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                </select>
                @error('estado') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="row" style="margin-top:.5rem;">
                <button class="btn" type="submit">Guardar voluntario</button>
                <a class="btn btn-light" href="{{ route('voluntarios.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
