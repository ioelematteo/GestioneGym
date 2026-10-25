/* ==========================================================================
   APP.JS — accende i moduli. Ogni modulo fa UNA cosa e parte solo se
   nella pagina c'è il suo elemento (es. data-session, data-chart).
   ========================================================================== */
// Font self-hosted da npm (niente CDN esterni)
import '@fontsource/big-shoulders-display/700';
import '@fontsource/big-shoulders-display/800';
import '@fontsource/ibm-plex-sans/400';
import '@fontsource/ibm-plex-sans/500';
import '@fontsource/ibm-plex-sans/600';
import '@fontsource/ibm-plex-mono/400';
import '@fontsource/ibm-plex-mono/500';

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
