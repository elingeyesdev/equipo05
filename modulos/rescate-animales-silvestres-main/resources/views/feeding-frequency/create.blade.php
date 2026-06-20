@extends('layouts.app')

@section('title', 'Nuevo registro — Frecuencia de alimentación')
@section('subtitle', 'Alta en el catálogo del módulo rescate.')
@section('content_header_title', 'Frecuencia de alimentación')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-clock text-success"></i> Nuevo</h3>
                        <a href="{{ route('rescate.feeding-frequencies.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Ir al listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.feeding-frequencies.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('feeding-frequency.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
