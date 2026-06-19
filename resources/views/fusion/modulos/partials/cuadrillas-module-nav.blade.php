@php
    $items = [
        ['route' => 'cuadrillas.estadisticas', 'patterns' => ['cuadrillas.estadisticas', 'cuadrillas.dashboard'], 'icon' => 'fa-chart-bar', 'label' => 'Dashboard'],
        ['route' => 'cuadrillas.reportes', 'patterns' => ['cuadrillas.reportes'], 'icon' => 'fa-exclamation-triangle', 'label' => 'Reporte'],
        ['route' => 'cuadrillas.focos-calor', 'patterns' => ['cuadrillas.focos-calor'], 'icon' => 'fa-map-marked-alt', 'label' => 'Mapa'],
        ['route' => 'cuadrillas.noticias', 'patterns' => ['cuadrillas.noticias'], 'icon' => 'fa-newspaper', 'label' => 'Noticias'],
        ['route' => 'cuadrillas.cursos', 'patterns' => ['cuadrillas.cursos', 'cuadrillas.crud.create', 'cuadrillas.crud.edit'], 'icon' => 'fa-graduation-cap', 'label' => 'Cursos'],
    ];
@endphp

<div class="card card-outline shadow-sm mb-3 cua-dashboard-shell">
    <div class="card-header p-2">
        <ul class="nav nav-pills nav-fill flex-wrap cua-dashboard-nav mb-0" role="navigation" aria-label="Navegación cuadrillas">
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
