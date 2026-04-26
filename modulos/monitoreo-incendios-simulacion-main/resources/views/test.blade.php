@extends('layouts.app')

@section('subtitle', 'Pruebas de Integraciones')
@section('content_header_title', 'Endpoint de Pruebas')
@section('content_header_subtitle', 'OpenWeather y FIRMS')

@section('content_body')
<div class="row">
    <div class="col-md-6">
        <x-adminlte-card title="Clima Actual (OpenWeatherMap)" theme="info" icon="fas fa-cloud-sun">
            <form method="get" action="{{ route('test.index') }}" class="mb-3">
                <div class="form-row">
                    <div class="col">
                        <x-adminlte-input name="city" label="Ciudad" value="{{ $params['city'] ?? 'Santa Cruz' }}" placeholder="Opcional si usas lat/lon" />
                    </div>
                    <div class="col">
                        <x-adminlte-input name="country" label="País (ISO2)" value="{{ $params['country'] ?? 'BO' }}" placeholder="Ej: BO" />
                    </div>
                </div>
                <div class="form-row mt-2">
                    <div class="col">
                        <x-adminlte-input name="lat" label="Latitud" value="{{ $params['lat'] ?? '' }}" placeholder="Ej: -17.7833" />
                    </div>
                    <div class="col">
                        <x-adminlte-input name="lon" label="Longitud" value="{{ $params['lon'] ?? '' }}" placeholder="Ej: -63.1821" />
                    </div>
                    <div class="col-auto d-flex align-items-end">
                        <x-adminlte-button type="submit" theme="primary" label="Actualizar" icon="fas fa-sync" />
                    </div>
                </div>
            </form>
            <pre class="bg-light p-3" style="max-height:300px; overflow:auto;">{{ json_encode($weather, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </x-adminlte-card>
    </div>
    <div class="col-md-6">
        <x-adminlte-card title="Focos de Calor (FIRMS)" theme="warning" icon="fas fa-fire">
            <form method="get" action="{{ route('test.index') }}" class="mb-3">
                <div class="form-row">
                    <div class="col">
                        <x-adminlte-input name="product" label="Producto FIRMS (ej. VIIRS_SNPP_NRT)" value="{{ $params['product'] ?? 'VIIRS_SNPP_NRT' }}" />
                    </div>
                    <div class="col">
                        <x-adminlte-input name="countryFirms" label="País FIRMS (ISO3)" value="{{ $params['countryFirms'] ?? 'BOL' }}" />
                    </div>
                    <div class="col-auto d-flex align-items-end">
                        <x-adminlte-button type="submit" theme="primary" label="Actualizar" icon="fas fa-sync" />
                    </div>
                </div>
            </form>
            @if(isset($firms['error']))
                <x-adminlte-alert theme="danger" title="Error">
                    Código {{ $firms['error'] }} - {{ $firms['message'] ?? 'Error consultando FIRMS' }}
                </x-adminlte-alert>
            @else
                <pre class="bg-light p-3" style="max-height:300px; overflow:auto;">{{ json_encode($firms, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            @endif
        </x-adminlte-card>
    </div>
</div>
@stop
