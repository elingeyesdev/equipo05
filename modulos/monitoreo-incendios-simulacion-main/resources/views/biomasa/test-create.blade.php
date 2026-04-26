@extends('adminlte::page')

@section('content')
<div class="container">
    <h2>TEST - Crear Biomasa (Formulario Minimalista)</h2>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if ($message = Session::get('success'))
        <div class="alert alert-success">{{ $message }}</div>
    @endif
    
    @if ($message = Session::get('error'))
        <div class="alert alert-danger">{{ $message }}</div>
    @endif
    
    <form method="POST" action="{{ route('biomasas.store') }}">
        @csrf
        
        <div class="form-group">
            <label>Tipo de Biomasa *</label>
            <select name="tipo_biomasa_id" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($tipoBiomasas as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->tipo_biomasa }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label>Ubicación</label>
            <input type="text" name="ubicacion" class="form-control" value="Test Location">
        </div>
        
        <div class="form-group">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control">Test biomasa creada desde formulario minimalista</textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">GUARDAR TEST</button>
        <a href="{{ route('biomasas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
