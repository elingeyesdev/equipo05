<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-bullhorn"></i> Información de la Campaña</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="nombre">Nombre de la Campaña <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-heading"></i></span>
                        </div>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre', $campana?->nombre) }}" id="nombre"
                            placeholder="Ingrese el nombre de la campaña" required>
                        @error('nombre')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="descripcion">Descripción <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                        </div>
                        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                            id="descripcion" rows="3" placeholder="Describa el objetivo de la campaña"
                            required>{{ old('descripcion', $campana?->descripcion) }}</textarea>
                        @error('descripcion')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        <input type="date" name="fecha_inicio"
                            class="form-control @error('fecha_inicio') is-invalid @enderror"
                            value="{{ old('fecha_inicio', $campana?->fecha_inicio) }}" id="fecha_inicio" required>
                        @error('fecha_inicio')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="fecha_fin">Fecha de Fin <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-times"></i></span>
                        </div>
                        <input type="date" name="fecha_fin"
                            class="form-control @error('fecha_fin') is-invalid @enderror"
                            value="{{ old('fecha_fin', $campana?->fecha_fin) }}" id="fecha_fin" required>
                        @error('fecha_fin')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="imagen_banner_file">Imagen Banner (Opcional)</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" name="imagen_banner_file" class="custom-file-input @error('imagen_banner_file') is-invalid @enderror" 
                                id="imagen_banner_file" accept="image/*" onchange="previewImage(event)">
                            <label class="custom-file-label" for="imagen_banner_file">Seleccionar imagen...</label>
                        </div>
                        @error('imagen_banner_file')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                    
                    @if($campana?->imagen_banner)
                        <div class="mt-3">
                            <label>Imagen actual:</label>
                            <div>
                                <img src="{{ asset($campana->imagen_banner) }}" alt="Banner actual" 
                                    class="img-thumbnail" style="max-width: 300px; max-height: 200px;" id="current-image">
                            </div>
                        </div>
                    @endif
                    
                    <div class="mt-3" id="preview-container" style="display: none;">
                        <label>Vista previa:</label>
                        <div>
                            <img id="image-preview" class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Campaña</button>
        <a href="{{ route('inventario.campana.index') }}" class="btn btn-secondary float-right"><i class="fas fa-times"></i>
            Cancelar</a>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    const label = event.target.nextElementSibling;
    const previewContainer = document.getElementById('preview-container');
    const preview = document.getElementById('image-preview');
    const currentImage = document.getElementById('current-image');
    
    if (file) {
        label.textContent = file.name;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
            if (currentImage) {
                currentImage.style.opacity = '0.5';
            }
        };
        reader.readAsDataURL(file);
    } else {
        label.textContent = 'Seleccionar imagen...';
        previewContainer.style.display = 'none';
        if (currentImage) {
            currentImage.style.opacity = '1';
        }
    }
}
</script>



