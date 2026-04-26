<div class="row padding-1 p-1">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $simulacione->nombre) }}" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="fecha">Fecha</label>
            <input type="datetime-local" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', optional($simulacione->fecha)->format('Y-m-d\TH:i')) }}">
            @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="duracion">Duración (min)</label>
            <input type="number" name="duracion" id="duracion" class="form-control @error('duracion') is-invalid @enderror" value="{{ old('duracion', $simulacione->duracion) }}" min="0">
            @error('duracion')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="focos_activos">Focos activos</label>
            <input type="number" name="focos_activos" id="focos_activos" class="form-control @error('focos_activos') is-invalid @enderror" value="{{ old('focos_activos', $simulacione->focos_activos) }}" min="0">
            @error('focos_activos')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="num_voluntarios_enviados">Voluntarios enviados</label>
            <input type="number" name="num_voluntarios_enviados" id="num_voluntarios_enviados" class="form-control @error('num_voluntarios_enviados') is-invalid @enderror" value="{{ old('num_voluntarios_enviados', $simulacione->num_voluntarios_enviados) }}" min="0">
            @error('num_voluntarios_enviados')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror">
                @php($estadoVal = old('estado', $simulacione->estado))
                <option value="pendiente" {{ $estadoVal==='pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="en_progreso" {{ $estadoVal==='en_progreso' ? 'selected' : '' }}>En progreso</option>
                <option value="completada" {{ $estadoVal==='completada' ? 'selected' : '' }}>Completada</option>
            </select>
            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>