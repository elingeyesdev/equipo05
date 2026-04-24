@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <h2>Asignación #{{ $asignacion->asignacionid }}</h2>

    <div class="card mb-3">
        <div class="card-body">

            <h4>{{ $asignacion->descripcion }}</h4>

            <p><strong>Campaña:</strong> {{ $asignacion->campania->titulo }}</p>
            <p><strong>Monto total:</strong> {{ number_format($asignacion->monto,2) }} Bs</p>
            <p><strong>Fecha:</strong> {{ $asignacion->fechaasignacion }}</p>
            <p><strong>Registrado por:</strong> {{ $asignacion->usuario->email }}</p>

        </div>
    </div>

    {{-- DETALLES --}}
    <h4>Detalles</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>

            @foreach($asignacion->detalles as $d)
            <tr>
                <td>{{ $d->concepto }}</td>
                <td>{{ $d->cantidad }}</td>
                <td>{{ number_format($d->preciounitario,2) }} Bs</td>
                <td>{{ number_format($d->cantidad * $d->preciounitario,2) }} Bs</td>
            </tr>
            @endforeach

        </tbody>
    </table>

    {{-- DONACIONES ASIGNADAS --}}
    <h4 class="mt-4">Donaciones asignadas</h4>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID Donación</th>
                <th>Monto Asignado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($donacionesAsignadas as $dd)
            <tr>
                <td>{{ $dd->donacionid }}</td>
                <td>{{ number_format($dd->montoasignado,2) }} Bs</td>
                <td>{{ $dd->fechaasignacion }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('asignaciones.asignarDonacionForm', $asignacion->asignacionid) }}" 
       class="btn btn-primary">Asignar Donación</a>

</div>

@endsection
