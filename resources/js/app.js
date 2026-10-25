/* ==========================================================================
   APP.JS — accende i moduli. Ogni modulo fa UNA cosa e parte solo se
   nella pagina c'è il suo elemento (es. data-session, data-chart).
   ========================================================================== */
// Font self-hosted da npm (niente CDN esterni). wdth = versione con larghezza variabile (75–125%)
import '@fontsource-variable/mona-sans/wdth.css';

import { initCommon } from './modules/common';
import { initCharts } from './modules/charts';
import { initSessionLogger } from './modules/session-logger';
import { initPlanEditor } from './modules/plan-editor';
import { initCoach } from './modules/coach';

document.addEventListener('DOMContentLoaded', () => {
    initCommon();
    initCharts();
    initSessionLogger();
    initPlanEditor();
    initCoach();
});
