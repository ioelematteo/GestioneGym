/* ==========================================================================
   GRAFICI (Chart.js). Ogni <canvas data-chart='{"type": ...}'> diventa un grafico.
   Tipi: bars (frequenza), line (peso corporeo), lift (peso medio + massimo)

   Chart.js lo carico con import() SOLO nelle pagine che hanno un grafico:
   Vite lo mette in un file a parte (code splitting), le altre pagine non lo scaricano.
   ========================================================================== */

const css = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim();

/** "#5bea9c" + 0.3 -> "rgba(91, 234, 156, 0.3)" (il canvas non capisce color-mix) */
function alpha(hex, a) {
    const n = parseInt(hex.replace('#', ''), 16);
    return `rgba(${(n >> 16) & 255}, ${(n >> 8) & 255}, ${n & 255}, ${a})`;
}

/** Sfumatura verticale sotto la linea: dal colore pieno al trasparente */
const fade = (color) => ({ chart }) => {
    const area = chart.chartArea;
    if (!area) return 'transparent';
    const gradient = chart.ctx.createLinearGradient(0, area.top, 0, area.bottom);
    gradient.addColorStop(0, alpha(color, 0.22));
    gradient.addColorStop(1, alpha(color, 0));
    return gradient;
};

function applyDefaults(Chart) {
    Chart.defaults.font.family = '"Mona Sans Variable", system-ui, sans-serif';
    Chart.defaults.font.size = 11;
    Chart.defaults.color = css('--color-fg-3');
    Chart.defaults.borderColor = css('--color-line');
    Chart.defaults.maintainAspectRatio = false;
    Chart.defaults.animation.duration = 500;
    Object.assign(Chart.defaults.plugins.tooltip, {
        backgroundColor: css('--color-surface-3'),
        borderColor: css('--color-line-strong'),
        borderWidth: 1,
        titleColor: css('--color-fg-2'),
        bodyColor: css('--color-fg'),
        titleFont: { weight: 500 },
        bodyFont: { weight: 600, size: 13 },
        padding: 10,
        cornerRadius: 10,
        displayColors: false,
    });
}

const scales = (y = {}) => ({
    x: { grid: { display: false }, border: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 6 } },
    y: { grid: { color: css('--color-line') }, border: { display: false, dash: [3, 4] }, ticks: { maxTicksLimit: 4, padding: 6 }, ...y },
});

function barsChart(Chart, canvas, config) {
    const accent = css('--color-accent');
    const muted = css('--color-surface-3');
    const dataFor = (rows) => ({
        labels: rows.map((r) => r.label),
        datasets: [{
            data: rows.map((r) => r.value),
            // l'ultima barra (settimana/mese in corso) è verde, le altre grigie
            backgroundColor: rows.map((_, i) => (i === rows.length - 1 ? accent : muted)),
            hoverBackgroundColor: rows.map((_, i) => (i === rows.length - 1 ? accent : css('--color-line-strong'))),
            borderRadius: 6,
            borderSkipped: false,
            maxBarThickness: 26,
        }],
    });

    const chart = new Chart(canvas, {
        type: 'bar',
        data: dataFor(config.weeks),
        options: {
            scales: scales({ beginAtZero: true, suggestedMax: 4, ticks: { stepSize: 1, precision: 0, maxTicksLimit: 5, padding: 6 } }),
            plugins: { tooltip: { callbacks: { label: (c) => `${c.parsed.y} allenamenti` } } },
        },
    });

    // bottoni Sett. / Mesi (lo stile lo fa il CSS leggendo aria-pressed)
    const panel = canvas.closest('[data-frequency]');
    panel?.querySelectorAll('[data-frequency-tab]').forEach((button) => {
        button.addEventListener('click', () => {
            const view = button.dataset.frequencyTab;
            chart.data = dataFor(config[view]);
            chart.update();
            panel.querySelectorAll('[data-frequency-tab]').forEach((b) => b.setAttribute('aria-pressed', String(b === button)));
            const subtitle = panel.querySelector('header p');
            if (subtitle) subtitle.textContent = view === 'months' ? 'Allenamenti per mese' : 'Allenamenti per settimana';
        });
    });
}

function lineChart(Chart, canvas, points, { label, unit = 'kg', extra = [] }) {
    const accent = css('--color-accent');
    const last = points.length - 1;

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: points.map((p) => p.label),
            datasets: [
                {
                    label,
                    data: points.map((p) => p.avg ?? p.value),
                    borderColor: accent,
                    borderWidth: 2.25,
                    tension: 0.35,
                    fill: 'origin',
                    backgroundColor: fade(accent),
                    // solo l'ultimo punto è visibile (= "oggi")
                    pointRadius: points.map((_, i) => (i === last ? 4 : 0)),
                    pointBackgroundColor: accent,
                    pointBorderColor: css('--color-surface'),
                    pointBorderWidth: 2,
                    pointHoverRadius: 5,
                },
                ...extra,
            ],
        },
        options: {
            interaction: { mode: 'index', intersect: false },
            scales: scales({ grace: '15%' }),
            plugins: { tooltip: { callbacks: { label: (c) => `${c.dataset.label}: ${c.parsed.y} ${unit}` } } },
        },
    });
}

export async function initCharts() {
    const canvases = document.querySelectorAll('canvas[data-chart]');
    if (canvases.length === 0) return;

    const { Chart } = await import('./chart-setup');
    applyDefaults(Chart);

    canvases.forEach((canvas) => {
        const config = JSON.parse(canvas.dataset.chart);

        if (config.type === 'bars') barsChart(Chart, canvas, config);
        if (config.type === 'line' || config.type === 'body') lineChart(Chart, canvas, config.points, { label: 'Peso', unit: config.unit });
        if (config.type === 'lift') {
            lineChart(Chart, canvas, config.points, {
                label: 'Medio',
                extra: [{
                    label: 'Massimo',
                    data: config.points.map((p) => p.max),
                    showLine: false,
                    pointRadius: 3,
                    pointHoverRadius: 4,
                    pointBackgroundColor: css('--color-fg-2'),
                    pointBorderWidth: 0,
                }],
            });
        }
    });
}
