<table>
    <thead>
        <tr>
            <th colspan="6" style="background-color: #4A5568; color: white; font-size: 16px; font-weight: bold; padding: 10px;">
                REPORTE DE ACTIVIDAD DE INCENDIOS
            </th>
        </tr>
        <tr>
            <th colspan="6" style="padding: 5px;">
                Período: {{ $filters['fecha_inicio'] ?? 'N/A' }} - {{ $filters['fecha_fin'] ?? 'N/A' }}
            </th>
        </tr>
        <tr style="background-color: #E5E7EB; font-weight: bold;">
            <th>Fecha</th>
            <th>Ubicación</th>
            <th>Intensidad</th>
            <th>Biomasa Relacionada</th>
            <th>Tipo Biomasa</th>
            <th>Reportado Por</th>
        </tr>
    </thead>
    <tbody>
        @foreach($fires as $fire)
        <tr>
            <td>{{ $fire->fecha->format('d/m/Y H:i') }}</td>
            <td>{{ $fire->ubicacion ?? 'Sin ubicación' }}</td>
            <td>{{ number_format($fire->intensidad, 2) }}</td>
            <td>{{ $fire->biomasa->ubicacion ?? 'N/A' }}</td>
            <td>{{ $fire->biomasa->tipoBiomasa->tipo_biomasa ?? 'N/A' }}</td>
            <td>{{ $fire->reporter->name ?? 'Sistema' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #F3F4F6; font-weight: bold;">
            <td colspan="2">TOTAL DE FOCOS:</td>
            <td colspan="4">{{ $statistics['total'] ?? 0 }}</td>
        </tr>
        <tr style="background-color: #F3F4F6; font-weight: bold;">
            <td colspan="2">INTENSIDAD PROMEDIO:</td>
            <td colspan="4">{{ $statistics['avg_intensity'] ?? 0 }}</td>
        </tr>
        <tr style="background-color: #F3F4F6; font-weight: bold;">
            <td colspan="2">INTENSIDAD MÁXIMA:</td>
            <td colspan="4">{{ $statistics['max_intensity'] ?? 0 }}</td>
        </tr>
    </tfoot>
</table>
