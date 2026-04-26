@extends('layouts.app')

@section('subtitle', 'Simulaciones')
@section('content_header_title', 'Simulaciones de Incendios')
@section('content_header_subtitle', '- Listado')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if ($message = Session::get('success'))
                    <x-adminlte-alert theme="success" dismissable>
                        {{ $message }}
                    </x-adminlte-alert>
                @endif

                <x-adminlte-card title="Simulaciones" theme="orange" icon="fas fa-play-circle">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('simulaciones.simulator') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-fire"></i> Simulador Avanzado
                        </a>
                    </x-slot>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nombre</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Focos Activos</th>
                                    <th style="width: 180px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($simulaciones as $simulacione)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $simulacione->nombre }}</td>
                                        <td>{{ optional($simulacione->fecha)->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $simulacione->estado === 'activa' ? 'success' : 'secondary' }}">
                                                {{ $simulacione->estado }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-danger">{{ $simulacione->focos_activos }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('simulaciones.show', $simulacione->id) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('simulaciones.pdf', $simulacione->id) }}" class="btn btn-primary btn-sm" title="Ver Informe" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <a href="{{ route('simulaciones.edit', $simulacione->id) }}" class="btn btn-success btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('simulaciones.destroy', $simulacione->id) }}" method="POST" style="display: inline;" 
                                                    onsubmit="return confirm('¿Está seguro de eliminar esta simulación?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {!! $simulaciones->withQueryString()->links() !!}
                    </div>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
