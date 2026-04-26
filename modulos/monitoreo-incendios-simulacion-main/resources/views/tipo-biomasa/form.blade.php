<div class="row">
    <div class="col-md-12">
        <x-adminlte-input name="tipo_biomasa" label="Tipo de Biomasa" 
            placeholder="Ej: Bosque, Sabana, Pastizal" 
            value="{{ old('tipo_biomasa', $tipoBiomasa?->tipo_biomasa) }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-leaf text-success"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-12">
        <x-adminlte-input-color name="color" label="Color Representativo" 
            value="{{ old('color', $tipoBiomasa?->color ?? '#4CAF50') }}" enable-old-support>
            <x-slot name="bottomSlot">
                <small class="form-text text-muted">
                    Este color se utilizará para identificar visualmente este tipo de biomasa en el mapa
                </small>
            </x-slot>
        </x-adminlte-input-color>
    </div>

    <div class="col-md-12">
        <x-adminlte-input-slider name="modificador_intensidad" 
            label="Factor de Propagación del Fuego" 
            value="{{ old('modificador_intensidad', $tipoBiomasa?->modificador_intensidad ?? 1.0) }}" 
            min="0.5" max="2.0" step="0.1" 
            color="orange" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-fire text-danger"></i>
                </div>
            </x-slot>
            <x-slot name="appendSlot">
                <div class="input-group-text">x</div>
            </x-slot>
            <x-slot name="bottomSlot">
                <small class="form-text text-muted">
                    Indica la velocidad a la que el fuego se propaga en esta biomasa. 
                    <strong>0.5x</strong> = Muy lento (áreas rocosas), 
                    <strong>1.0x</strong> = Estándar, 
                    <strong>2.0x</strong> = Muy rápido (bosque seco)
                </small>
            </x-slot>
        </x-adminlte-input-slider>
    </div>

    <div class="col-12 mt-3">
        <x-adminlte-button type="submit" label="Guardar" theme="primary" icon="fas fa-save"/>
        <a href="{{ route('tipo-biomasas.index') }}" class="btn btn-danger "><i class="fas fa-arrow-left"></i> Cancelar</a>
    </div>
</div>
