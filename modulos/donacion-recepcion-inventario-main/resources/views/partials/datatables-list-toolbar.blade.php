{{-- Barra de filtros y orden para listados DataTables --}}
@php
    $filters = $filters ?? [];
    $sortId = $sortId ?? 'ordenarPor';
    $sortOptions = $sortOptions ?? [];
@endphp

@if (count($filters) > 0 || count($sortOptions) > 0)
    <div class="row mb-3 align-items-end inventario-dt-toolbar">
        @foreach ($filters as $filter)
            <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                <label for="{{ $filter['id'] }}" class="mb-1">{{ $filter['label'] }}</label>
                <select id="{{ $filter['id'] }}" class="form-control form-control-sm inventario-dt-filter">
                    <option value="">{{ $filter['placeholder'] ?? 'Todos' }}</option>
                    @foreach ($filter['options'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endforeach

        @if (count($sortOptions) > 0)
            <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                <label for="{{ $sortId }}" class="mb-1">Ordenar por</label>
                <select id="{{ $sortId }}" class="form-control form-control-sm inventario-dt-sort">
                    @foreach ($sortOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($defaultSort ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
@endif
