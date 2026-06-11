@php
    $unidades = \Modules\Inventario\Models\CategoriasProducto::UNIDADES_MEDIDA;
    $prioridades = \Modules\Inventario\Models\Producto::PRIORIDADES;
    $estados = \Modules\Inventario\Models\Producto::ESTADOS;
    $unidadActual = old('unidad_medida', $producto?->unidad_medida);
@endphp

<div class="card card-outline card-primary mb-3">
    <div class="card-header"><h3 class="card-title mb-0">Identificación del producto</h3></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="codigo">Código <span class="text-danger">*</span></label>
                    <input type="text" name="codigo" id="codigo" class="form-control text-uppercase @error('codigo') is-invalid @enderror"
                        value="{{ old('codigo', $producto?->codigo) }}" placeholder="PROD-AGUA-001" maxlength="50" required>
                    <small class="text-muted">Formato: PROD-NOMBRE-001</small>
                    @error('codigo')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
                        value="{{ old('nombre', $producto?->nombre) }}" maxlength="150" required>
                    @error('nombre')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_categoria">
                        Categoría <span class="text-danger">*</span>
                        <a href="{{ route('inventario.categorias-producto.create') }}" target="_blank" class="btn btn-xs btn-success ml-1" title="Nueva categoría">
                            <i class="fas fa-plus"></i>
                        </a>
                    </label>
                    <select name="id_categoria" id="id_categoria" class="form-control @error('id_categoria') is-invalid @enderror" required>
                        <option value="">— Seleccionar —</option>
                        @foreach (($categorias ?? []) as $catId => $catName)
                            <option value="{{ $catId }}" @selected((string) old('id_categoria', $producto?->id_categoria) === (string) $catId)>{{ $catName }}</option>
                        @endforeach
                    </select>
                    @error('id_categoria')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="imagen_url">Imagen URL</label>
                    <input type="url" name="imagen_url" id="imagen_url" class="form-control @error('imagen_url') is-invalid @enderror"
                        value="{{ old('imagen_url', $producto?->imagen_url) }}" placeholder="https://...">
                    @error('imagen_url')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-12">
                <div id="categoria-reglas" class="alert alert-light border d-none mb-3"></div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="2" class="form-control @error('descripcion') is-invalid @enderror"
                        maxlength="500" placeholder="Descripción del producto en catálogo">{{ old('descripcion', $producto?->descripcion) }}</textarea>
                    @error('descripcion')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-warning mb-3">
    <div class="card-header"><h3 class="card-title mb-0">Clasificación logística</h3></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="unidad_medida">Unidad de medida <span class="text-danger">*</span></label>
                    <select name="unidad_medida" id="unidad_medida" class="form-control @error('unidad_medida') is-invalid @enderror" required>
                        <option value="">— Seleccionar —</option>
                        @foreach ($unidades as $value => $label)
                            <option value="{{ $value }}" @selected($unidadActual === $value)>{{ $label }}</option>
                        @endforeach
                        @if ($unidadActual && !array_key_exists($unidadActual, $unidades))
                            <option value="{{ $unidadActual }}" selected>{{ $unidadActual }}</option>
                        @endif
                    </select>
                    @error('unidad_medida')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="prioridad">Prioridad <span class="text-danger">*</span></label>
                    <select name="prioridad" id="prioridad" class="form-control @error('prioridad') is-invalid @enderror" required>
                        @foreach ($prioridades as $value => $label)
                            <option value="{{ $value }}" @selected(old('prioridad', $producto?->prioridad ?? 'media') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('prioridad')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="estado">Estado <span class="text-danger">*</span></label>
                    <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" required>
                        @foreach ($estados as $value => $label)
                            <option value="{{ $value }}" @selected(old('estado', $producto?->estado ?? 'activo') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('estado')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="stock_minimo">Stock mínimo (alerta)</label>
                    <input type="number" name="stock_minimo" id="stock_minimo" min="0" class="form-control @error('stock_minimo') is-invalid @enderror"
                        value="{{ old('stock_minimo', $producto?->stock_minimo ?? 0) }}">
                    @error('stock_minimo')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-info mb-3">
    <div class="card-header"><h3 class="card-title mb-0">Control especial</h3></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-check">
                    <input type="hidden" name="requiere_vencimiento" value="0">
                    <input type="checkbox" name="requiere_vencimiento" value="1" id="requiere_vencimiento" class="form-check-input"
                        @checked(old('requiere_vencimiento', $producto?->requiere_vencimiento))>
                    <label class="form-check-label" for="requiere_vencimiento">Requiere vencimiento</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input type="hidden" name="requiere_talla" value="0">
                    <input type="checkbox" name="requiere_talla" value="1" id="requiere_talla" class="form-check-input"
                        @checked(old('requiere_talla', $producto?->requiere_talla))>
                    <label class="form-check-label" for="requiere_talla">Requiere talla</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input type="hidden" name="requiere_condicion" value="0">
                    <input type="checkbox" name="requiere_condicion" value="1" id="requiere_condicion" class="form-check-input"
                        @checked(old('requiere_condicion', $producto?->requiere_condicion))>
                    <label class="form-check-label" for="requiere_condicion">Requiere control de condición</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input type="hidden" name="producto_restringido" value="0">
                    <input type="checkbox" name="producto_restringido" value="1" id="producto_restringido" class="form-check-input"
                        @checked(old('producto_restringido', $producto?->producto_restringido))>
                    <label class="form-check-label" for="producto_restringido">Producto restringido</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-secondary mb-3">
    <div class="card-header"><h3 class="card-title mb-0">Almacenamiento y observaciones</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label for="condiciones_almacenamiento">Condiciones de almacenamiento</label>
            <textarea name="condiciones_almacenamiento" id="condiciones_almacenamiento" rows="2" class="form-control @error('condiciones_almacenamiento') is-invalid @enderror"
                maxlength="500" placeholder="Lugar seco, refrigerado, protegido del sol…">{{ old('condiciones_almacenamiento', $producto?->condiciones_almacenamiento) }}</textarea>
            @error('condiciones_almacenamiento')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
        </div>
        <div class="form-group mb-0">
            <label for="observaciones">Observaciones internas</label>
            <textarea name="observaciones" id="observaciones" rows="2" class="form-control @error('observaciones') is-invalid @enderror"
                maxlength="500">{{ old('observaciones', $producto?->observaciones) }}</textarea>
            @error('observaciones')<div class="invalid-feedback"><strong>{{ $message }}</strong></div>@enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar producto</button>
    <a href="{{ route('inventario.producto.index') }}" class="btn btn-secondary">Cancelar</a>
</div>

@push('js')
<script>
(function () {
    const categoriasMeta = @json($categoriasMeta ?? []);
    const catSelect = document.getElementById('id_categoria');
    const reglasBox = document.getElementById('categoria-reglas');

    function aplicarReglasCategoria() {
        const id = catSelect?.value;
        const meta = id ? categoriasMeta[id] : null;
        if (!reglasBox) return;

        if (!meta) {
            reglasBox.classList.add('d-none');
            reglasBox.innerHTML = '';
            return;
        }

        const lineas = [];
        if (meta.requiere_fecha_vencimiento) lineas.push('Esta categoría requiere control de vencimiento.');
        if (meta.prioridad === 'alta') lineas.push('Esta categoría tiene prioridad alta en emergencia.');
        if (meta.es_perecedero) lineas.push('Esta categoría incluye productos perecederos.');
        if (meta.requiere_talla) lineas.push('Esta categoría requiere registro de talla (vestimenta).');
        if (meta.producto_restringido) lineas.push('Esta categoría puede implicar productos con autorización especial.');
        if (meta.condiciones_almacenamiento) lineas.push('Almacenamiento: ' + meta.condiciones_almacenamiento);

        reglasBox.innerHTML = '<strong>Reglas de la categoría «' + meta.nombre + '»</strong><ul class="mb-0 mt-2"><li>' + lineas.join('</li><li>') + '</li></ul>';
        reglasBox.classList.toggle('d-none', lineas.length === 0);

        const chkVenc = document.getElementById('requiere_vencimiento');
        if (chkVenc && meta.requiere_fecha_vencimiento) chkVenc.checked = true;

        const chkTalla = document.getElementById('requiere_talla');
        if (chkTalla && meta.requiere_talla) chkTalla.checked = true;

        const chkRestr = document.getElementById('producto_restringido');
        if (chkRestr && meta.producto_restringido) chkRestr.checked = true;

        const prioridad = document.getElementById('prioridad');
        if (prioridad && meta.prioridad && !prioridad.dataset.userChanged) {
            prioridad.value = meta.prioridad;
        }

        const unidad = document.getElementById('unidad_medida');
        if (unidad && meta.unidad_medida) {
            const opt = Array.from(unidad.options).find(o => o.value === meta.unidad_medida);
            if (opt) unidad.value = meta.unidad_medida;
        }

        const cond = document.getElementById('condiciones_almacenamiento');
        if (cond && meta.condiciones_almacenamiento && !cond.value.trim()) {
            cond.value = meta.condiciones_almacenamiento;
        }
    }

    catSelect?.addEventListener('change', aplicarReglasCategoria);
    document.getElementById('prioridad')?.addEventListener('change', function () {
        this.dataset.userChanged = '1';
    });

    document.getElementById('nombre')?.addEventListener('blur', function () {
        const codigo = document.getElementById('codigo');
        if (codigo && !codigo.value.trim() && this.value.trim()) {
            const slug = this.value.toUpperCase().replace(/[^A-Z0-9]+/g, '-').replace(/^-|-$/g, '').substring(0, 20);
            codigo.value = 'PROD-' + (slug || 'ITEM');
        }
    });

    aplicarReglasCategoria();
})();
</script>
@endpush
