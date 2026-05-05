@extends('adminlte::page')

{{-- Extend and customize the browser title --}}

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle') | @yield('subtitle') @endif
@endsection

{{-- Extend and customize the page content header --}}

@section('content_header')
    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title')

            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif
@endsection

{{-- Rename section content to content_body --}}

@section('content')
    @yield('content_body')
@endsection

{{-- Create a common footer --}}

@section('footer')
    <div class="float-right">
        Versión: {{ config('app.version', '1.0.0') }}
    </div>

    <strong>
        Copyright &copy; {{ date('Y') }}
        <a href="{{ config('app.url', '#') }}">
            SIPII - Sistema de Prevención de Incendios
        </a>
    </strong>
@endsection

{{-- Add common Javascript/Jquery code --}}

@push('js')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Chart.js Global Initialization Function -->
<script>
// Global chart instances tracker
window.chartInstances = window.chartInstances || {};

// Center text plugin for gauge charts
const centerTextPlugin = {
    id: 'centerText',
    afterDatasetsDraw(chart, args, options) {
        if (options && options.value !== undefined) {
            const {ctx, chartArea} = chart;
            if (!chartArea) return;

            ctx.save();

            const centerX = (chartArea.left + chartArea.right) / 2;
            const centerY = (chartArea.top + chartArea.bottom) / 2;
            const boxWidth = chartArea.right - chartArea.left;
            const boxHeight = chartArea.bottom - chartArea.top;
            const maxDim = Math.min(boxWidth, boxHeight);

            // Valor principal (más grande) y label con tamaños relativos
            const text = typeof options.value === 'number' ? (Math.round(options.value * 10) / 10).toFixed(1) : options.value.toString();
            const valueFont = Math.max(18, Math.round(maxDim * 0.28));
            const labelFont = Math.max(12, Math.round(valueFont * 0.36));

            // optional subtle shadow for depth
            ctx.shadowColor = 'rgba(0,0,0,0.06)';
            ctx.shadowBlur = 8;

            ctx.font = `700 ${valueFont}px sans-serif`;
            ctx.fillStyle = options.color || '#000';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            // Slightly raise the value so label fits below
            ctx.fillText(text, centerX, centerY - (labelFont * 0.15));

            // Label debajo (más pequeño)
            if (options.label) {
                ctx.font = `${labelFont}px sans-serif`;
                ctx.fillStyle = options.labelColor || '#6c757d';
                ctx.fillText(options.label, centerX, centerY + (valueFont * 0.42));
            }

            ctx.restore();
        }
    }
};

// Global chart initialization function
window.initChart = function(chartId) {
    console.log('🔧 initChart llamado para:', chartId);
    
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js no está disponible');
        return;
    }

    const canvas = document.getElementById(chartId);
    if (!canvas) {
        console.error('❌ Canvas no encontrado:', chartId);
        return;
    }

    // Check if canvas is visible
    if (!canvas.offsetParent) {
        console.warn('⚠️ Canvas no visible (probablemente en tab oculto):', chartId);
        return;
    }

    // Destroy existing chart if any
    if (window.chartInstances[chartId]) {
        console.log('🗑️ Destruyendo chart existente:', chartId);
        window.chartInstances[chartId].destroy();
        delete window.chartInstances[chartId];
    }

    try {
        const chartType = canvas.dataset.chartType;
        
        // Decodificar datos con manejo de errores
        let labels, datasets, options;
        
        try {
            labels = JSON.parse(atob(canvas.dataset.chartLabels || btoa('[]')));
        } catch (e) {
            console.error('❌ Error decodificando labels:', e);
            labels = [];
        }
        
        try {
            datasets = JSON.parse(atob(canvas.dataset.chartDatasets || btoa('[]')));
        } catch (e) {
            console.error('❌ Error decodificando datasets:', e);
            datasets = [];
        }
        
        try {
            options = JSON.parse(atob(canvas.dataset.chartOptions || btoa('{}')));
        } catch (e) {
            console.error('❌ Error decodificando options:', e);
            options = {};
        }
        
        // Asegurar que options sea un objeto, no un array
        if (Array.isArray(options) || typeof options !== 'object' || options === null) {
            console.warn('⚠️ Options no es un objeto válido, usando objeto vacío');
            options = {};
        }

        // Merge defaults to ensure responsive behavior inside cards/tabs
        options = Object.assign({ responsive: true, maintainAspectRatio: false }, options);

        console.log('🔍 Datos decodificados:', {
            chartId,
            chartType,
            labels,
            labelsType: Array.isArray(labels) ? 'array' : typeof labels,
            labelsLength: labels?.length,
            datasets,
            datasetsType: Array.isArray(datasets) ? 'array' : typeof datasets,
            datasetsLength: datasets?.length
        });
        
        // Log detallado de cada dataset
        if (Array.isArray(datasets)) {
            datasets.forEach((ds, idx) => {
                console.log(`🔍 Dataset ${idx}:`, {
                    completo: ds,
                    label: ds?.label,
                    data: ds?.data,
                    dataType: Array.isArray(ds?.data) ? 'array' : typeof ds?.data,
                    dataLength: ds?.data?.length,
                    dataValues: ds?.data
                });
            });
        }

        // Validate data
        if (!Array.isArray(labels)) {
            console.error('❌ Labels no es un array:', labels);
            labels = [];
        }
        
        if (!Array.isArray(datasets)) {
            console.error('❌ Datasets no es un array:', datasets);
            datasets = [];
        }

        // Ensure datasets have valid data arrays
        const validDatasets = datasets.map((dataset, index) => {
            if (!dataset || typeof dataset !== 'object') {
                console.warn(`⚠️ Dataset ${index} es inválido:`, dataset);
                return null;
            }
            
            if (!Array.isArray(dataset.data)) {
                console.warn(`⚠️ Dataset ${index} no tiene data array:`, dataset);
                return null;
            }
            
            // Asegurar que todos los valores de data son números
            const sanitizedData = dataset.data.map(val => {
                const num = Number(val);
                return isNaN(num) ? 0 : num;
            });
            
            return {
                ...dataset,
                data: sanitizedData
            };
        }).filter(d => d !== null);

        if (validDatasets.length === 0) {
            console.warn('⚠️ No hay datasets válidos para el chart:', chartId);
            return;
        }

        console.log('📊 Creando chart con datos validados:', {
            chartId,
            chartType,
            labels,
            datasets: validDatasets
        });

        const config = {
            type: chartType,
            data: { 
                labels: labels, 
                datasets: validDatasets 
            },
            options: options,
            plugins: [centerTextPlugin]
        };
        
        console.log('📋 Configuración completa del chart:', JSON.stringify(config, null, 2));

        window.chartInstances[chartId] = new Chart(canvas.getContext('2d'), config);
        console.log('✅ Chart creado exitosamente:', chartId);
    } catch (error) {
        console.error('❌ Error creando chart:', chartId, error);
        console.error('Stack trace:', error.stack);
    }
};
</script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
<script>
    $(document).ready(function() {
        // Configuración global de DataTables en español
        if ($.fn.DataTable) {
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                }
            });
        }

        // Configuración global de SweetAlert2
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            window.Toast = Toast;
        }
    });
</script>
@endpush

{{-- Add common CSS customizations --}}

@push('css')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
<style type="text/css">
    /* Mejoras visuales para cards */
    .card-header {
        font-weight: 600;
    }
    
    /* Mejoras para tablas */
    .table thead th {
        vertical-align: middle;
    }
    
    /* Botones de acción en tablas */
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
    }

    /* Leaflet map container */
    #map {
        height: 500px;
        width: 100%;
        border-radius: 0.25rem;
    }

    /* Dashboard weather cards */
    .weather-card {
        text-align: center;
        padding: 1rem;
    }
    .weather-card .value {
        font-size: 2rem;
        font-weight: bold;
        color: #17a2b8;
    }
    .weather-card .label {
        font-size: 0.9rem;
        color: #6c757d;
    }

    /* Fix para iconos gigantes de Font Awesome */
    svg.svg-inline--fa {
        max-width: 1em !important;
        max-height: 1em !important;
    }

    /* Purple button styles */
    .btn-purple {
        color: #fff;
        background-color: #6f42c1;
        border-color: #6f42c1;
    }
    .btn-purple:hover {
        color: #fff;
        background-color: #5a32a3;
        border-color: #53299d;
    }
    .btn-purple:focus, .btn-purple.focus {
        color: #fff;
        background-color: #5a32a3;
        border-color: #53299d;
        box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.5);
    }
    .btn-purple.disabled, .btn-purple:disabled {
        color: #fff;
        background-color: #6f42c1;
        border-color: #6f42c1;
    }
    .btn-purple:not(:disabled):not(.disabled):active, 
    .btn-purple:not(:disabled):not(.disabled).active {
        color: #fff;
        background-color: #53299d;
        border-color: #4c2791;
    }
    
    /* Ocultar pseudo-elementos problemáticos */
    body::after, body::before,
    .content-wrapper::after, .content-wrapper::before {
        display: none !important;
    }

    /* Dashboard equal cards styling */
    .dashboard-cards-row > .col-md-6 {
        display: flex;
    }

    .dashboard-equal-card .card {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .dashboard-equal-card .card-body {
        flex: 1 1 auto;
    }

    /* Remove inner white backgrounds inside these dashboard cards */
    .dashboard-equal-card .info-box {
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
    }

    .dashboard-equal-card .info-box .info-box-content {
        color: #343a40;
    }
</style>
@endpush
