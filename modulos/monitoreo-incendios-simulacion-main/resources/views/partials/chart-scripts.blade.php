{{-- Chart.js + initChart para vistas incendios que usan layouts.app del sistema principal --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
window.chartInstances = window.chartInstances || {};

window.initChart = function(chartId, force) {
    if (typeof Chart === 'undefined') {
        return;
    }

    const canvas = document.getElementById(chartId);
    if (!canvas) {
        return;
    }

    if (!force && !canvas.offsetParent) {
        return;
    }

    if (window.chartInstances[chartId]) {
        window.chartInstances[chartId].destroy();
        delete window.chartInstances[chartId];
    }

    try {
        const chartType = canvas.dataset.chartType || 'line';
        const labels = JSON.parse(atob(canvas.dataset.chartLabels || btoa('[]')));
        let datasets = JSON.parse(atob(canvas.dataset.chartDatasets || btoa('[]')));
        let options = JSON.parse(atob(canvas.dataset.chartOptions || btoa('{}')));

        if (!Array.isArray(options) || typeof options !== 'object' || options === null) {
            options = {};
        }

        options = Object.assign({ responsive: true, maintainAspectRatio: false }, options);

        if (!Array.isArray(datasets)) {
            datasets = [];
        }

        const validDatasets = datasets.map((dataset) => {
            if (!dataset || !Array.isArray(dataset.data)) {
                return null;
            }
            return {
                ...dataset,
                data: dataset.data.map((v) => {
                    const n = Number(v);
                    return Number.isNaN(n) ? 0 : n;
                }),
            };
        }).filter(Boolean);

        if (validDatasets.length === 0) {
            return;
        }

        window.chartInstances[chartId] = new Chart(canvas.getContext('2d'), {
            type: chartType,
            data: { labels, datasets: validDatasets },
            options,
        });
    } catch (e) {
        console.error('Error al crear gráfico:', chartId, e);
    }
};

window.initIncendiosCharts = function(container) {
    const root = container ? document.querySelector(container) : document;
    if (!root) {
        return;
    }
    root.querySelectorAll('canvas[data-chart-type]').forEach((canvas) => {
        window.initChart(canvas.id, true);
    });
};
</script>
