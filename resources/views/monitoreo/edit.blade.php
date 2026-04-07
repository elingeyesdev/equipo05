@extends('layouts.app')

@section('title', 'Editar incendio')

@section('content')
    <h1 style="margin:0;">Editar incendio</h1>
    <div class="card">
        <form method="POST" action="{{ route('incendios.update', $incendio) }}">
            @method('PUT')
            @include('monitoreo._form', ['submitLabel' => 'Actualizar incendio'])
        </form>
    </div>
@endsection