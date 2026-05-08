@extends('layouts.app')

@section('title', 'Editar — Tipo de incidente')
@section('subtitle', 'Actualizar datos del catálogo.')
@section('content_header_title', 'Tipo de incidente')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-warning shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-exclamation-triangle text-warning"></i> Editar</h3>
                        <a href="{{ route('rescate.incident-types.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.incident-types.update', $incidentType->id) }}" role="form" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf
                            @include('incident-type.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
