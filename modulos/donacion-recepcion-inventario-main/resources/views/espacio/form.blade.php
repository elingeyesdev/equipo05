<div class="row padding-1 p-1">
    <div class="col-md-12">

        {{-- Estante Field --}}
        <div class="form-group mb-3">
            <label for="id_estante" class="form-label">
                {{ __('Estante') }}
                <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <select name="id_estante" id="id_estante" class="form-control @error('id_estante') is-invalid @enderror"
                    required>
                    <option value="">{{ __('-- Seleccione un Estante --') }}</option>
                    @foreach(($estantes ?? []) as $estId => $estLabel)
                        <option value="{{ $estId }}" {{ (string) old('id_estante', $espacio?->id_estante) === (string) $estId ? 'selected' : '' }}>
                            {{ $estLabel }}
                        </option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <a href="{{ route('inventario.estante.create', ['return_url' => request()->fullUrl()]) }}"
                        class="btn btn-success" title="Crear nuevo estante">
                        <i class="fas fa-plus"></i> Nuevo Estante
                    </a>
                </div>
                @error('id_estante')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <small class="form-text text-muted">
                Seleccione el estante al que pertenece este espacio
            </small>
        </div>

        <div class="alert alert-info">
            <strong>Nota:</strong> El código del espacio se generará automáticamente al guardar.
        </div>

    </div>
    <div class="col-md-12 mt-3">
        <button type="submit" class="btn btn-primary btn-lg">
            {{ __('Guardar Espacio') }}
        </button>
        <a href="{{ route('inventario.espacio.index') }}" class="btn btn-secondary btn-lg">
            {{ __('Cancelar') }}
        </a>
    </div>
</div>

@push('js')
    <script>
        // Auto-select newly created estante
        @if(session('new_estante_id'))
            document.addEventListener('DOMContentLoaded', function () {
                const selectElement = document.getElementById('id_estante');
                if (selectElement) {
                    selectElement.value = '{{ session('new_estante_id') }}';

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



