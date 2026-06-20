@extends('layouts.app')

@section('title', 'Tipos de incidente — Rescate')
@section('subtitle', 'Catálogo para clasificar hallazgos y reportes de campo.')
@section('content_header_title', 'Tipos de incidente')
@section('content_header_subtitle', 'Catálogo')

@section('content_body')
    
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: .5rem;">
                            <div>
                                <a href="{{ route('rescate.incident-types.create') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Nuevo tipo
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nombre</th>
                                        <th>Riesgo</th>
                                        <th>Activo</th>
                                        <th style="width: 220px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $riskMap = ['Bajo', 'Medio', 'Alto']; @endphp
                                    @forelse ($incidentTypes as $incidentType)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $incidentType->nombre }}</td>
                                            <td>{{ $riskMap[(int) ($incidentType->riesgo ?? 0)] ?? '—' }}</td>
                                            <td>{{ (int) $incidentType->activo === 1 ? 'Sí' : 'No' }}</td>
                                            <td>
                                                <form action="{{ route('rescate.incident-types.destroy', $incidentType->id) }}" method="POST" class="d-inline-flex flex-wrap" style="gap: .25rem;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('rescate.incident-types.show', $incidentType->id) }}"><i class="fa fa-fw fa-eye"></i> Ver</a>
                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('rescate.incident-types.edit', $incidentType->id) }}"><i class="fa fa-fw fa-edit"></i> Editar</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                Sin tipos de incidente.
                                                <a href="{{ route('rescate.incident-types.create') }}" class="d-inline-block mt-2">Registrar uno</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $incidentTypes->withQueryString()->links() !!}
            </div>
        </div>
@endsection
