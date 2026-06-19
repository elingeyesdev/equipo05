@php
    $items = [
        ['route' => 'incendios.dashboard', 'pattern' => 'incendios.dashboard', 'icon' => 'fa-map-marked-alt', 'label' => 'Monitoreo'],
        ['route' => 'incendios.focos-incendios.index', 'pattern' => 'incendios.focos-incendios.*', 'icon' => 'fa-fire', 'label' => 'Focos de Incendio'],
        ['route' => 'incendios.biomasas.index', 'pattern' => 'incendios.biomasas.*', 'icon' => 'fa-leaf', 'label' => 'Biomasas'],
        ['route' => 'incendios.simulaciones.index', 'pattern' => 'incendios.simulaciones.*', 'icon' => 'fa-project-diagram', 'label' => 'Simulaciones'],
        ['route' => 'incendios.reports.fires', 'pattern' => 'incendios.reports.*', 'icon' => 'fa-chart-line', 'label' => 'Reportes'],
    ];
@endphp

<div class="card card-outline shadow-sm mb-3 inc-dashboard-shell">
    <div class="card-header inc-dashboard-quicknav">
        <ul class="nav nav-pills nav-fill flex-wrap inc-dashboard-nav" role="navigation" aria-label="Accesos del módulo incendios">
            @foreach ($items as $item)
                @php $active = request()->routeIs($item['pattern']); @endphp
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
