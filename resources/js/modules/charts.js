/* ==========================================================================
   GRAFICI (Chart.js). Ogni <canvas data-chart='{"type": ...}'> diventa un grafico.
   Tipi: bars (frequenza), lift (peso medio + massimo), body (peso corporeo)
   ========================================================================== */
import {
    Chart, BarController, BarElement, LineController, LineElement, PointElement,
    CategoryScale, LinearScale, Tooltip, Filler,
} from 'chart.js';

Chart.register(BarController, BarElement, LineController, LineElement, PointElement, CategoryScale, LinearScale, Tooltip, Filler);

const css = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim();

function applyDefaults() {
    Chart.defaults.font.family = '"IBM Plex Mono", monospace';
    Chart.defaults.font.size = 11;
    Chart.defaults.color = css('--color-polvere');
    Chart.defaults.borderColor = css('--color-riga');
    Chart.defaults.maintainAspectRatio = false;
    Object.assign(Chart.defaults.plugins.tooltip, {
        backgroundColor: css('--color-inchiostro'),
        titleColor: '#ffffffaa',
        bodyColor: '#fff',
        padding: 10,
        cornerRadius: 3,
        displayColors: false,
    });
}

const scales = (extra = {}) => ({
    x: { grid: { display: false }, border: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
    y: { border: { display: false }, ticks: { maxTicksLimit: 5 }, ...extra },
});

function barsChart(canvas, config) {
    const ink = css('--color-inchiostro');
    const red = css('--color-disco-rosso');
    const dataFor = (rows) => ({
        labels: rows.map((r) => r.label),
        datasets: [{
            data: rows.map((r) => r.value),
            backgroundColor: rows.map((_, i) => (i === rows.length - 1 ? red : ink)),
            borderRadius: 2,
            maxBarThickness: 22,
        }],
    });

    const chart = new Chart(canvas, {
        type: 'bar',
        data: dataFor(config.weeks),
        options: {
            scales: scales({ beginAtZero: true, suggestedMax: 4, ticks: { stepSize: 1, precision: 0 } }),
            plugins: { tooltip: { callbacks: { label: (c) => `${c.parsed.y} allenamenti` } } },
        },
    });

    // bottoni Settimane / Mesi
    const panel = canvas.closest('[data-frequency]');
    panel?.querySelectorAll('[data-frequency-tab]').forEach((button) => {
        button.addEventListener('click', () => {
            const view = button.dataset.frequencyTab;
            chart.data = dataFor(config[view]);
            chart.update();
            panel.querySelectorAll('[data-frequency-tab]').forEach((b) => {
                const on = b === button;
                b.setAttribute('aria-pressed', String(on));
                b.classList.toggle('bg-inchiostro', on);
                b.classList.toggle('text-white', on);
            });
            panel.querySelector('p').textContent = view === 'months' ? 'Allenamenti per mese' : 'Allenamenti per settimana';
        });
    });
}

function lineChart(canvas, points, { label, extra = [] }) {
    const ink = css('--color-inchiostro');
    const red = css('--color-disco-rosso');
    const last = points.length - 1;

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: points.map((p) => p.label),
            datasets: [
                {
                    label,
                    data: points.map((p) => p.avg ?? p.value),
                    borderColor: ink,
                    borderWidth: 2,
                    tension: 0.25,
                    fill: { target: 'origin', above: 'rgba(19,21,20,0.05)' },
                    pointRadius: points.map((_, i) => (i === last ? 5 : 0)),
                    pointBackgroundColor: red,
                    pointBorderColor: red,
                    pointHoverRadius: 5,
                },
                ...extra,
            ],
        },
        options: {
            interaction: { mode: 'index', intersect: false },
            scales: scales({ grace: '12%' }),
            plugins: { tooltip: { callbacks: { label: (c) => `${c.dataset.label}: ${c.parsed.y} kg` } } },
        },
    });
}

export function initCharts() {
    const canvases = document.querySelectorAll('canvas[data-chart]');
    if (canvases.length === 0) return;

    applyDefaults();

    canvases.forEach((canvas) => {
        const config = JSON.parse(canvas.dataset.chart);

        if (config.type === 'bars') barsChart(canvas, config);
        if (config.type === 'body') lineChart(canvas, config.points, { label: 'Peso' });
        if (config.type === 'lift') {
            lineChart(canvas, config.points, {
                label: 'Medio',
                extra: [{
                    label: 'Massimo',
                    data: config.points.map((p) => p.max),
                    showLine: false,
                    pointRadius: 3,
                    pointBackgroundColor: css('--color-disco-rosso'),
                    pointBorderColor: css('--color-disco-rosso'),
                }],
            });
        }
    });
}
