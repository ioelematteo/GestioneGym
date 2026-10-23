/* ==========================================================================
   HOME.JS — i due grafici della dashboard (con la libreria Chart.js)
   I dati arrivano dal PHP dentro <script id="dati-grafici"> in formato JSON.
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  if (typeof Chart === 'undefined') return; // Chart.js non caricato

  const dati = JSON.parse(document.getElementById('dati-grafici').textContent);

  impostaStileGrafici();
  creaGraficoFrequenza(dati.frequenza);
  creaGraficoPeso(dati.pesi);
});

/**
 * Legge un colore dal CSS (es. '--accento'), così i colori stanno in un posto solo.
 */
function coloreCss(nome) {
  return getComputedStyle(document.documentElement).getPropertyValue(nome).trim();
}

/**
 * Stile comune a tutti i grafici: font, colori, tooltip scuro.
 */
function impostaStileGrafici() {
  Chart.defaults.font.family = '"Geist Mono", ui-monospace, monospace';
  Chart.defaults.font.size = 11;
  Chart.defaults.color = coloreCss('--testo-3');
  Chart.defaults.borderColor = coloreCss('--bordo');
  Chart.defaults.animation.duration = 500;

  Object.assign(Chart.defaults.plugins.tooltip, {
    backgroundColor: coloreCss('--superficie-3'),
    borderColor: coloreCss('--bordo-forte'),
    borderWidth: 1,
    titleColor: coloreCss('--testo-2'),
    bodyColor: coloreCss('--testo'),
    padding: 10,
    cornerRadius: 8,
    displayColors: false,
  });
  Chart.defaults.plugins.legend.display = false; // una sola serie: basta il titolo
}

/**
 * Grafico a barre "Frequenza allenamenti", con i bottoni Settimane / Mesi.
 */
function creaGraficoFrequenza(frequenza) {
  const canvas = document.getElementById('grafico-frequenza');
  if (!canvas) return;

  const grafico = new Chart(canvas, {
    type: 'bar',
    data: datiBarre(frequenza.settimane),
    options: {
      maintainAspectRatio: false,
      scales: {
        x: { grid: { display: false }, border: { display: false }, ticks: { maxRotation: 0, autoSkip: true } },
        y: {
          beginAtZero: true,
          suggestedMax: 4,
          ticks: { stepSize: 1, precision: 0 },
          border: { display: false },
        },
      },
      plugins: {
        tooltip: {
          callbacks: {
            label: (contesto) => contesto.parsed.y + (contesto.parsed.y === 1 ? ' allenamento' : ' allenamenti'),
          },
        },
      },
    },
  });

  const sottotitolo = document.querySelector('[data-sottotitolo-frequenza]');

  document.querySelectorAll('[data-frequenza]').forEach((bottone) => {
    bottone.addEventListener('click', () => {
      const vista = bottone.dataset.frequenza; // 'settimane' oppure 'mesi'

      grafico.data = datiBarre(frequenza[vista]);
      grafico.update();

      sottotitolo.textContent = vista === 'mesi'
        ? 'Numero di allenamenti per mese'
        : 'Numero di allenamenti per settimana';

      document.querySelectorAll('[data-frequenza]').forEach((b) => {
        b.setAttribute('aria-pressed', String(b === bottone));
      });
    });
  });
}

/**
 * Prepara le barre: l'ultima (settimana o mese in corso) è piena, le altre più tenui.
 */
function datiBarre(periodi) {
  const accento = coloreCss('--accento');
  const ultimo = periodi.length - 1;

  return {
    labels: periodi.map((p) => p.etichetta),
    datasets: [{
      data: periodi.map((p) => p.valore),
      backgroundColor: periodi.map((_, i) => (i === ultimo ? accento : 'rgba(111, 227, 161, 0.5)')),
      hoverBackgroundColor: accento,
      borderRadius: { topLeft: 4, topRight: 4 },
      borderSkipped: 'bottom',
      maxBarThickness: 24,
    }],
  };
}

/**
 * Grafico a linea "Peso corporeo".
 */
function creaGraficoPeso(pesi) {
  const canvas = document.getElementById('grafico-peso');
  if (!canvas) return;

  const accento = coloreCss('--accento');
  const ultimo = pesi.length - 1;

  // sfumatura verde sotto la linea (10% di opacità, va a zero in basso)
  const sfumatura = canvas.getContext('2d').createLinearGradient(0, 0, 0, 170);
  sfumatura.addColorStop(0, 'rgba(111, 227, 161, 0.16)');
  sfumatura.addColorStop(1, 'rgba(111, 227, 161, 0)');

  new Chart(canvas, {
    type: 'line',
    data: {
      labels: pesi.map((p) => p.data),
      datasets: [{
        data: pesi.map((p) => p.peso),
        borderColor: accento,
        borderWidth: 2,
        backgroundColor: sfumatura,
        fill: true,
        tension: 0.35,
        // pallino solo sull'ultimo valore
        pointRadius: pesi.map((_, i) => (i === ultimo ? 4 : 0)),
        pointBackgroundColor: accento,
        pointBorderColor: coloreCss('--superficie'),
        pointBorderWidth: 2,
        pointHoverRadius: 5,
      }],
    },
    options: {
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      scales: {
        x: { grid: { display: false }, border: { display: false }, ticks: { maxTicksLimit: 4, maxRotation: 0 } },
        y: { grace: '15%', border: { display: false }, ticks: { maxTicksLimit: 5 } },
      },
      plugins: {
        tooltip: { callbacks: { label: (contesto) => contesto.parsed.y + ' kg' } },
      },
    },
  });
}
