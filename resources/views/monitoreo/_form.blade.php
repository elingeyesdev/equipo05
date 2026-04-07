<<<<<<< HEAD
@php
    $i = $incendio ?? null;
    $defaultLat = '-34.603722';
    $defaultLng = '-58.381592';
@endphp

<form action="{{ $action }}" method="post" class="form-grid">
    @csrf
    @if (($method ?? 'POST') === 'PUT')
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="titulo">Título</label>
        <input type="text" id="titulo" name="titulo" value="{{ old('titulo', optional($i)->titulo ?? '') }}" required maxlength="255" autocomplete="off">
        @error('titulo')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="4" placeholder="Detalle del incidente (opcional)">{{ old('descripcion', optional($i)->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="estado">Estado</label>
        <select id="estado" name="estado" required>
            @foreach (['activo' => 'Activo', 'controlado' => 'Controlado', 'extinguido' => 'Extinguido'] as $val => $label)
                <option value="{{ $val }}" @selected(old('estado', optional($i)->estado ?? 'activo') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        @error('estado')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="nivel_riesgo">Nivel de riesgo</label>
        <select id="nivel_riesgo" name="nivel_riesgo" required>
            @foreach (['bajo' => 'Bajo', 'medio' => 'Medio', 'alto' => 'Alto'] as $val => $label)
                <option value="{{ $val }}" @selected(old('nivel_riesgo', optional($i)->nivel_riesgo ?? 'medio') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        @error('nivel_riesgo')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="fecha_inicio">Fecha y hora de inicio</label>
        <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', ($i && $i->fecha_inicio) ? $i->fecha_inicio->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
        @error('fecha_inicio')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="fecha_fin">Fecha y hora de fin <span style="font-weight:400;text-transform:none;color:var(--text-muted)">(opcional)</span></label>
        <input type="datetime-local" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin', ($i && $i->fecha_fin) ? $i->fecha_fin->format('Y-m-d\TH:i') : '') }}">
        @error('fecha_fin')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $i ? (string) $i->latitud : $defaultLat) }}">
    <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $i ? (string) $i->longitud : $defaultLng) }}">

    <div class="form-group">
        <label>Ubicación en el mapa</label>
        <p class="hint" style="margin:0 0 0.5rem;font-size:0.88rem;color:var(--text-muted)">Hacé clic en el mapa o arrastrá el marcador para indicar dónde se registra el incendio.</p>
        <div id="map-picker"></div>
        <p class="coord-readout" id="coord-readout"></p>
        @error('latitud')
            <div class="error">{{ $message }}</div>
        @enderror
        @error('longitud')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div style="display:flex;flex-wrap:wrap;gap:0.65rem;margin-top:0.5rem">
        <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Guardar' }}</button>
        <a href="{{ route('home') }}" class="btn btn-ghost">Cancelar</a>
    </div>
</form>
=======
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
>>>>>>> origin/santiago
