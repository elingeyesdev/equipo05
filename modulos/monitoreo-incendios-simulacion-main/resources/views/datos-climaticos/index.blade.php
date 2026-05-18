@extends('layouts.app')

@section('subtitle', 'Datos Climáticos')
@section('content_header_title', 'Datos Climáticos Históricos')
@section('content_header_subtitle')
    Chiquitanía, Santa Cruz de la Sierra y localidades de referencia - Última semana
@endsection

@section('content_body')
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info mb-0 py-2 d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-calendar-alt"></i>
                    <strong>Período:</strong> Últimos 7 días
                    <span id="loading-indicator" class="ml-3 text-primary" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Cargando...
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <select id="ubicacion-select" class="form-control form-control-sm" style="width: auto; min-width: 200px;">
                        @foreach($ubicaciones as $key => $ubic)
                            <option value="{{ $key }}"
                                    data-lat="{{ $ubic['lat'] }}"
                                    data-lng="{{ $ubic['lng'] }}"
                                    {{ $ubicacionKey === $key ? 'selected' : '' }}>
                                {{ $ubic['nombre'] }}, Bolivia
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- 6 tarjetas uniformes --}}
    <div class="row clima-summary-row" id="summary-cards">
        <div class="col-6 col-md-4 col-xl-2 clima-summary-col">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="feels-current">
                        @if(isset($datosGraficas['sensacion_actual']))
                            {{ $datosGraficas['sensacion_actual'] }}&deg;C
                        @else
                            <i class="fas fa-spinner fa-spin"></i>
                        @endif
                    </h3>
                    <p>Sensaci&oacute;n t&eacute;rmica</p>
                </div>
                <div class="icon"><i class="fas fa-temperature-low"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2 clima-summary-col">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="temp-current">
                        @if(isset($datosGraficas['temperatura_actual']))
                            {{ $datosGraficas['temperatura_actual'] }}&deg;C
                        @else
                            <i class="fas fa-spinner fa-spin"></i>
                        @endif
                    </h3>
                    <p>Temperatura</p>
                </div>
                <div class="icon"><i class="fas fa-temperature-high"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2 clima-summary-col">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="humidity-current">
                        @if(isset($datosGraficas['humedad_actual']))
                            {{ $datosGraficas['humedad_actual'] }}%
                        @else
                            <i class="fas fa-spinner fa-spin"></i>
                        @endif
                    </h3>
                    <p>Humedad</p>
                </div>
                <div class="icon"><i class="fas fa-tint"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2 clima-summary-col">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="precip-current">
                        @if(isset($datosGraficas['precip_actual']))
                            {{ number_format((float) $datosGraficas['precip_actual'], 1) }} mm
                        @else
                            <i class="fas fa-spinner fa-spin"></i>
                        @endif
                    </h3>
                    <p>Precipitaci&oacute;n</p>
                </div>
                <div class="icon"><i class="fas fa-cloud-rain"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2 clima-summary-col">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="wind-current">
                        @if(isset($datosGraficas['viento_actual']))
                            {{ $datosGraficas['viento_actual'] }} km/h
                        @else
                            <i class="fas fa-spinner fa-spin"></i>
                        @endif
                    </h3>
                    <p>Viento</p>
                </div>
                <div class="icon"><i class="fas fa-wind"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2 clima-summary-col">
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3 id="gust-current">
                        @if(isset($datosGraficas['rafagas_actual']))
                            {{ $datosGraficas['rafagas_actual'] }} km/h
                        @else
                            <i class="fas fa-spinner fa-spin"></i>
                        @endif
                    </h3>
                    <p>R&aacute;fagas de viento</p>
                </div>
                <div class="icon"><i class="fas fa-wind"></i></div>
            </div>
        </div>
    </div>

    <div class="row clima-charts-row">
        <div class="col-lg-6 mb-3">
            <x-adminlte-card title="Temperatura Horaria" theme="danger" icon="fas fa-temperature-high" class="clima-chart-card h-100">
                <div class="clima-chart-wrap"><canvas id="temperaturaChart"></canvas></div>
            </x-adminlte-card>
        </div>
        <div class="col-lg-6 mb-3">
            <x-adminlte-card title="Sensaci&oacute;n t&eacute;rmica (ajuste tropical)" theme="warning" icon="fas fa-temperature-low" class="clima-chart-card h-100">
                <div class="clima-chart-wrap"><canvas id="sensacionChart"></canvas></div>
            </x-adminlte-card>
        </div>
    </div>

    <div class="row clima-charts-row">
        <div class="col-lg-6 mb-3">
            <x-adminlte-card title="Humedad Relativa" theme="info" icon="fas fa-tint" class="clima-chart-card h-100">
                <div class="clima-chart-wrap"><canvas id="humedadChart"></canvas></div>
            </x-adminlte-card>
        </div>
        <div class="col-lg-6 mb-3">
            <x-adminlte-card title="Precipitaci&oacute;n" theme="primary" icon="fas fa-cloud-rain" class="clima-chart-card h-100">
                <div class="clima-chart-wrap"><canvas id="precipitacionChart"></canvas></div>
            </x-adminlte-card>
        </div>
    </div>

    <div class="row clima-charts-row mb-2">
        <div class="col-lg-6 mb-3">
            <x-adminlte-card title="Velocidad del Viento" theme="success" icon="fas fa-wind" class="clima-chart-card h-100">
                <div class="clima-chart-wrap"><canvas id="vientoChart"></canvas></div>
            </x-adminlte-card>
        </div>
        <div class="col-lg-6 mb-3">
            <x-adminlte-card title="R&aacute;fagas de Viento" theme="teal" icon="fas fa-wind" class="clima-chart-card h-100">
                <div class="clima-chart-wrap"><canvas id="rafagasChart"></canvas></div>
            </x-adminlte-card>
        </div>
    </div>
@endsection

@push('css')
<style>
    #ubicacion-select { transition: all 0.3s ease; font-weight: 500; }
    #ubicacion-select:focus { box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); }
    #loading-indicator { animation: pulse 1s infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

    .clima-summary-row { margin-left: -8px; margin-right: -8px; }
    .clima-summary-col { padding-left: 8px; padding-right: 8px; margin-bottom: 16px; display: flex; }
    .clima-summary-col .small-box {
        width: 100%; margin-bottom: 0; min-height: 118px;
        display: flex; flex-direction: column; justify-content: center;
    }
    .clima-summary-col .small-box .inner { padding: 12px 14px; }
    .clima-summary-col .small-box .inner h3 { font-size: 1.75rem; margin: 0 0 4px; }
    .clima-summary-col .small-box .inner p { margin: 0; font-size: 0.95rem; }
    .clima-summary-col .small-box .icon { font-size: 56px; top: 8px; }

    .bg-teal { background-color: #20c997 !important; color: #fff; }
    .bg-teal .icon { color: rgba(255,255,255,0.25); }

    .clima-chart-card .card-body { padding: 12px 16px 16px; }
    .clima-chart-wrap { position: relative; height: 220px; width: 100%; }
    .clima-chart-wrap canvas { max-height: 220px; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentData = @json($datosGraficas);
    let tempChart, sensacionChart, humChart, precipChart, windChart, gustChart;

    function calcSensacionTermica(temp, hum, wind, gust, precip, aparenteApi) {
        let base = aparenteApi != null ? aparenteApi : (temp + 0.33 * ((hum/100)*6.105*Math.exp((17.27*temp)/(237.7+temp))) - 0.70*Math.max(0.1,wind) - 4);
        const vEff = Math.max(wind, (gust||0)*0.85);
        if (temp <= 22) {
            base -= Math.max(0, (hum-50)*0.14);
            if (precip > 0) base -= Math.min(4, precip*0.6);
            if (vEff >= 12) base -= Math.min(3, (vEff-10)*0.15);
            if (temp <= 16 && hum >= 60) base -= 2.5;
        }
        return Math.round(Math.max(-15, Math.min(55, base))*10)/10;
    }

    function buildSensacionSerie(data) {
        const serie = data.sensacion_termica;
        if (serie && serie.length && serie.some(v => v != null)) {
            return serie;
        }
        return (data.temperatura || []).map((t, i) => {
            if (t == null) return null;
            return calcSensacionTermica(
                t,
                data.humedad?.[i] ?? 50,
                data.viento?.[i] ?? 0,
                data.rafagas?.[i] ?? 0,
                data.precipitacion?.[i] ?? 0,
                null
            );
        });
    }

    function formatLabels(labels) {
        return labels.map(label => {
            const fecha = new Date(label);
            const dia = fecha.getDate().toString().padStart(2, '0');
            const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
            const hora = fecha.getHours().toString().padStart(2, '0');
            return `${dia}/${mes} ${hora}:00`;
        });
    }

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 600, easing: 'easeInOutQuart' },
        plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
        scales: { x: { ticks: { maxRotation: 45, minRotation: 45, maxTicksLimit: 24 } } }
    };

    function yScale(minZero, title) {
        return {
            ...commonOptions.scales,
            y: { beginAtZero: minZero, title: { display: true, text: title } }
        };
    }

    function initCharts() {
        const labels = formatLabels(currentData.labels || []);
        const sensacionData = buildSensacionSerie(currentData);

        tempChart = new Chart(document.getElementById('temperaturaChart'), {
            type: 'line',
            data: { labels, datasets: [{ label: 'Temperatura', data: currentData.temperatura, borderColor: 'rgb(255,99,132)', backgroundColor: 'rgba(255,99,132,0.1)', fill: true, tension: 0.4, spanGaps: true }] },
            options: { ...commonOptions, scales: yScale(false, 'Temperatura (C)') }
        });

        sensacionChart = new Chart(document.getElementById('sensacionChart'), {
            type: 'line',
            data: { labels, datasets: [{ label: 'Sensacion', data: sensacionData, borderColor: 'rgb(255,159,64)', backgroundColor: 'rgba(255,159,64,0.15)', fill: true, tension: 0.4, spanGaps: true }] },
            options: { ...commonOptions, scales: yScale(false, 'Sensacion (C)') }
        });

        humChart = new Chart(document.getElementById('humedadChart'), {
            type: 'line',
            data: { labels, datasets: [{ label: 'Humedad', data: currentData.humedad, borderColor: 'rgb(54,162,235)', backgroundColor: 'rgba(54,162,235,0.1)', fill: true, tension: 0.4 }] },
            options: { ...commonOptions, scales: { ...yScale(true, 'Humedad (%)'), y: { ...yScale(true, 'Humedad (%)').y, max: 100 } } }
        });

        precipChart = new Chart(document.getElementById('precipitacionChart'), {
            type: 'bar',
            data: { labels, datasets: [{ label: 'Precipitacion', data: currentData.precipitacion, backgroundColor: 'rgba(75,192,192,0.6)', borderColor: 'rgb(75,192,192)', borderWidth: 1 }] },
            options: { ...commonOptions, scales: yScale(true, 'Precipitacion (mm)') }
        });

        windChart = new Chart(document.getElementById('vientoChart'), {
            type: 'line',
            data: { labels, datasets: [{ label: 'Viento', data: currentData.viento, borderColor: 'rgb(40,167,69)', backgroundColor: 'rgba(40,167,69,0.1)', fill: true, tension: 0.4, spanGaps: true }] },
            options: { ...commonOptions, scales: yScale(true, 'Viento (km/h)') }
        });

        gustChart = new Chart(document.getElementById('rafagasChart'), {
            type: 'line',
            data: { labels, datasets: [{ label: 'Rafagas', data: currentData.rafagas, borderColor: 'rgb(32,201,151)', backgroundColor: 'rgba(32,201,151,0.15)', fill: true, tension: 0.4, spanGaps: true }] },
            options: { ...commonOptions, scales: yScale(true, 'Rafagas (km/h)') }
        });
    }

    async function updateSummaryCards(lat, lng) {
        try {
            const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current=temperature_2m,relative_humidity_2m,precipitation,wind_speed_10m,wind_gusts_10m,apparent_temperature,uv_index&timezone=America/La_Paz`;
            const result = await (await fetch(url)).json();
            if (result.current) {
                const t = result.current.temperature_2m ?? 0;
                const h = result.current.relative_humidity_2m ?? 0;
                const w = result.current.wind_speed_10m ?? 0;
                const g = result.current.wind_gusts_10m ?? (w * 1.35);
                const p = result.current.precipitation ?? 0;
                document.getElementById('temp-current').textContent = t.toFixed(1) + '°C';
                document.getElementById('feels-current').textContent = calcSensacionTermica(t, h, w, g, p, result.current.apparent_temperature).toFixed(1) + '°C';
                document.getElementById('humidity-current').textContent = h.toFixed(0) + '%';
                document.getElementById('precip-current').textContent = p.toFixed(1) + ' mm';
                document.getElementById('wind-current').textContent = w.toFixed(1) + ' km/h';
                document.getElementById('gust-current').textContent = g.toFixed(1) + ' km/h';
            }
        } catch (e) {
            ['temp-current','feels-current','humidity-current','precip-current','wind-current','gust-current'].forEach(id => {
                document.getElementById(id).textContent = 'N/A';
            });
        }
    }

    document.getElementById('ubicacion-select').addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('ubicacion', this.value);
        window.location.href = url.toString();
    });

    initCharts();
    const sel = document.getElementById('ubicacion-select');
    const opt = sel.options[sel.selectedIndex];
    updateSummaryCards(parseFloat(opt.dataset.lat), parseFloat(opt.dataset.lng));
});
</script>
@endpush
