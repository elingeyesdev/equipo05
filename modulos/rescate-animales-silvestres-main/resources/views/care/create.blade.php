@extends('layouts.app')

@section('title', 'Nuevo registro de cuidado — Rescate')
@section('subtitle', 'Registrar cuidado de un animal.')
@section('content_header_title', 'Cuidados registrados')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-hand-holding-medical text-success"></i> Nuevo cuidado</h3>
                        <a href="{{ route('rescate.cares.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.cares.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('care.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
