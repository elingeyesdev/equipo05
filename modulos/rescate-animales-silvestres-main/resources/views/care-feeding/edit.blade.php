@extends('layouts.app')

@section('title', 'Editar alimentación — Rescate')
@section('subtitle', 'Actualizar datos de alimentación.')
@section('content_header_title', 'Alimentación')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-warning shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-utensils text-warning"></i> Editar alimentación</h3>
                        <a href="{{ route('rescate.care-feedings.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.care-feedings.update', $careFeeding->id) }}" role="form" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf
                            @include('care-feeding.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
