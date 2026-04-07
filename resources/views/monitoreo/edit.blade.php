@extends('monitoreo.layout')

@section('title', 'Editar incendio')

@section('content')
    <div class="wrap form-page">
        <a class="back" href="{{ route('home') }}">← Volver al monitoreo</a>
        <h1>Editar incendio</h1>
        <p style="color:var(--text-muted);margin:0 0 1.5rem;max-width:50ch;font-size:0.95rem">
            Modificá los datos o la posición en el mapa. Los cambios se guardan al enviar el formulario.
        </p>

        @if ($errors->any())
            <div class="alert alert-error" role="alert">
                Revisá los campos marcados abajo.
            </div>
        @endif

        @include('monitoreo._form', [
            'action' => route('incendios.update', $incendio),
            'method' => 'PUT',
            'incendio' => $incendio,
            'submitLabel' => 'Guardar cambios',
        ])
    </div>
@endsection

@push('scripts')
    @include('monitoreo._map_picker_script')
@endpush
