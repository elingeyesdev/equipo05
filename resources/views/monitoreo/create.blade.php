@extends('monitoreo.layout')

@section('title', 'Registrar incendio')

@section('content')
    <div class="wrap form-page">
        <a class="back" href="{{ route('home') }}">← Volver al monitoreo</a>
        <h1>Registrar incendio</h1>
        <p style="color:var(--text-muted);margin:0 0 1.5rem;max-width:50ch;font-size:0.95rem">
            Completá los datos y elegí la ubicación en el mapa haciendo clic o moviendo el marcador.
        </p>

        @if ($errors->any())
            <div class="alert alert-error" role="alert">
                Revisá los campos marcados abajo.
            </div>
        @endif

        @include('monitoreo._form', [
            'action' => route('incendios.store'),
            'method' => 'POST',
            'incendio' => null,
            'submitLabel' => 'Registrar incendio',
        ])
    </div>
@endsection

@push('scripts')
    @include('monitoreo._map_picker_script')
@endpush
