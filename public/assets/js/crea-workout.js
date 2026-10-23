/* ==========================================================================
   CREA-WORKOUT.JS — form per creare/modificare un workout

   - Aggiungi esercizio  -> copia il <template> e lo mette in fondo
   - ↑ ↓                 -> sposta la riga (l'ordine delle righe = ordine degli esercizi)
   - ✕                   -> rimuove la riga (ne resta sempre almeno una)
   - Scegli un esercizio dai suggerimenti -> il gruppo muscolare si riempie da solo
   - Scrivi il nome      -> la lettera nei dischi colorati si aggiorna
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('[data-crea-workout]');
  if (!form) return;

  const lista = form.querySelector('[data-lista-esercizi]');

  form.addEventListener('click', (evento) => {
    const bottone = evento.target.closest('button[type="button"]');
    if (!bottone) return;

    const riga = bottone.closest('[data-riga-esercizio]');

    if (bottone.matches('[data-aggiungi-esercizio]')) aggiungiRiga(lista);
    if (bottone.matches('[data-rimuovi]')) rimuoviRiga(lista, riga);
    if (bottone.matches('[data-sposta="su"]')) spostaSu(riga);
    if (bottone.matches('[data-sposta="giu"]')) spostaGiu(riga);
  });

  form.addEventListener('change', (evento) => {
    if (evento.target.matches('[data-nome-esercizio]')) riempiGruppoMuscolare(evento.target);
  });

  form.querySelector('#nome').addEventListener('input', (evento) => aggiornaAnteprimaLettera(evento.target.value));
});

function aggiungiRiga(lista) {
  const modello = document.getElementById('modello-esercizio');

  lista.insertAdjacentHTML('beforeend', modello.innerHTML);
  lista.lastElementChild.querySelector('[data-nome-esercizio]').focus();
}

function rimuoviRiga(lista, riga) {
  if (lista.children.length === 1) {
    // l'ultima riga non la tolgo: la svuoto
    riga.querySelectorAll('input').forEach((input) => { input.value = input.defaultValue = ''; });
    return;
  }

  riga.remove();
}

function spostaSu(riga) {
  const prima = riga.previousElementSibling;
  if (prima) prima.before(riga);
}

function spostaGiu(riga) {
  const dopo = riga.nextElementSibling;
  if (dopo) dopo.after(riga);
}

/**
 * Se il nome scritto è uno degli esercizi suggeriti, imposto il suo gruppo muscolare.
 */
function riempiGruppoMuscolare(inputNome) {
  const opzione = [...document.querySelectorAll('#catalogo-esercizi option')]
    .find((o) => o.value.toLowerCase() === inputNome.value.trim().toLowerCase());

  if (!opzione || !opzione.dataset.gruppo) return;

  const select = inputNome.closest('[data-riga-esercizio]').querySelector('[data-gruppo-esercizio]');
  select.value = opzione.dataset.gruppo;
}

/**
 * Stessa logica di iniziale_workout() in PHP: "Push A" -> "A", "Legs" -> "L"
 */
function aggiornaAnteprimaLettera(nome) {
  const parole = nome.trim().split(/\s+/);
  const ultima = parole[parole.length - 1] || '';
  const lettera = parole.length > 1 && ultima.length <= 2 ? ultima : nome.trim().charAt(0);

  document.querySelectorAll('[data-anteprima-iniziale]').forEach((disco) => {
    disco.textContent = (lettera || 'A').toUpperCase();
  });
}
