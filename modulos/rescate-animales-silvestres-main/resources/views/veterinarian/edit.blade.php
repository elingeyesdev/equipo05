@extends('layouts.app')

@section('title', 'Editar veterinario — Rescate')
@section('subtitle', 'Actualizar datos del veterinario.')
@section('content_header_title', 'Veterinarios')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-warning shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-user-md text-warning"></i> Editar veterinario</h3>
                        <a href="{{ route('rescate.veterinarians.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.veterinarians.update', $veterinarian->id) }}" role="form" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf
                            @include('veterinarian.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
