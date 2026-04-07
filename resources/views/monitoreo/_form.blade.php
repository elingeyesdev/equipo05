@csrf

<div class="field">
    <label for="titulo">Titulo</label>
    <input id="titulo" name="titulo" type="text" value="{{ old('titulo', $incendio->titulo ?? '') }}" required>
    @error('titulo') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
    <label for="descripcion">Descripcion</label>
    <textarea id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $incendio->descripcion ?? '') }}</textarea>
    @error('descripcion') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="field" style="flex:1;min-width:180px;">
        <label for="latitud">Latitud</label>
        <input id="latitud" name="latitud" type="number" step="0.0000001" value="{{ old('latitud', $incendio->latitud ?? '') }}" required>
        @error('latitud') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field" style="flex:1;min-width:180px;">
        <label for="longitud">Longitud</label>
        <input id="longitud" name="longitud" type="number" step="0.0000001" value="{{ old('longitud', $incendio->longitud ?? '') }}" required>
        @error('longitud') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="field" style="flex:1;min-width:180px;">
        <label for="estado">Estado</label>
        <select id="estado" name="estado" required>
            @foreach (['activo', 'controlado', 'extinguido'] as $estado)
                <option value="{{ $estado }}" @selected(old('estado', $incendio->estado ?? 'activo') === $estado)>{{ ucfirst($estado) }}</option>
            @endforeach
        </select>
        @error('estado') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field" style="flex:1;min-width:180px;">
        <label for="nivel_riesgo">Nivel de riesgo</label>
        <select id="nivel_riesgo" name="nivel_riesgo" required>
            @foreach (['bajo', 'medio', 'alto'] as $riesgo)
                <option value="{{ $riesgo }}" @selected(old('nivel_riesgo', $incendio->nivel_riesgo ?? 'medio') === $riesgo)>{{ ucfirst($riesgo) }}</option>
            @endforeach
        </select>
        @error('nivel_riesgo') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="field" style="flex:1;min-width:220px;">
        <label for="fecha_inicio">Fecha inicio</label>
        <input id="fecha_inicio" name="fecha_inicio" type="datetime-local" value="{{ old('fecha_inicio', isset($incendio?->fecha_inicio) ? $incendio->fecha_inicio->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
        @error('fecha_inicio') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field" style="flex:1;min-width:220px;">
        <label for="fecha_fin">Fecha fin</label>
        <input id="fecha_fin" name="fecha_fin" type="datetime-local" value="{{ old('fecha_fin', isset($incendio?->fecha_fin) ? $incendio->fecha_fin->format('Y-m-d\TH:i') : '') }}">
        @error('fecha_fin') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <button class="btn" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-light" href="{{ route('monitoreo.index') }}">Cancelar</a>
</div>
