@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Card 1: Bienvenido -->
    <div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeIn" style="border-top: 4px solid #28a745; border-radius: 8px;">
        <div class="card-header bg-white py-3">
            <h5 class="m-0 font-weight-bold text-dark d-flex align-items-center">
                <i class="fas fa-leaf text-success mr-2"></i> Bienvenido al Sistema de Gestión - Alas Chiquitanas
            </h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-9 col-sm-8">
                    <h5 class="text-success mb-3" style="font-weight: 600; font-size: 1.25rem;">
                        <i class="fas fa-leaf mr-1"></i> Organización Voluntaria de Conservación Ambiental
                    </h5>
                    <p class="text-secondary" style="font-size: 1.05rem; line-height: 1.6;">
                        Este dashboard es el centro de operaciones para la gestión de actividades de conservación, monitoreo ambiental y respuesta ante emergencias en la Chiquitania.
                    </p>
                    <p class="text-secondary" style="font-size: 1.05rem; line-height: 1.6; margin-bottom: 0;">
                        Desde aquí podrás acceder a todas las herramientas necesarias para documentar, reportar y coordinar nuestras acciones de protección de la biodiversidad y los ecosistemas locales.
                    </p>
                </div>
                <div class="col-md-3 col-sm-4 text-center d-none d-sm-block">
                    <i class="fas fa-tree text-success" style="font-size: 7.5rem; color: #d4edda !important;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Vista Rápida -->
    <div class="card shadow-sm border-0 animate__animated animate__fadeIn animate__delay-1s" style="border-radius: 8px; overflow: hidden;">
        <div class="card-header py-3" style="background-color: #545b62; color: #fff;">
            <h5 class="m-0 font-weight-bold d-flex align-items-center">
                <i class="fas fa-chart-line mr-2"></i> Vista Rápida del Sistema
            </h5>
        </div>
        <div class="card-body py-4">
            <div class="row text-center">
                <div class="col-md-3 col-6 quick-stat-col" style="border-right: 1px solid #e9ecef;">
                    <i class="fas fa-users text-success fa-2x mb-2"></i>
                    <h3 class="m-0 font-weight-bold text-dark">{{ $voluntariosActivos > 0 ? $voluntariosActivos : '--' }}</h3>
                    <div class="text-uppercase text-muted font-weight-bold mt-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Voluntarios Activos</div>
                </div>
                <div class="col-md-3 col-6 quick-stat-col" style="border-right: 1px solid #e9ecef;">
                    <i class="fas fa-handshake text-info fa-2x mb-2"></i>
                    <h3 class="m-0 font-weight-bold text-dark">{{ $comunariosApoyo > 0 ? $comunariosApoyo : '--' }}</h3>
                    <div class="text-uppercase text-muted font-weight-bold mt-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Comunarios de Apoyo</div>
                </div>
                <div class="col-md-3 col-6 quick-stat-col" style="border-right: 1px solid #e9ecef;">
                    <i class="fas fa-file-alt text-warning fa-2x mb-2"></i>
                    <h3 class="m-0 font-weight-bold text-dark">{{ $reportesEsteMes > 0 ? $reportesEsteMes : '--' }}</h3>
                    <div class="text-uppercase text-muted font-weight-bold mt-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Reportes Este Mes</div>
                </div>
                <div class="col-md-3 col-6 quick-stat-col">
                    <i class="fas fa-fire text-danger fa-2x mb-2"></i>
                    <h3 class="m-0 font-weight-bold text-dark">{{ $incendiosReportados > 0 ? $incendiosReportados : '--' }}</h3>
                    <div class="text-uppercase text-muted font-weight-bold mt-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Incendios Reportados</div>
                </div>
            </div>
            <div class="text-center text-muted mt-4" style="font-size: 0.85rem;">
                Los contadores se actualizarán automáticamente conforme uses el sistema
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 767.98px) {
        .quick-stat-col {
            border-right: none !important;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 1.2rem;
            margin-bottom: 1.2rem;
            max-width: 100%;
            flex: 0 0 100%;
        }
        .quick-stat-col:last-child {
            border-bottom: none !important;
            padding-bottom: 0;
            margin-bottom: 0;
        }
    }
</style>
@endsection
