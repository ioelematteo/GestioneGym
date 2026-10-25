/* ==========================================================================
   PAGINA ALLENAMENTO
   1. cronometro   2. ✓ serie fatta (+ copia l'ultima volta)   3. + Serie
   4. timer di recupero   5. bozza nel telefono (non perdi niente)
   ========================================================================== */
import { storage, draftKey } from './storage';

const DRAFT_MAX_AGE = 12 * 60 * 60 * 1000; // dopo 12 ore la bozza è vecchia

export function initSessionLogger() {
    const form = document.querySelector('[data-session]');
    if (!form) return;

    const draft = createDraft(form);
    const rest = createRestTimer();

    draft.restore();
    startClock(draft);

    form.addEventListener('click', (event) => {
        const done = event.target.closest('[data-done]');
        const copy = event.target.closest('[data-copy]');
        const add = event.target.closest('[data-add-set]');

        if (done) toggleDone(done.closest('[data-set]'), rest);
        if (copy) copyLast(copy.closest('[data-set]'), true);
        if (add) addSet(add.closest('[data-exercise]'));
        if (done || copy || add) draft.save();
    });

    form.addEventListener('input', () => draft.save());

    form.addEventListener('submit', () => {
        setDuration(form, draft.started);
        form.querySelector('button:not([type="button"])').disabled = true; // niente doppio invio
    });

    document.querySelector('[data-draft-discard]')?.addEventListener('click', () => {
        draft.clear();
        window.location.reload();
    });
}

/* ---------- 1. cronometro ---------- */

export function formatTime(seconds) {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = String(seconds % 60).padStart(2, '0');
    return h > 0 ? `${h}:${String(m).padStart(2, '0')}:${s}` : `${m}:${s}`;
}

function startClock(draft) {
    const clock = document.querySelector('[data-clock]');
    const tick = () => { clock.textContent = formatTime(Math.floor((Date.now() - draft.started) / 1000)); };
    tick();
    setInterval(tick, 1000);
}

/** La durata la metto solo se l'allenamento è di oggi. */
function setDuration(form, started) {
    const today = new Date().toLocaleDateString('sv-SE'); // YYYY-MM-DD
    const date = form.querySelector('[name="date"]').value;
    form.querySelector('[data-duration]').value = date === today ? Math.max(1, Math.round((Date.now() - started) / 60000)) : '';
}

/* ---------- 2. serie fatta / copia ---------- */

function toggleDone(row, rest) {
    const button = row.querySelector('[data-done]');

    if (button.getAttribute('aria-pressed') === 'true') {
        markDone(row, false);
        return;
    }

    copyLast(row, false); // campi vuoti? uso l'ultima volta

    if (row.querySelector('[data-reps]').value === '') {
        row.querySelector('[data-reps]').focus(); // prima volta: scrivi le rep
        return;
    }

    markDone(row, true);
    rest.start(Number(row.closest('[data-exercise]').dataset.rest));
}

function markDone(row, done) {
    row.querySelector('[data-done]').setAttribute('aria-pressed', String(done));
    row.querySelectorAll('input').forEach((input) => input.classList.toggle('!border-inchiostro', done));
}

/** overwrite = false -> riempie solo i campi vuoti */
function copyLast(row, overwrite) {
    const last = row.querySelector('[data-copy]');
    if (!last) return;
    const weight = row.querySelector('[data-weight]');
    const reps = row.querySelector('[data-reps]');
    if (overwrite || weight.value === '') weight.value = last.dataset.lastWeight;
    if (overwrite || reps.value === '') reps.value = last.dataset.lastReps;
}

/* ---------- 3. + serie ---------- */

function addSet(exercise) {
    const list = exercise.querySelector('[data-set-list]');
    const previous = list.lastElementChild;
    const html = document.getElementById('set-template').innerHTML
        .replaceAll('__EX__', exercise.dataset.exercise)
        .replaceAll('__N__', list.children.length + 1);

    list.insertAdjacentHTML('beforeend', html);
    const row = list.lastElementChild;

    // suggerimento = i numeri della serie prima
    if (previous) {
        ['[data-weight]', '[data-reps]'].forEach((selector) => {
            const before = previous.querySelector(selector);
            row.querySelector(selector).placeholder = before.value || before.placeholder;
        });
    }
    row.querySelector('[data-weight]').focus();
}

/* ---------- 4. recupero ---------- */

function createRestTimer() {
    const box = document.querySelector('[data-rest-box]');
    const time = box.querySelector('[data-rest-time]');
    const bar = box.querySelector('[data-rest-bar]');
    let end = 0;
    let total = 0;
    let timer = null;

    const update = () => {
        const left = Math.max(0, Math.ceil((end - Date.now()) / 1000));
        time.textContent = left > 0 ? formatTime(left) : 'Vai!';
        bar.style.width = `${total ? (left / total) * 100 : 0}%`;
        if (left === 0) {
            clearInterval(timer);
            navigator.vibrate?.([200, 100, 200]);
            setTimeout(stop, 5000);
        }
    };

    const run = () => { clearInterval(timer); timer = setInterval(update, 250); update(); };
    const stop = () => { clearInterval(timer); box.classList.add('hidden'); };

    box.querySelector('[data-rest-plus]').addEventListener('click', () => { end += 15000; total += 15; run(); });
    box.querySelector('[data-rest-skip]').addEventListener('click', stop);

    return {
        start(seconds) {
            total = seconds;
            end = Date.now() + seconds * 1000;
            box.classList.remove('hidden');
            run();
        },
    };
}

/* ---------- 5. bozza ---------- */

function createDraft(form) {
    const key = draftKey(form.dataset.session);

    const draft = {
        started: Date.now(),

        save() {
            const fields = {};
            form.querySelectorAll('[name^="sets"], [name="notes"], [name="body_weight"]').forEach((field) => {
                if (field.value !== '') fields[field.name] = field.value;
            });
            const rows = {};
            form.querySelectorAll('[data-exercise]').forEach((ex) => { rows[ex.dataset.exercise] = ex.querySelectorAll('[data-set]').length; });
            const done = [...form.querySelectorAll('[data-done][aria-pressed="true"]')].map((b) => b.closest('[data-set]').querySelector('[data-weight]').name);

            storage.write(key, { started: draft.started, saved: Date.now(), fields, rows, done });
        },

        restore() {
            const saved = storage.read(key);
            if (!saved || Date.now() - saved.saved > DRAFT_MAX_AGE) return;

            draft.started = saved.started;

            Object.entries(saved.rows ?? {}).forEach(([id, count]) => {
                const ex = form.querySelector(`[data-exercise="${id}"]`);
                while (ex && ex.querySelectorAll('[data-set]').length < count) addSet(ex);
            });
            Object.entries(saved.fields ?? {}).forEach(([name, value]) => {
                const field = form.querySelector(`[name="${CSS.escape(name)}"]`);
                if (field && field.value === '') field.value = value;
            });
            (saved.done ?? []).forEach((name) => {
                const field = form.querySelector(`[name="${CSS.escape(name)}"]`);
                if (field) markDone(field.closest('[data-set]'), true);
            });
            document.activeElement?.blur();

            if (Object.keys(saved.fields ?? {}).length > 0) {
                document.querySelector('[data-draft-notice]').classList.replace('hidden', 'flex');
            }
        },

        clear() { storage.remove(key); },
    };

    return draft;
}
