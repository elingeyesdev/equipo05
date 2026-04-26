<table>
    <thead>
        <tr>
            <th colspan="8" style="background-color: #10B981; color: white; font-size: 16px; font-weight: bold; padding: 10px;">
                REPORTE DE GESTIÓN DE BIOMASAS
            </th>
        </tr>
        <tr>
            <th colspan="8" style="padding: 5px;">
                Generado: {{ now()->format('d/m/Y H:i') }}
            </th>
        </tr>
        <tr style="background-color: #E5E7EB; font-weight: bold;">
            <th>ID</th>
            <th>Ubicación</th>
            <th>Área (ha)</th>
            <th>Densidad</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Creado Por</th>
            <th>Fecha Creación</th>
        </tr>
    </thead>
    <tbody>
        @foreach($biomasas as $biomasa)
        <tr>
            <td>{{ $biomasa->id }}</td>
            <td>{{ $biomasa->ubicacion ?? 'Sin ubicación' }}</td>
            <td>{{ number_format($biomasa->area_m2 / 10000, 2) }}</td>
            <td>{{ ucfirst($biomasa->densidad ?? 'N/A') }}</td>
            <td>{{ $biomasa->tipoBiomasa->tipo_biomasa ?? 'N/A' }}</td>
            <td>{{ ucfirst($biomasa->estado) }}</td>
            <td>{{ $biomasa->user->name ?? 'N/A' }}</td>
            <td>{{ $biomasa->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #F3F4F6; font-weight: bold;">
            <td colspan="2">TOTAL BIOMASAS:</td>
            <td colspan="6">{{ $statistics['total'] ?? 0 }}</td>
        </tr>
        <tr style="background-color: #F3F4F6; font-weight: bold;">
            <td colspan="2">APROBADAS:</td>
            <td colspan="6">{{ $statistics['aprobadas'] ?? 0 }}</td>
        </tr>
        <tr style="background-color: #F3F4F6; font-weight: bold;">
            <td colspan="2">PENDIENTES:</td>
            <td colspan="6">{{ $statistics['pendientes'] ?? 0 }}</td>
        </tr>
        <tr style="background-color: #F3F4F6; font-weight: bold;">
            <td colspan="2">ÁREA TOTAL (ha):</td>
            <td colspan="6">{{ $statistics['area_total_ha'] ?? 0 }}</td>
        </tr>
    </tfoot>
</table>
