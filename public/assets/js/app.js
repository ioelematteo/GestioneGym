/* ==========================================================================
   APP.JS — piccole cose che servono in TUTTE le pagine
   Ogni blocco è una funzione con un nome che dice cosa fa.
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  chiudiMessaggiFlash();
  inviaFormAlCambio();
  chiediConfermaPrimaDiEliminare();
  chiudiMenuUtenteCliccandoFuori();
  cancellaBozzaSalvata();
});

/**
 * La X sui messaggi verdi li fa sparire.
 */
function chiudiMessaggiFlash() {
  document.querySelectorAll('[data-chiudi-flash]').forEach((bottone) => {
    bottone.addEventListener('click', () => bottone.closest('.flash').remove());
  });
}

/**
 * Le <select data-invia-al-cambio> inviano il loro form appena scegli un'opzione
 * (es. il filtro "Ultime 4 settimane" in Home).
 */
function inviaFormAlCambio() {
  document.querySelectorAll('[data-invia-al-cambio]').forEach((select) => {
    select.addEventListener('change', () => select.form.submit());
  });
}

/**
 * I form con data-conferma="Sei sicuro?" chiedono conferma prima di partire.
 */
function chiediConfermaPrimaDiEliminare() {
  document.querySelectorAll('form[data-conferma]').forEach((form) => {
    form.addEventListener('submit', (evento) => {
      if (!window.confirm(form.dataset.conferma)) {
        evento.preventDefault();
      }
    });
  });
}

/**
 * Il menu utente (in alto a destra) si chiude se clicchi altrove.
 */
function chiudiMenuUtenteCliccandoFuori() {
  const menu = document.querySelector('.menu-utente');
  if (!menu) return;

  document.addEventListener('click', (evento) => {
    if (!menu.contains(evento.target)) {
      menu.removeAttribute('open');
    }
  });
}

/**
 * Dopo aver salvato un allenamento, la pagina contiene <span data-cancella-bozza="ID">:
 * cancello la bozza che era rimasta salvata nel browser.
 */
function cancellaBozzaSalvata() {
  const segnale = document.querySelector('[data-cancella-bozza]');
  if (!segnale) return;

  try {
    localStorage.removeItem('gymlog:bozza:' + segnale.dataset.cancellaBozza);
  } catch (errore) {
    // localStorage non disponibile (es. navigazione privata): niente da fare
  }
}
