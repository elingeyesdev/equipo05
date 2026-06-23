@php
    use App\Support\LogisticaCrudUi;
    use App\Support\ModuloCrudEjemplos;

    $listRoute = LogisticaCrudUi::listRouteName($seccion);
    $listParams = LogisticaCrudUi::listRouteParams($seccion);
    $lastSection = null;
@endphp

<div class="logistica-crud-shell">
    <nav aria-label="breadcrumb" class="logistica-crud-breadcrumb mb-3">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('logistica.estadisticas') }}">Logística</a></li>
            @if(LogisticaCrudUi::isConfigSection($seccion))
                <li class="breadcrumb-item"><a href="{{ route('logistica.configuracion') }}">Configuración</a></li>
                <li class="breadcrumb-item"><a href="{{ route('logistica.'.$seccion) }}">{{ $tituloSeccion }}</a></li>
            @else
                <li class="breadcrumb-item"><a href="{{ route($listRoute, $listParams) }}">{{ $tituloSeccion }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $registro ? 'Editar' : 'Nuevo' }}</li>
        </ol>
    </nav>

    <div class="card logistica-list-card shadow-sm">
        <div class="card-header logistica-crud-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h5 class="mb-0">{{ $registro ? 'Editar' : 'Registrar' }} {{ strtolower($tituloSeccion) }}</h5>
                <small class="text-muted">Complete los campos marcados según el tipo de registro.</small>
            </div>
            <a href="{{ route($listRoute, $listParams) }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="card-body">
            <form method="POST"
                  action="{{ $registro ? route('logistica.crud.update', ['seccion' => $seccion, 'id' => data_get($registro, $primaryKey)]) : route('logistica.crud.store', ['seccion' => $seccion]) }}"
                  class="logistica-crud-form"
                  @if($seccion === 'paquete') enctype="multipart/form-data" @endif>
                @csrf
                @if($registro)
                    @method('PUT')
                @endif

                <div class="row">
                @foreach($columns as $column)
                    @php
                        $sectionTitle = LogisticaCrudUi::sectionTitle($seccion, $column);
                        $label = LogisticaCrudUi::label($column);
                        $colClass = LogisticaCrudUi::colClass($seccion, $column);
                        $readonly = LogisticaCrudUi::isReadonly($seccion, $column);
                        $value = old($column, data_get($registro, $column));
                        $placeholder = ModuloCrudEjemplos::placeholder('logistica', $seccion, $column);
                        $isIdField = str_starts_with($column, 'id_') || str_ends_with($column, '_id');
                    @endphp

                    @if($sectionTitle && $sectionTitle !== $lastSection)
                        @php $lastSection = $sectionTitle; @endphp
                        <div class="col-12">
                            <h6 class="logistica-form-section-title">{{ $sectionTitle }}</h6>
                        </div>
                    @endif

                    <div class="{{ $colClass }} mb-3">
                        <label class="logistica-form-label" for="field-{{ $seccion }}-{{ $column }}">{{ $label }}</label>

                        @if($readonly)
                            <input type="text" id="field-{{ $seccion }}-{{ $column }}" class="form-control bg-light" value="{{ $value }}" readonly>
                            <small class="text-muted">Generado automáticamente; no se modifica.</small>

                        @elseif($seccion === 'solicitud' && $column === 'estado')
                            <select name="{{ $column }}" id="field-{{ $seccion }}-{{ $column }}" class="form-control">
                                @foreach(LogisticaCrudUi::estadoSolicitudOptions() as $estado)
                                    <option value="{{ $estado }}" {{ (string) $value === $estado ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $estado)) }}</option>
                                @endforeach
                            </select>

                        @elseif($seccion === 'solicitud' && $column === 'tipo_emergencia')
                            <select name="{{ $column }}" id="field-{{ $seccion }}-{{ $column }}" class="form-control">
                                @if(!empty($options[$column]))
                                    @foreach($options[$column] as $option)
                                        <option value="{{ $option->nombre }}" {{ (string) $value === (string) $option->nombre ? 'selected' : '' }}>{{ $option->nombre }}</option>
                                    @endforeach
                                @else
                                    @foreach(LogisticaCrudUi::emergenciaFallbackOptions() as $tipo)
                                        <option value="{{ $tipo }}" {{ (string) $value === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                    @endforeach
                                @endif
                                @if($value && !collect($options[$column] ?? [])->pluck('nombre')->contains($value))
                                    <option value="{{ $value }}" selected>{{ $value }}</option>
                                @endif
                            </select>

                        @elseif(LogisticaCrudUi::isBooleanField($column))
                            @php
                                $boolVal = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                                if ($boolVal === null && $value !== null && $value !== '') {
                                    $boolVal = in_array(strtolower((string) $value), ['1', 'true', 'si', 'sí'], true);
                                }
                                $boolVal = $boolVal ?? false;
                            @endphp
                            <select name="{{ $column }}" id="field-{{ $seccion }}-{{ $column }}" class="form-control">
                                <option value="1" {{ $boolVal ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ ! $boolVal ? 'selected' : '' }}>No</option>
                            </select>

                        @elseif($isIdField)
                            <select name="{{ $column }}" id="field-{{ $seccion }}-{{ $column }}" class="form-control">
                                @if(!empty($options[$column]))
                                    <option value="">Seleccione…</option>
                                    @foreach($options[$column] as $option)
                                        <option value="{{ $option->id }}" {{ (string) $value === (string) $option->id ? 'selected' : '' }}>{{ $option->nombre }}</option>
                                    @endforeach
                                @else
                                    <option value="">Sin opciones en catálogo</option>
                                @endif
                            </select>

                        @elseif(str_contains($column, 'fecha'))
                            <input type="date" name="{{ $column }}" id="field-{{ $seccion }}-{{ $column }}" class="form-control" value="{{ $value }}">

                        @elseif(str_contains($column, 'descripcion') || str_contains($column, 'contenido') || str_contains($column, 'insumo') || str_contains($column, 'observacion'))
                            <textarea name="{{ $column }}" id="field-{{ $seccion }}-{{ $column }}" rows="3" class="form-control" placeholder="{{ $placeholder }}">{{ $value }}</textarea>

                        @elseif(str_contains($column, 'cantidad') || str_contains($column, 'capacidad') || str_contains($column, 'puntaje') || str_contains($column, 'nota') || $column === 'anio')
                            <input type="number" name="{{ $column }}" id="field-{{ $seccion }}-{{ $column }}" class="form-control" value="{{ $value }}" placeholder="{{ $placeholder }}" @if($column === 'anio') min="1980" max="2035" @else min="0" @endif>

                        @elseif($column === 'email')
                            <input type="email" name="{{ $column }}" id="field-{{ $seccion }}-{{ $column }}" class="form-control" value="{{ $value }}" placeholder="{{ $placeholder }}">

                        @elseif($column === 'telefono' || $column === 'ci' || $column === 'placa')
                            <input type="text" name="{{ $column }}" id="field-{{ $seccion }}-{{ $column }}" class="form-control" value="{{ $value }}" placeholder="{{ $placeholder }}">

                        @else
                            <input type="text" name="{{ $column }}" id="field-{{ $seccion }}-{{ $column }}" class="form-control" value="{{ $value }}" placeholder="{{ $placeholder }}">
                        @endif
                    </div>
                @endforeach

                @if($seccion === 'paquete')
                    <div class="col-12 mb-3" id="foto-entrega">
                        <h6 class="logistica-form-section-title">Foto de entrega (galería pública)</h6>
                        <p class="small text-muted mb-2">Suba una foto del paquete entregado en la comunidad. Aparecerá en la galería de la app y del acceso ciudadano.</p>
                        @if($tieneFotoEntrega ?? false)
                            <div class="logistica-foto-preview mb-3">
                                <img src="data:image/jpeg;base64,{{ base64_encode($registro->imagen) }}" alt="Foto actual" class="img-fluid rounded border">
                                <small class="d-block text-success mt-1"><i class="fas fa-check-circle"></i> Este paquete ya tiene foto en galería.</small>
                            </div>
                        @endif
                        <label class="logistica-form-label" for="foto-entrega-input">Seleccionar imagen</label>
                        <input type="file" name="foto_entrega" id="foto-entrega-input" class="form-control-file" accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted">Formatos: JPG, PNG o WebP. Máximo recomendado 5 MB.</small>
                    </div>
                @endif
                </div>

                <div class="logistica-crud-footer d-flex flex-wrap justify-content-between align-items-center pt-3 mt-2 border-top">
                    <small class="text-muted mb-2 mb-md-0">Los cambios se reflejan de inmediato en listados y seguimiento.</small>
                    <div class="d-flex" style="gap:.5rem;">
                        <a href="{{ route($listRoute, $listParams) }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
