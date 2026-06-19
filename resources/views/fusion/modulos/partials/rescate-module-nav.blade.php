@php
    $items = [
        ['route' => 'rescate.home', 'patterns' => ['rescate.home', 'rescate.dashboard.*'], 'icon' => 'fa-chart-line', 'label' => 'Inicio'],
        ['route' => 'rescate.reports.index', 'patterns' => ['rescate.reports.index', 'rescate.reports.show', 'rescate.reports.edit', 'rescate.reports.create', 'rescate.reports.mapa-campo'], 'icon' => 'fa-binoculars', 'label' => 'Hallazgos'],
        ['route' => 'rescate.animal-files.index', 'patterns' => ['rescate.animal-files.*', 'rescate.animal-records.*'], 'icon' => 'fa-paw', 'label' => 'Hojas de vida'],
        ['route' => 'rescate.transfers.index', 'patterns' => ['rescate.transfers.*'], 'icon' => 'fa-truck', 'label' => 'Traslados'],
        ['route' => 'rescate.medical-evaluations.index', 'patterns' => ['rescate.medical-evaluations.*', 'rescate.medical-evaluation-transactions.*'], 'icon' => 'fa-stethoscope', 'label' => 'Evaluaciones'],
        ['route' => 'rescate.cares.index', 'patterns' => ['rescate.cares.*', 'rescate.care-feedings.*', 'rescate.animal-care-records.*', 'rescate.animal-feeding-records.*'], 'icon' => 'fa-heart', 'label' => 'Cuidados'],
        ['route' => 'rescate.releases.index', 'patterns' => ['rescate.releases.*'], 'icon' => 'fa-leaf', 'label' => 'Liberaciones'],
        ['route' => 'rescate.animal-histories.index', 'patterns' => ['rescate.animal-histories.*'], 'icon' => 'fa-history', 'label' => 'Historial'],
        ['route' => 'rescate.reportes.index', 'patterns' => ['rescate.reportes.*'], 'icon' => 'fa-chart-bar', 'label' => 'Reportes'],
    ];
@endphp

<div class="card card-outline shadow-sm mb-3 res-dashboard-shell">
    <div class="card-header p-2">
        <ul class="nav nav-pills nav-fill flex-wrap res-dashboard-nav mb-0" role="navigation" aria-label="Navegación módulo rescate">
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
