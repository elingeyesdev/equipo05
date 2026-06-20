@extends('layouts.app')

@section('title', 'Nuevo rescatista — Rescate')
@section('subtitle', 'Registrar postulación de rescatista.')
@section('content_header_title', 'Rescatistas')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-hands-helping text-success"></i> Nuevo rescatista</h3>
                        <a href="{{ route('rescate.rescuers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.rescuers.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('rescuer.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
