@extends('layouts.app')

@section('title', 'Monitoreo de Incendios')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Monitoreo de Incendios</h3>
    </div>
    <div class="card-body">
        <p class="mb-2">
            Este modulo ya fue incorporado al repositorio principal y esta en proceso de adaptacion para operar con autenticacion unica.
        </p>
        <ul>
            <li>Codigo fuente: <code>modulos/monitoreo-incendios-simulacion-main</code></li>
            <li>Prefijo objetivo web: <code>/incendios/*</code></li>
            <li>Prefijo objetivo API: <code>/api/incendios/*</code></li>
        </ul>
    </div>
</div>
@endsection

