@extends('layouts.app')

@section('title', 'Nuevo animal — Rescate')
@section('subtitle', 'Alta asociada a un reporte.')
@section('content_header_title', 'Animales')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-paw text-success"></i> Nuevo animal</h3>
                        <a href="{{ route('rescate.animals.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.animals.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('animal.form', [
                                'animal' => $animal ?? null,
                                'reports' => $reports ?? [],
                                'animalStatuses' => (\Modules\Rescate\Models\AnimalStatus::orderBy('nombre')->get()),
                            ])
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
