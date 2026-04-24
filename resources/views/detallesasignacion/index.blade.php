@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Detalles de Asignación</h2>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <a href="{{ route('detallesasignacion.create') }}" class="btn btn-primary mb-3">➕ Nuevo Detalle</a>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Asignación</th>
                <th>Concepto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detalles as $d)
                <tr>
                    <td>{{ $d->detalleid }}</td>
                    <td>{{ optional($d->asignacion)->descripcion }}</td>
                    <td>{{ $d->concepto }}</td>
                    <td>{{ $d->cantidad }}</td>
                    <td>{{ $d->preciounitario }}</td>
                    <td>{{ $d->imagenurl }}</td>
                    <td>
                        <a href="{{ route('detallesasignacion.edit', $d->detalleid) }}" class="btn btn-warning btn-sm">✏️ Editar</a>
                        <form action="{{ route('detallesasignacion.destroy', $d->detalleid) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar detalle?')">🗑 Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No hay detalles</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
