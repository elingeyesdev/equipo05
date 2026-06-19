@php
    $items = [
        ['route' => 'seguimiento.estadisticas', 'patterns' => ['seguimiento.estadisticas', 'seguimiento.dashboard'], 'icon' => 'fa-chart-bar', 'label' => 'Estadísticas'],
        ['route' => 'seguimiento.voluntarios', 'patterns' => ['seguimiento.voluntarios', 'seguimiento.voluntarios-inactivos', 'seguimiento.crud.create', 'seguimiento.crud.edit'], 'icon' => 'fa-users', 'label' => 'Voluntarios'],
        ['route' => 'seguimiento.voluntarios-inactivos', 'patterns' => ['seguimiento.voluntarios-inactivos'], 'icon' => 'fa-user-slash', 'label' => 'Inactivos'],
        ['route' => 'seguimiento.evaluacion', 'patterns' => ['seguimiento.evaluacion'], 'icon' => 'fa-clipboard-check', 'label' => 'Evaluación'],
        ['route' => 'seguimiento.evaluacion-pruebas', 'patterns' => ['seguimiento.evaluacion-pruebas'], 'icon' => 'fa-tasks', 'label' => 'Pruebas'],
        ['route' => 'seguimiento.capacitaciones', 'patterns' => ['seguimiento.capacitaciones'], 'icon' => 'fa-chalkboard-teacher', 'label' => 'Capacitaciones'],
        ['route' => 'seguimiento.necesidades', 'patterns' => ['seguimiento.necesidades'], 'icon' => 'fa-clipboard-list', 'label' => 'Necesidades'],
        ['route' => 'seguimiento.ayudas-solicitadas', 'patterns' => ['seguimiento.ayudas-solicitadas'], 'icon' => 'fa-hands-helping', 'label' => 'Ayudas'],
        ['route' => 'seguimiento.administradores', 'patterns' => ['seguimiento.administradores'], 'icon' => 'fa-user-shield', 'label' => 'Administradores'],
        ['route' => 'seguimiento.universidades', 'patterns' => ['seguimiento.universidades'], 'icon' => 'fa-university', 'label' => 'Universidades'],
        ['route' => 'seguimiento.chat-consulta', 'patterns' => ['seguimiento.chat-consulta', 'seguimiento.chat.enviar'], 'icon' => 'fa-comments', 'label' => 'Chat'],
        ['route' => 'seguimiento.helpdesk', 'patterns' => ['seguimiento.helpdesk'], 'icon' => 'fa-life-ring', 'label' => 'Soporte'],
    ];
@endphp

<div class="card card-outline shadow-sm mb-3 seg-dashboard-shell">
    <div class="card-header p-2">
        <ul class="nav nav-pills nav-fill flex-wrap seg-dashboard-nav mb-0" role="navigation" aria-label="Navegación seguimiento voluntarios">
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
