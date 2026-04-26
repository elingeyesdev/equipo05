<table>
    <thead>
        <tr>
            <th colspan="9" style="background-color: #8B5CF6; color: white; font-size: 16px; font-weight: bold; padding: 10px;">
                REPORTE DE EFECTIVIDAD DE SIMULACIONES
            </th>
        </tr>
        <tr>
            <th colspan="9" style="padding: 5px;">
                Generado: {{ now()->format('d/m/Y H:i') }}
            </th>
        </tr>
        <tr style="background-color: #E5E7EB; font-weight: bold;">
            <th>ID</th>
            <th>Nombre</th>
            <th>Fecha</th>
            <th>Duración (min)</th>
            <th>Riesgo Incendio</th>
            <th>Focos Activos</th>
            <th>Voluntarios</th>
            <th>Temperatura (°C)</th>
            <th>Humedad (%)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($simulations as $sim)
        <tr>
            <td>{{ $sim->id }}</td>
            <td>{{ $sim->nombre ?? 'Sin nombre' }}</td>
            <td>{{ $sim->fecha->format('d/m/Y H:i') }}</td>
            <td>{{ $sim->duracion ?? 0 }}</td>
            <td>{{ number_format($sim->fire_risk ?? 0, 2) }}</td>
            <td>{{ $sim->focos_activos ?? 0 }}</td>
            <td>{{ $sim->num_voluntarios_enviados ?? 0 }}</td>
            <td>{{ number_format($sim->temperature ?? 0, 1) }}</td>
            <td>{{ number_format($sim->humidity ?? 0, 1) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #F3F4F6; font-weight: bold;">
            <td colspan="3">TOTAL SIMULACIONES:</td>
            <td colspan="6">{{ $statistics['total'] ?? 0 }}</td>
        </tr>
        <tr style="background-color: #F3F4F6; font-weight: bold;">
            <td colspan="3">DURACIÓN PROMEDIO (min):</td>
            <td colspan="6">{{ $statistics['avg_duration'] ?? 0 }}</td>
        </tr>
        <tr style="background-color: #F3F4F6; font-weight: bold;">
            <td colspan="3">RIESGO PROMEDIO:</td>
            <td colspan="6">{{ $statistics['avg_fire_risk'] ?? 0 }}</td>
        </tr>
        <tr style="background-color: #F3F4F6; font-weight: bold;">
            <td colspan="3">TOTAL VOLUNTARIOS DESPLEGADOS:</td>
            <td colspan="6">{{ $statistics['total_volunteers'] ?? 0 }}</td>
        </tr>
    </tfoot>
</table>
