@extends('layouts.app')

@section('title', 'Editar hoja de vida — Rescate')
@section('subtitle', 'Actualizar datos registrados.')
@section('content_header_title', 'Hojas de vida')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-warning shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-file-medical text-warning"></i> Editar hoja de vida</h3>
                        <a href="{{ route('rescate.animal-files.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <div class="font-weight-bold mb-1">{{ __('No se pudo actualizar. Revise los siguientes errores:') }}</div>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('rescate.animal-files.update', $animalFile->id) }}" role="form" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf
                            @include('animal-file.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
