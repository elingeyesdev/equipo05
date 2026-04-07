@extends('layouts.app')

@section('title', 'Registrar incendio')

@section('content')
    <h1 style="margin:0;">Registrar incendio</h1>
    <div class="card">
        <form method="POST" action="{{ route('incendios.store') }}">
            @include('monitoreo._form', ['submitLabel' => 'Guardar incendio'])
        </form>
    </div>
@endsection