<<<<<<< HEAD
@extends('monitoreo.layout')
=======
@extends('layouts.app')
>>>>>>> origin/santiago

@section('title', 'Editar incendio')

@section('content')
<<<<<<< HEAD
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
=======
    <h1 style="margin:0;">Editar incendio</h1>
    <div class="card">
        <form method="POST" action="{{ route('incendios.update', $incendio) }}">
            @method('PUT')
            @include('monitoreo._form', ['submitLabel' => 'Actualizar incendio'])
        </form>
    </div>
@endsection
>>>>>>> origin/santiago
