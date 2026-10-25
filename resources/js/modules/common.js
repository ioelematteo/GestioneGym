/* Piccole cose valide in tutte le pagine. */
import { storage, draftKey } from './storage';

export function initCommon() {
    // select che inviano il form appena cambi (filtro periodo)
    document.querySelectorAll('[data-autosubmit]').forEach((select) => {
        select.addEventListener('change', () => select.form.submit());
    });

    // form con data-confirm: chiedo conferma prima di inviare
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) event.preventDefault();
        });
    });

    // menu utente: si chiude cliccando fuori
    const menu = document.querySelector('[data-menu]');
    if (menu) {
        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target)) menu.removeAttribute('open');
        });
    }

    // foto: appena scegli i file il form parte da solo (niente secondo bottone)
    document.querySelectorAll('[data-photo-input]').forEach((input) => {
        input.addEventListener('change', () => {
            if (input.files.length === 0) return;
            const label = input.closest('label');
            label.classList.add('opacity-60', 'pointer-events-none');
            label.querySelector('[data-photo-label]').textContent = 'Carico…';
            input.form.submit();
        });
    });

    // dopo il salvataggio di un allenamento cancello la sua bozza
    const clear = document.querySelector('[data-clear-draft]');
    if (clear) storage.remove(draftKey(clear.dataset.clearDraft));
}
