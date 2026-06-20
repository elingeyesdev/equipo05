@extends('layouts.app')

@section('title', 'Nueva alimentación — Rescate')
@section('subtitle', 'Asociar tipo y frecuencia de alimentación a un cuidado.')
@section('content_header_title', 'Alimentación')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-utensils text-success"></i> Nueva alimentación</h3>
                        <a href="{{ route('rescate.care-feedings.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.care-feedings.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('care-feeding.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
