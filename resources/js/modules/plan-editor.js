/* ==========================================================================
   FORM SCHEDA: aggiungi / togli / sposta esercizi, lettera del disco in anteprima.
   ========================================================================== */
export function initPlanEditor() {
    const form = document.querySelector('[data-plan-editor]');
    if (!form) return;

    const rows = form.querySelector('[data-rows]');
    let next = rows.children.length; // indice per le righe nuove: rows[N][...]

    form.addEventListener('click', (event) => {
        const button = event.target.closest('button[type="button"]');
        if (!button) return;
        const row = button.closest('[data-row]');

        if (button.matches('[data-add-row]')) {
            rows.insertAdjacentHTML('beforeend', document.getElementById('row-template').innerHTML.replaceAll('__I__', next++));
            rows.lastElementChild.querySelector('[data-row-name]').focus();
        }
        if (button.matches('[data-remove]')) {
            if (rows.children.length > 1) row.remove();
            else row.querySelectorAll('input').forEach((input) => { input.value = ''; });
        }
        if (button.dataset.move === 'up' && row.previousElementSibling) row.previousElementSibling.before(row);
        if (button.dataset.move === 'down' && row.nextElementSibling) row.nextElementSibling.after(row);
    });

    // "Allenamento A" -> "A", "Legs" -> "L" (stessa regola di Workout::initial())
    form.querySelector('[data-plan-name]')?.addEventListener('input', (event) => {
        const words = event.target.value.trim().split(/\s+/);
        const last = words[words.length - 1] || '';
        const letter = (words.length > 1 && last.length <= 2 ? last : event.target.value.trim().charAt(0)) || 'A';
        form.querySelectorAll('[data-plate-preview]').forEach((plate) => {
            plate.lastChild.textContent = letter.toUpperCase();
        });
    });
}
