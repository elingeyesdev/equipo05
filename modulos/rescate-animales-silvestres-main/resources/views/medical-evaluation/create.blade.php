@extends('layouts.app')

@section('title', 'Nueva evaluación médica — Rescate')
@section('subtitle', 'Registrar evaluación clínica.')
@section('content_header_title', 'Evaluaciones médicas')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-notes-medical text-success"></i> Nueva evaluación</h3>
                        <a href="{{ route('rescate.medical-evaluations.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.medical-evaluations.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('medical-evaluation.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
