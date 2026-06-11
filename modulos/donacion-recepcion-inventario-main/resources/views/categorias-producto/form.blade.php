@php
    $tipos = \Modules\Inventario\Models\CategoriasProducto::TIPOS_CATEGORIA;
    $prioridades = \Modules\Inventario\Models\CategoriasProducto::PRIORIDADES;
    $unidades = \Modules\Inventario\Models\CategoriasProducto::UNIDADES_MEDIDA;
    $condicionesSugeridas = \Modules\Inventario\Models\CategoriasProducto::CONDICIONES_SUGERIDAS;
@endphp

<div class="card card-outline card-primary mb-3">
    <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-tag"></i> Identificación</h3></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nombre">Nombre de la categoría <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                        value="{{ old('nombre', $categoriasProducto?->nombre) }}" id="nombre"
                        placeholder="Ej. Agua potable, Medicamentos básicos" required>
                    @error('nombre')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="codigo">Código interno <span class="text-danger">*</span></label>
                    <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror text-uppercase"
                        value="{{ old('codigo', $categoriasProducto?->codigo) }}" id="codigo"
                        placeholder="CAT-AGUA" maxlength="24" required>
                    <small class="text-muted">Formato: CAT-AGUA, CAT-ALIM-PER</small>
                    @error('codigo')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="2" class="form-control @error('descripcion') is-invalid @enderror"
                        placeholder="Qué productos pertenecen a esta categoría">{{ old('descripcion', $categoriasProducto?->descripcion) }}</textarea>
                    @error('descripcion')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-warning mb-3">
    <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-truck-loading"></i> Clasificación logística</h3></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="tipo_categoria">Tipo de categoría <span class="text-danger">*</span></label>
                    <select name="tipo_categoria" id="tipo_categoria" class="form-control @error('tipo_categoria') is-invalid @enderror" required>
                        @foreach ($tipos as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipo_categoria', $categoriasProducto?->tipo_categoria ?? 'OTRO') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('tipo_categoria')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="unidad_medida">Unidad de medida por defecto</label>
                    <select name="unidad_medida" id="unidad_medida" class="form-control @error('unidad_medida') is-invalid @enderror">
                        <option value="">— Seleccionar —</option>
                        @foreach ($unidades as $value => $label)
                            <option value="{{ $value }}" @selected(old('unidad_medida', $categoriasProducto?->unidad_medida) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('unidad_medida')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="prioridad">Prioridad en emergencia <span class="text-danger">*</span></label>
                    <select name="prioridad" id="prioridad" class="form-control @error('prioridad') is-invalid @enderror" required>
                        @foreach ($prioridades as $value => $label)
                            <option value="{{ $value }}" @selected(old('prioridad', $categoriasProducto?->prioridad ?? 'media') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('prioridad')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check mt-2">
                    <input type="hidden" name="es_perecedero" value="0">
                    <input type="checkbox" name="es_perecedero" value="1" id="es_perecedero" class="form-check-input"
                        @checked(old('es_perecedero', $categoriasProducto?->es_perecedero))>
                    <label class="form-check-label" for="es_perecedero">¿Es perecedero?</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check mt-2">
                    <input type="hidden" name="requiere_fecha_vencimiento" value="0">
                    <input type="checkbox" name="requiere_fecha_vencimiento" value="1" id="requiere_fecha_vencimiento" class="form-check-input"
                        @checked(old('requiere_fecha_vencimiento', $categoriasProducto?->requiere_fecha_vencimiento))>
                    <label class="form-check-label" for="requiere_fecha_vencimiento">¿Requiere fecha de vencimiento?</label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group mt-2">
                    <label for="condiciones_almacenamiento">Condiciones de almacenamiento</label>
                    <textarea name="condiciones_almacenamiento" id="condiciones_almacenamiento" rows="2"
                        class="form-control @error('condiciones_almacenamiento') is-invalid @enderror"
                        placeholder="Refrigerado, seco, protegido del sol…">{{ old('condiciones_almacenamiento', $categoriasProducto?->condiciones_almacenamiento) }}</textarea>
                    <small class="text-muted">Sugerencias: {{ implode(' · ', $condicionesSugeridas) }}</small>
                    @error('condiciones_almacenamiento')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="recomendaciones_uso">Recomendaciones de uso / distribución</label>
                    <textarea name="recomendaciones_uso" id="recomendaciones_uso" rows="2"
                        class="form-control @error('recomendaciones_uso') is-invalid @enderror"
                        placeholder="Indicaciones para despacho durante la emergencia">{{ old('recomendaciones_uso', $categoriasProducto?->recomendaciones_uso) }}</textarea>
                    @error('recomendaciones_uso')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar categoría</button>
    <a href="{{ route('inventario.categorias-producto.index') }}" class="btn btn-secondary">Cancelar</a>
</div>

@push('js')
<script>
document.getElementById('nombre')?.addEventListener('blur', function () {
    const codigo = document.getElementById('codigo');
    if (codigo && !codigo.value.trim() && this.value.trim()) {
        const slug = this.value.toUpperCase().replace(/[^A-Z0-9]+/g, '-').replace(/^-|-$/g, '').substring(0, 16);
        codigo.value = 'CAT-' + (slug || 'OTRO');
    }
});
document.getElementById('es_perecedero')?.addEventListener('change', function () {
    const venc = document.getElementById('requiere_fecha_vencimiento');
    if (this.checked && venc) venc.checked = true;
});
</script>
@endpush
