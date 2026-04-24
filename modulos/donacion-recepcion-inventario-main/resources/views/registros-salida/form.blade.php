<div class="row">
    <div class="col-md-12">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Información de Salida</h3>
            </div>
            <div class="card-body">
                
                <!-- Alerta para datos encontrados del sistema externo -->
                <div id="alert-datos-externos" class="alert alert-info" style="display: none;">
                    <i class="fas fa-info-circle"></i> <span id="alert-mensaje"></span>
                </div>
                
                <div class="form-group">
                    <label for="id_paquete">Paquete a Enviar</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-box"></i></span>
                        </div>
                        <select name="id_paquete" class="form-control @error('id_paquete') is-invalid @enderror" id="id_paquete" required>
                            <option value="" data-codigo="">Seleccione un paquete</option>
                            @foreach($paquetes as $paquete)
                                <option value="{{ $paquete->id_paquete }}" 
                                    data-codigo="{{ $paquete->codigo_paquete }}"
                                    {{ old('id_paquete', $registrosSalida->id_paquete) == $paquete->id_paquete ? 'selected' : '' }}>
                                    {{ $paquete->codigo_paquete }} - (Creado: {{ \Carbon\Carbon::parse($paquete->fecha_creacion)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('id_paquete')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="fecha_salida">Fecha y Hora de Salida</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="datetime-local" name="fecha_salida" 
                            class="form-control @error('fecha_salida') is-invalid @enderror" 
                            value="{{ old('fecha_salida', $registrosSalida->fecha_salida ? \Carbon\Carbon::parse($registrosSalida->fecha_salida)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" 
                            id="fecha_salida" required>
                    </div>
                    @error('fecha_salida')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="destino">Destino / Beneficiario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <input type="text" name="destino" 
                            class="form-control @error('destino') is-invalid @enderror" 
                            value="{{ old('destino', $registrosSalida->destino) }}" 
                            id="destino" placeholder="Ingrese el destino o nombre del beneficiario" required>
                    </div>
                    @error('destino')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="encargado">Encargado de Entrega</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" name="encargado" 
                            class="form-control @error('encargado') is-invalid @enderror" 
                            value="{{ old('encargado', $registrosSalida->encargado) }}" 
                            id="encargado" placeholder="Nombre del encargado de la entrega">
                    </div>
                    @error('encargado')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                        </div>
                        <textarea name="observaciones" 
                            class="form-control @error('observaciones') is-invalid @enderror" 
                            id="observaciones" placeholder="Observaciones adicionales..." rows="3">{{ old('observaciones', $registrosSalida->observaciones) }}</textarea>
                    </div>
                    @error('observaciones')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Registro</button>
                <a href="{{ route('inventario.registros-salida.index') }}" class="btn btn-secondary float-right"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectPaquete = document.getElementById('id_paquete');
    const inputDestino = document.getElementById('destino');
    const inputEncargado = document.getElementById('encargado');
    const alertDatos = document.getElementById('alert-datos-externos');
    const alertMensaje = document.getElementById('alert-mensaje');
    const apiBaseUrl = '{{ env("API_BASE_URL_ADS") }}';

    selectPaquete.addEventListener('change', async function() {
        const selectedOption = this.options[this.selectedIndex];
        const codigoPaquete = selectedOption.dataset.codigo;

        // Ocultar alerta
        alertDatos.style.display = 'none';

        if (!codigoPaquete) return;

        try {
            const response = await fetch(`${apiBaseUrl}/api/gateway/logistica/paquetes/destino-voluntario/${codigoPaquete}`);
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.success && data.data) {
                    // Mostrar alerta de datos encontrados
                    alertMensaje.textContent = `Se encontraron datos asociados al paquete ${codigoPaquete} en el sistema externo.`;
                    alertDatos.style.display = 'block';

                    // Llenar campos con datos del endpoint
                    if (data.data.destino && data.data.destino.direccion) {
                        inputDestino.value = data.data.destino.direccion;
                    }

                    if (data.data.encargado && data.data.encargado.completo) {
                        inputEncargado.value = data.data.encargado.completo;
                    }
                }
            }
        } catch (error) {
            console.log('No se pudo conectar con el sistema externo:', error);
        }
    });
});
</script>
@endpush





