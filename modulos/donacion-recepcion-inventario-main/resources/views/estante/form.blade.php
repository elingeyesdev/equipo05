<div class="row padding-1 p-1">
    <div class="col-md-12">

        {{-- Almacen Field --}}
        <div class="form-group mb-3">
            <label for="id_almacen" class="form-label">
                {{ __('Almacén') }}
                <span class="text-danger">*</span>
            </label>
            @if(isset($idAlmacen) && $idAlmacen)
                {{-- Campo de solo lectura cuando viene desde almacenes --}}
                <div class="input-group">
                    <select id="id_almacen_display" class="form-control" disabled>
                        @foreach(($almacenes ?? []) as $almId => $almName)
                            <option value="{{ $almId }}" {{ (string) $idAlmacen === (string) $almId ? 'selected' : '' }}>
                                {{ $almName }}
                            </option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                </div>
                {{-- Campo oculto para enviar el valor --}}
                <input type="hidden" name="id_almacen" value="{{ $idAlmacen }}">
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i> El almacén está preseleccionado y no puede modificarse
                </small>
            @else
                {{-- Campo editable cuando se accede normalmente --}}
                <div class="input-group">
                    <select name="id_almacen" id="id_almacen" class="form-control @error('id_almacen') is-invalid @enderror"
                        required>
                        <option value="">{{ __('-- Seleccione un Almacén --') }}</option>
                        @foreach(($almacenes ?? []) as $almId => $almName)
                            <option value="{{ $almId }}" {{ (string) old('id_almacen', $estante?->id_almacen) === (string) $almId ? 'selected' : '' }}>
                                {{ $almName }}
                            </option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <a href="{{ route('inventario.almacene.create', ['return_url' => request()->fullUrl()]) }}"
                            class="btn btn-success" title="Crear nuevo almacén">
                            <i class="fas fa-plus"></i> Nuevo Almacén
                        </a>
                    </div>
                    @error('id_almacen')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <small class="form-text text-muted">
                    Seleccione el almacén al que pertenece este estante
                </small>
            @endif
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="filas" class="form-label">
                        {{ __('Filas') }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="filas" id="filas"
                        class="form-control @error('filas') is-invalid @enderror" value="{{ old('filas', 3) }}" min="1"
                        required placeholder="Ej: 3">
                    @error('filas')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <small class="form-text text-muted">
                        Número de filas del estante
                    </small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="columnas" class="form-label">
                        {{ __('Columnas') }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="columnas" id="columnas"
                        class="form-control @error('columnas') is-invalid @enderror" value="{{ old('columnas', 3) }}"
                        min="1" required placeholder="Ej: 3">
                    @error('columnas')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <small class="form-text text-muted">
                        Número de columnas por fila
                    </small>
                </div>
            </div>
        </div>

        {{-- Descripcion Field --}}
        <div class="form-group mb-3">
            <label for="descripcion" class="form-label">
                {{ __('Descripción') }}
            </label>
            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                id="descripcion" rows="3"
                placeholder="Descripción del estante (opcional)">{{ old('descripcion', $estante?->descripcion) }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
            <small class="form-text text-muted">
                Agregue información adicional sobre el estante
            </small>
        </div>

        <div class="alert alert-info">
            <strong>Nota:</strong> El código del estante se generará automáticamente al guardar.
        </div>

    </div>
    <div class="col-md-12 mt-3">
        <button type="submit" class="btn btn-primary btn-lg">
            {{ __('Guardar Estante') }}
        </button>
        <a href="{{ route('inventario.estante.index') }}" class="btn btn-secondary btn-lg">
            {{ __('Cancelar') }}
        </a>
    </div>
</div>

@push('js')
    <script>
        // Auto-select newly created almacen
        @if(session('new_almacen_id'))
            document.addEventListener('DOMContentLoaded', function () {
                const selectElement = document.getElementById('id_almacen');
                if (selectElement) {
                    selectElement.value = '{{ session('new_almacen_id') }}';

                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                                            {{ session('success') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        `;

                    const formContainer = selectElement.closest('.col-md-12');
                    if (formContainer) {
                        formContainer.insertBefore(alertDiv, formContainer.firstChild);
                    }
                }
            });
        @endif
    </script>
@endpush



