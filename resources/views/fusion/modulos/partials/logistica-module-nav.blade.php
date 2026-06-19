@php
    $items = [
        ['route' => 'logistica.estadisticas', 'patterns' => ['logistica.estadisticas', 'logistica.dashboard'], 'icon' => 'fa-chart-bar', 'label' => 'Estadísticas'],
        ['route' => 'logistica.solicitud', 'patterns' => ['logistica.solicitud', 'logistica.solicitud.*'], 'icon' => 'fa-clipboard-list', 'label' => 'Solicitudes'],
        ['route' => 'logistica.paquete', 'patterns' => ['logistica.paquete'], 'icon' => 'fa-boxes', 'label' => 'Paquetes'],
        ['route' => 'logistica.seguimiento', 'patterns' => ['logistica.seguimiento'], 'icon' => 'fa-route', 'label' => 'Seguimiento'],
        ['route' => 'logistica.vehiculo', 'patterns' => ['logistica.vehiculo'], 'icon' => 'fa-truck', 'label' => 'Vehículos'],
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
