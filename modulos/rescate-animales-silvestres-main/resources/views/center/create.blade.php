@extends('layouts.app')

@section('title', 'Nuevo centro — Rescate')
@section('subtitle', 'Alta de centro de custodia.')
@section('content_header_title', 'Centro')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-hospital text-success"></i> Nuevo centro</h3>
                        <a href="{{ route('rescate.centers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.centers.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('center.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection