/* Solo i pezzi di Chart.js che usiamo (così il file resta piccolo).
   Questo modulo lo carica charts.js con import(), solo se serve. */
import {
    Chart, BarController, BarElement, LineController, LineElement, PointElement,
    CategoryScale, LinearScale, Tooltip, Filler,
} from 'chart.js';

Chart.register(BarController, BarElement, LineController, LineElement, PointElement, CategoryScale, LinearScale, Tooltip, Filler);

export { Chart };
