@extends('layouts.app')

@section('title', 'Editar — Tipo de tratamiento')
@section('subtitle', 'Actualizar datos del catálogo.')
@section('content_header_title', 'Tipo de tratamiento')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-warning shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-pills text-warning"></i> Editar</h3>
                        <a href="{{ route('rescate.treatment-types.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.treatment-types.update', $treatmentType->id) }}" role="form" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf
                            @include('treatment-type.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
