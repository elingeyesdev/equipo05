<?php

declare(strict_types=1);

/**
 * One-shot: rescate CRUD polish (method_field, form actions, simple catalog shells).
 * php scripts/_rescate_polish_forms_once.php
 */

$views = dirname(__DIR__).'/modulos/rescate-animales-silvestres-main/resources/views';

// --- 1) @method('PATCH') ---
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($views, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
    if (! $f->isFile() || ! str_ends_with($f->getFilename(), '.blade.php')) {
        continue;
    }
    $p = $f->getPathname();
    $c = file_get_contents($p);
    $n = str_replace("{{ method_field('PATCH') }}", "@method('PATCH')", $c);
    if ($n !== $c) {
        file_put_contents($p, $n);
    }
}

// --- 2) Form footers: Submit -> Guardar + Cancelar ---
$footerOld = <<<'BLADE'
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
BLADE;

$formRoutes = [
    'animal-status/form.blade.php' => 'rescate.animal-statuses.index',
    'animal-condition/form.blade.php' => 'rescate.animal-conditions.index',
    'care-type/form.blade.php' => 'rescate.care-types.index',
    'center/form.blade.php' => 'rescate.centers.index',
    'feeding-frequency/form.blade.php' => 'rescate.feeding-frequencies.index',
    'feeding-portion/form.blade.php' => 'rescate.feeding-portions.index',
    'feeding-type/form.blade.php' => 'rescate.feeding-types.index',
    'incident-type/form.blade.php' => 'rescate.incident-types.index',
    'medical-evaluation/form.blade.php' => 'rescate.medical-evaluations.index',
    'release/form.blade.php' => 'rescate.releases.index',
    'report/form.blade.php' => 'rescate.reports.index',
    'rescuer/form.blade.php' => 'rescate.rescuers.index',
    'species/form.blade.php' => 'rescate.species.index',
    'transfer/form.blade.php' => 'rescate.transfers.index',
    'treatment-type/form.blade.php' => 'rescate.treatment-types.index',
    'user/form.blade.php' => 'rescate.users.index',
    'veterinarian/form.blade.php' => 'rescate.veterinarians.index',
    'care-feeding/form.blade.php' => 'rescate.care-feedings.index',
    'care/form.blade.php' => 'rescate.cares.index',
];

foreach ($formRoutes as $rel => $routeName) {
    $p = $views.'/'.$rel;
    if (! is_file($p)) {
        fwrite(STDERR, "Skip missing form: {$rel}\n");

        continue;
    }
    $c = file_get_contents($p);
    if (! str_contains($c, "{{ __('Submit') }}")) {
        continue;
    }
    $footerNew = <<<BLADE
    <div class="col-md-12 mt-3 d-flex flex-wrap align-items-center" style="gap: .5rem;">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
        <a href="{{ route('{$routeName}') }}" class="btn btn-secondary">Cancelar</a>
    </div>
BLADE;

    // Liberación: mantiene id submit_wrap + oculto inicial
    if ($rel === 'release/form.blade.php') {
        $footerOldRel = <<<'BLADE'
    <div class="col-md-12 mt20 mt-2" id="submit_wrap" style="display:none;">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
BLADE;
        $footerNewRel = <<<BLADE
    <div class="col-md-12 mt-3 d-flex flex-wrap align-items-center" id="submit_wrap" style="display:none; gap: .5rem;">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
        <a href="{{ route('{$routeName}') }}" class="btn btn-secondary">Cancelar</a>
    </div>
BLADE;
        $n = str_replace($footerOldRel, $footerNewRel, $c);
        if ($n !== $c) {
            file_put_contents($p, $n);
            fwrite(STDOUT, "Patched release form footer\n");
        }

        continue;
    }

    $n = str_replace($footerOld, $footerNew, $c);
    if ($n !== $c) {
        file_put_contents($p, $n);
        fwrite(STDOUT, "Patched form footer: {$rel}\n");
    }
}

// --- 3) Animal / animal-file guardar row ---
$animalForm = $views.'/animal/form.blade.php';
if (is_file($animalForm)) {
    $c = file_get_contents($animalForm);
    $old = <<<'BLADE'
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
        </div>
BLADE;
    $new = <<<'BLADE'
        <div class="mt-3 d-flex flex-wrap align-items-center" style="gap: .5rem;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            <a href="{{ route('rescate.animals.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
BLADE;
    $n = str_replace($old, $new, $c);
    if ($n !== $c) {
        file_put_contents($animalForm, $n);
        fwrite(STDOUT, "Patched animal/form\n");
    }
}

$afForm = $views.'/animal-file/form.blade.php';
if (is_file($afForm)) {
    $c = file_get_contents($afForm);
    $old = <<<'BLADE'
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
        </div>
BLADE;
    $new = <<<'BLADE'
        <div class="mt-3 d-flex flex-wrap align-items-center" style="gap: .5rem;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            <a href="{{ route('rescate.animal-files.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
BLADE;
    $n = str_replace($old, $new, $c);
    if ($n !== $c) {
        file_put_contents($afForm, $n);
        fwrite(STDOUT, "Patched animal-file/form\n");
    }
}

// --- 4) Person form: icon + español labels mínimos ---
$personForm = $views.'/person/form.blade.php';
if (is_file($personForm)) {
    $c = file_get_contents($personForm);
    $c = str_replace(
        '<button type="submit" class="btn btn-primary">{{ __(\'Guardar\') }}</button>',
        '<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>',
        $c
    );
    file_put_contents($personForm, $c);
    fwrite(STDOUT, "Patched person/form button icon\n");
}

/**
 * @param array{
 *   folder:string,
 *   routes:string,
 *   var:string,
 *   label:string,
 *   icon:string,
 *   extraFields?:list<string>
 * } $meta
 */
function writeSimpleCatalogCrud(string $views, array $meta): void
{
    $folder = $meta['folder'];
    $routes = $meta['routes'];
    $var = $meta['var'];
    $label = $meta['label'];
    $icon = $meta['icon'];

    $store = "rescate.{$routes}.store";
    $update = "rescate.{$routes}.update";
    $index = "rescate.{$routes}.index";
    $edit = "rescate.{$routes}.edit";

    $bv = '$'.$var;

    $create = <<<BLADE
@extends('layouts.app')

@section('title', 'Nuevo registro — {$label}')
@section('subtitle', 'Alta en el catálogo del módulo rescate.')
@section('content_header_title', '{$label}')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="{$icon} text-success"></i> Nuevo</h3>
                        <a href="{{ route('{$index}') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Ir al listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('{$store}') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('{$folder}.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection

BLADE;

    $editBlade = <<<BLADE
@extends('layouts.app')

@section('title', 'Editar — {$label}')
@section('subtitle', 'Actualizar datos del catálogo.')
@section('content_header_title', '{$label}')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-warning shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="{$icon} text-warning"></i> Editar</h3>
                        <a href="{{ route('{$index}') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('{$update}', {$bv}->id) }}" role="form" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf
                            @include('{$folder}.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection

BLADE;

    $bodyExtra = '';
    if (! empty($meta['extraFields'])) {
        foreach ($meta['extraFields'] as $field => $textLabel) {
            $bodyExtra .= "\n                        <div class=\"form-group mb-3\"><strong>{$textLabel}:</strong> {{ {$bv}->{$field} ?? '—' }}</div>";
        }
    }

    $show = <<<BLADE
@extends('layouts.app')

@section('title', 'Detalle — {$label}')
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', '{$label}')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="{$icon}"></i> {{ {$bv}->nombre ?? 'Registro' }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('{$index}') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('{$edit}', {$bv}->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Nombre:</strong>
                            {{ {$bv}->nombre }}
                        </div>
{$bodyExtra}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection

BLADE;

    $baseDir = $views.'/'.$folder;
    if (! is_dir($baseDir)) {
        return;
    }
    file_put_contents($baseDir.'/create.blade.php', $create);
    file_put_contents($baseDir.'/edit.blade.php', $editBlade);
    file_put_contents($baseDir.'/show.blade.php', $show);
    fwrite(STDOUT, "Wrote CRUD shell: {$folder}\n");
}

$catalogs = [
    ['folder' => 'animal-status', 'routes' => 'animal-statuses', 'var' => 'animalStatus', 'label' => 'Estado de animal', 'icon' => 'fas fa-heartbeat'],
    ['folder' => 'treatment-type', 'routes' => 'treatment-types', 'var' => 'treatmentType', 'label' => 'Tipo de tratamiento', 'icon' => 'fas fa-pills'],
    ['folder' => 'species', 'routes' => 'species', 'var' => 'species', 'label' => 'Especie', 'icon' => 'fas fa-paw'],
    ['folder' => 'care-type', 'routes' => 'care-types', 'var' => 'careType', 'label' => 'Tipo de cuidado', 'icon' => 'fas fa-hand-holding-medical',
        'extraFields' => ['descripcion' => 'Descripción']],
    ['folder' => 'feeding-type', 'routes' => 'feeding-types', 'var' => 'feedingType', 'label' => 'Tipo de alimento', 'icon' => 'fas fa-drumstick-bite',
        'extraFields' => ['descripcion' => 'Descripción']],
    ['folder' => 'feeding-frequency', 'routes' => 'feeding-frequencies', 'var' => 'feedingFrequency', 'label' => 'Frecuencia de alimentación', 'icon' => 'fas fa-clock',
        'extraFields' => ['descripcion' => 'Descripción']],
    ['folder' => 'feeding-portion', 'routes' => 'feeding-portions', 'var' => 'feedingPortion', 'label' => 'Porción de alimento', 'icon' => 'fas fa-balance-scale',
        'extraFields' => ['cantidad' => 'Cantidad', 'unidad' => 'Unidad']],
];

foreach ($catalogs as $c) {
    writeSimpleCatalogCrud($views, $c);
}

// --- Animal-condition & incident-type (nombre + extras en show) ---
writeSimpleCatalogCrud($views, [
    'folder' => 'animal-condition',
    'routes' => 'animal-conditions',
    'var' => 'animalCondition',
    'label' => 'Condición inicial',
    'icon' => 'fas fa-notes-medical',
    'extraFields' => ['severidad' => 'Severidad', 'activo' => 'Activo (1=sí)'],
]);

writeSimpleCatalogCrud($views, [
    'folder' => 'incident-type',
    'routes' => 'incident-types',
    'var' => 'incidentType',
    'label' => 'Tipo de incidente',
    'icon' => 'fas fa-exclamation-triangle',
    'extraFields' => ['riesgo' => 'Riesgo (índice)', 'activo' => 'Activo (1=sí)'],
]);

// Center: show nombre direccion contacto
$centerShow = <<<'BLADE'
@extends('layouts.app')

@section('title', 'Detalle — Centro')
@section('subtitle', 'Centro de custodia.')
@section('content_header_title', 'Centro')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-hospital"></i> {{ $center->nombre }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.centers.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.centers.edit', $center->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3"><strong>Nombre:</strong> {{ $center->nombre }}</div>
                        <div class="form-group mb-3"><strong>Dirección:</strong> {{ $center->direccion }}</div>
                        <div class="form-group mb-3"><strong>Contacto:</strong> {{ $center->contacto }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
BLADE;

$centerCreate = <<<'BLADE'
@extends('layouts.app')

@section('title', 'Nuevo centro — Rescate')
@section('subtitle', 'Alta de centro de custodia.')
@section('content_header_title', 'Centro')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    <div class="container-fluid page-pad">
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
    </div>
    @include('partials.page-pad')
@endsection
BLADE;

$centerEdit = <<<'BLADE'
@extends('layouts.app')

@section('title', 'Editar centro — Rescate')
@section('subtitle', 'Actualizar datos del centro.')
@section('content_header_title', 'Centro')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-warning shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-hospital text-warning"></i> Editar centro</h3>
                        <a href="{{ route('rescate.centers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.centers.update', $center->id) }}" role="form" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf
                            @include('center.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
BLADE;

file_put_contents($views.'/center/create.blade.php', $centerCreate);
file_put_contents($views.'/center/edit.blade.php', $centerEdit);
file_put_contents($views.'/center/show.blade.php', $centerShow);
fwrite(STDOUT, "Wrote center create/edit/show\n");

// User CRUD shells (sync with UserController variables)
$userCreate = <<<'BLADE'
@extends('layouts.app')

@section('title', 'Nuevo usuario — BD rescate')
@section('subtitle', 'Cuenta en la base del submódulo.')
@section('content_header_title', 'Usuario (rescate)')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-user-plus text-success"></i> Nuevo usuario</h3>
                        <a href="{{ route('rescate.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.users.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('user.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
BLADE;

$userEdit = <<<'BLADE'
@extends('layouts.app')

@section('title', 'Editar usuario — BD rescate')
@section('subtitle', 'Actualizar correo o contraseña.')
@section('content_header_title', 'Usuario (rescate)')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-warning shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-user-edit text-warning"></i> Editar usuario</h3>
                        <a href="{{ route('rescate.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.users.update', $user->id) }}" role="form" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf
                            @include('user.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
BLADE;

$userShow = <<<'BLADE'
@extends('layouts.app')

@section('title', 'Usuario — BD rescate')
@section('subtitle', 'Detalle de cuenta del submódulo.')
@section('content_header_title', 'Usuario (rescate)')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-user"></i> {{ $user->name }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.users.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.users.edit', $user->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3"><strong>Nombre mostrado:</strong> {{ $user->name }}</div>
                        <div class="form-group mb-3"><strong>Correo:</strong> {{ $user->email }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
BLADE;

file_put_contents($views.'/user/create.blade.php', $userCreate);
file_put_contents($views.'/user/edit.blade.php', $userEdit);
file_put_contents($views.'/user/show.blade.php', $userShow);
fwrite(STDOUT, "Wrote user create/edit/show\n");

// User form labels ES
$userFormPath = $views.'/user/form.blade.php';
if (is_file($userFormPath)) {
    $uf = file_get_contents($userFormPath);
    $uf = str_replace("{{ __('Name') }}", 'Nombre (mostrado)', $uf);
    $uf = str_replace('placeholder="Name"', 'placeholder="Nombre"', $uf);
    $uf = str_replace("{{ __('Email') }}", 'Correo electrónico', $uf);
    $uf = str_replace('placeholder="Email"', 'placeholder="correo@ejemplo.com"', $uf);
    file_put_contents($userFormPath, $uf);
    fwrite(STDOUT, "Patched user/form labels\n");
}

echo "\nDone.\n";
