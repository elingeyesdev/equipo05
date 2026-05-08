@extends('layouts.app')

@section('title', 'Nuevo registro — Porción de alimento')
@section('subtitle', 'Alta en el catálogo del módulo rescate.')
@section('content_header_title', 'Porción de alimento')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-balance-scale text-success"></i> Nuevo</h3>
                        <a href="{{ route('rescate.feeding-portions.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Ir al listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.feeding-portions.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('feeding-portion.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
