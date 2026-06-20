@extends('layouts.app')

@section('title', 'Editar cuidado — Rescate')
@section('subtitle', 'Actualizar datos del cuidado.')
@section('content_header_title', 'Cuidados registrados')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-warning shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-hand-holding-medical text-warning"></i> Editar cuidado</h3>
                        <a href="{{ route('rescate.cares.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.cares.update', $care->id) }}" role="form" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf
                            @include('care.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
