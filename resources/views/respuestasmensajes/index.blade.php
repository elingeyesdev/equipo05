@extends('layouts.app')

@section('title', 'Respuestas a mensajes')

@section('header')
    <h1>
        <i class="fas fa-reply-all icon-title"></i>
        Respuestas a mensajes
    </h1>
    <p class="text-muted mb-0">
        Gestión de todas las respuestas registradas en el sistema.
    </p>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle mr-1"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-list mr-1"></i> Listado de respuestas
            </h3>
            <a href="{{ route('respuestasmensajes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Nueva respuesta
            </a>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mensaje</th>
                        <th>Usuario</th>
                        <th>Contenido</th>
                        <th>Fecha</th>
                        <th class="text-right" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($respuestas as $r)
                        <tr>
                            <td>{{ $r->respuestaid }}</td>
                            <td>#{{ $r->mensajeid }}</td>
                            <td>
                                {{ optional($r->usuario)->nombre }}
                                {{ optional($r->usuario)->apellido }}<br>
                                <small class="text-muted">{{ optional($r->usuario)->email }}</small>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($r->contenido, 60) }}</td>
                            <td>{{ $r->fecharespuesta }}</td>
                            <td class="text-right">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('respuestasmensajes.edit', $r->respuestaid) }}"
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('respuestasmensajes.destroy', $r->respuestaid) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Eliminar esta respuesta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay respuestas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($respuestas, 'links'))
            <div class="card-footer">
                {{ $respuestas->links() }}
            </div>
        @endif
    </div>
@endsection
