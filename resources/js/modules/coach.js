/* COACH: suggerimenti cliccabili e bottone "sto pensando" mentre l'IA risponde. */
export function initCoach() {
    const form = document.querySelector('[data-coach-form]');
    if (!form) return;

    const question = form.querySelector('textarea');

    form.querySelectorAll('[data-suggestion]').forEach((button) => {
        button.addEventListener('click', () => {
            question.value = button.textContent.trim();
            question.focus();
        });
    });

    form.addEventListener('submit', () => {
        const submit = form.querySelector('[data-coach-submit]');
        submit.disabled = true;
        submit.textContent = 'Il coach sta leggendo i tuoi dati…';
    });
}
