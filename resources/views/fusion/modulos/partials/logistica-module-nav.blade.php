@php
    $items = [
        ['route' => 'logistica.estadisticas', 'patterns' => ['logistica.estadisticas', 'logistica.dashboard'], 'icon' => 'fa-chart-bar', 'label' => 'Estadísticas'],
        ['route' => 'logistica.solicitud', 'patterns' => ['logistica.solicitud', 'logistica.solicitud.*'], 'icon' => 'fa-clipboard-list', 'label' => 'Solicitudes'],
        ['route' => 'logistica.paquete', 'patterns' => ['logistica.paquete', 'logistica.paquete.*', 'logistica.seguimiento.tracking'], 'icon' => 'fa-boxes', 'label' => 'Paquetes'],
        ['route' => 'logistica.flota', 'patterns' => ['logistica.flota', 'logistica.vehiculo', 'logistica.conductor'], 'icon' => 'fa-truck', 'label' => 'Flota'],
        ['route' => 'logistica.mapa', 'patterns' => ['logistica.mapa'], 'icon' => 'fa-map-marked-alt', 'label' => 'Mapa'],
        ['route' => 'logistica.configuracion', 'patterns' => ['logistica.configuracion', 'logistica.solicitante', 'logistica.destino', 'logistica.ubicacion', 'logistica.marca', 'logistica.tipo-vehiculo', 'logistica.usuario', 'logistica.rol', 'logistica.estado', 'logistica.tipo-emergencia', 'logistica.tipo-licencia', 'logistica.reporte'], 'icon' => 'fa-cog', 'label' => 'Configuración'],
    ];
@endphp

<div class="card card-outline shadow-sm mb-3 logistica-dashboard-shell">
    <div class="card-header p-2">
        <ul class="nav nav-pills nav-fill flex-wrap logistica-dashboard-nav mb-0" role="navigation" aria-label="Navegación logística">
            @foreach ($items as $item)
                @php
                    $active = false;
                    foreach ($item['patterns'] as $pattern) {
                        if (request()->routeIs($pattern)) {
                            $active = true;
                            break;
                        }
                    }
                @endphp
                <li class="nav-item">
                    @if ($active)
                        <span class="nav-link active" aria-current="page">
                            <i class="fas {{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </span>
                    @else
                        <a class="nav-link" href="{{ route($item['route']) }}">
                            <i class="fas {{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
