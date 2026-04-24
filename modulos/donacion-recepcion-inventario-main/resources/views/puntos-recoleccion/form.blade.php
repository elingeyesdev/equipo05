<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Información del Punto de Recolección</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nombre">Nombre del Punto <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                        </div>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre', $puntosRecoleccion?->nombre) }}" id="nombre"
                            placeholder="Ej: Sede Central" required>
                        @error('nombre')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                        </div>
                        <input type="text" name="direccion"
                            class="form-control @error('direccion') is-invalid @enderror"
                            value="{{ old('direccion', $puntosRecoleccion?->direccion) }}" id="direccion"
                            placeholder="Ej: Av. Principal #123" required>
                        @error('direccion')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="contacto">Contacto</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        </div>
                        <input type="text" name="contacto" class="form-control @error('contacto') is-invalid @enderror"
                            value="{{ old('contacto', $puntosRecoleccion?->contacto) }}" id="contacto"
                            placeholder="Ej: Juan Pérez - 77712345">
                        @error('contacto')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="latitud">Latitud</label>
                            <input type="text" name="latitud" class="form-control"
                                value="{{ old('latitud', $puntosRecoleccion?->latitud) }}" id="latitud" readonly>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="longitud">Longitud</label>
                            <input type="text" name="longitud" class="form-control"
                                value="{{ old('longitud', $puntosRecoleccion?->longitud) }}" id="longitud" readonly>
                        </div>
                    </div>
                </div>
                <small class="text-muted"><i class="fas fa-info-circle"></i> Haga clic en el mapa para seleccionar la
                    ubicación exacta.</small>
            </div>

            <div class="col-md-6">
                <label>Ubicación en el Mapa</label>
                <div id="map" style="height: 400px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Punto</button>
        <a href="{{ route('inventario.puntos-recoleccion.index') }}" class="btn btn-secondary float-right"><i
                class="fas fa-times"></i> Cancelar</a>
    </div>
</div>



