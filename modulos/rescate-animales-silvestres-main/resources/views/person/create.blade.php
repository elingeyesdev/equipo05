@extends('layouts.app')

@section('title', 'Nueva persona — Rescate')
@section('subtitle', 'Alta en el directorio del módulo.')
@section('content_header_title', 'Personas')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-user-plus text-success"></i> Nueva persona</h3>
                        <a href="{{ route('rescate.people.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.people.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('person.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @include('partials.leaflet')
@endsection
