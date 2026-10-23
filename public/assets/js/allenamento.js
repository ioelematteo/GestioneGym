/* ==========================================================================
   ALLENAMENTO.JS — la pagina che usi in palestra

   1. Cronometro          -> quanto dura l'allenamento
   2. Serie fatta (✓)     -> se i campi sono vuoti copia l'ultima volta, poi parte il recupero
   3. "ultima 80 × 6"     -> toccala per copiare peso e rep
   4. Aggiungi serie      -> una riga in più per quell'esercizio
   5. Timer di recupero   -> conto alla rovescia + vibrazione
   6. Bozza               -> salva tutto nel telefono: se chiudi la pagina non perdi niente
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('[data-allenamento]');
  if (!form) return;

  const bozza = creaBozza(form);
  const recupero = creaTimerRecupero();

  bozza.ripristina();
  avviaCronometro(bozza);

  // Un solo "ascoltatore" per tutti i click del form (funziona anche per le righe aggiunte dopo)
  form.addEventListener('click', (evento) => {
    const fatta = evento.target.closest('[data-fatta]');
    const copia = evento.target.closest('[data-copia]');
    const aggiungi = evento.target.closest('[data-aggiungi-serie]');

    if (fatta) segnaSerieFatta(fatta, recupero);
    if (copia) copiaUltimaVolta(copia.closest('[data-serie]'));
    if (aggiungi) aggiungiSerie(aggiungi.closest('[data-esercizio]'));

    if (fatta || copia || aggiungi) bozza.salva();
  });

  // Ogni volta che scrivi qualcosa, salvo la bozza
  form.addEventListener('input', () => bozza.salva());

  form.addEventListener('submit', () => {
    impostaDurata(form, bozza.inizio);
    form.querySelector('button:not([type="button"])').disabled = true; // niente doppio invio
  });

  document.querySelector('[data-scarta-bozza]')?.addEventListener('click', () => {
    bozza.cancella();
    window.location.reload();
  });
});

/* --------------------------------------------------------------------------
   1. CRONOMETRO
   -------------------------------------------------------------------------- */

function avviaCronometro(bozza) {
  const display = document.querySelector('[data-cronometro]');

  const aggiorna = () => {
    const secondi = Math.floor((Date.now() - bozza.inizio) / 1000);
    display.textContent = formattaTempo(secondi);
  };

  aggiorna();
  setInterval(aggiorna, 1000);
}

/**
 * 75 -> "1:15" | 3725 -> "1:02:05"
 */
function formattaTempo(secondi) {
  const ore = Math.floor(secondi / 3600);
  const minuti = Math.floor((secondi % 3600) / 60);
  const sec = String(secondi % 60).padStart(2, '0');

  if (ore > 0) {
    return `${ore}:${String(minuti).padStart(2, '0')}:${sec}`;
  }

  return `${minuti}:${sec}`;
}

/**
 * Prima di salvare scrivo la durata in minuti nel campo nascosto.
 * Se stai registrando un allenamento di un altro giorno, la durata non ha senso: la lascio vuota.
 */
function impostaDurata(form, inizio) {
  const campoDurata = form.querySelector('[data-durata]');
  const data = form.querySelector('[name="data"]').value;
  const oggi = new Date().toLocaleDateString('sv-SE'); // formato YYYY-MM-DD

  campoDurata.value = data === oggi ? Math.max(1, Math.round((Date.now() - inizio) / 60000)) : '';
}

/* --------------------------------------------------------------------------
   2 e 3. SERIE FATTA / COPIA ULTIMA VOLTA
   -------------------------------------------------------------------------- */

function segnaSerieFatta(bottone, recupero) {
  const riga = bottone.closest('[data-serie]');
  const giaFatta = bottone.getAttribute('aria-pressed') === 'true';

  if (giaFatta) {
    impostaFatta(riga, false);
    return;
  }

  // campi vuoti? uso i numeri dell'ultima volta
  if (riga.querySelector('.serie__rep').value === '') {
    copiaUltimaVolta(riga, false);
  }

  // ancora senza rep (prima volta che fai l'esercizio)? ti porto sul campo
  if (riga.querySelector('.serie__rep').value === '') {
    riga.querySelector('.serie__rep').focus();
    return;
  }

  impostaFatta(riga, true);
  recupero.avvia(Number(riga.closest('[data-esercizio]').dataset.recupero));
}

function impostaFatta(riga, fatta) {
  riga.classList.toggle('is-fatta', fatta);
  riga.querySelector('[data-fatta]').setAttribute('aria-pressed', String(fatta));
}

/**
 * Copia peso e rep dell'ultima volta nei campi della riga.
 * sovrascrivi = false -> riempie solo i campi vuoti
 */
function copiaUltimaVolta(riga, sovrascrivi = true) {
  const ultima = riga.querySelector('[data-copia]');
  if (!ultima) return;

  const kg = riga.querySelector('.serie__kg');
  const rep = riga.querySelector('.serie__rep');

  if (sovrascrivi || kg.value === '') kg.value = ultima.dataset.peso;
  if (sovrascrivi || rep.value === '') rep.value = ultima.dataset.rep;
}

/* --------------------------------------------------------------------------
   4. AGGIUNGI SERIE
   -------------------------------------------------------------------------- */

function aggiungiSerie(esercizio) {
  const lista = esercizio.querySelector('[data-serie-lista]');
  const precedente = lista.lastElementChild;
  const numero = lista.children.length + 1;

  // copio il <template> e sostituisco i segnaposto con i valori veri
  const html = document.getElementById('modello-serie').innerHTML
    .replaceAll('__ES__', esercizio.dataset.esercizio)
    .replaceAll('__N__', numero);

  lista.insertAdjacentHTML('beforeend', html);

  const nuova = lista.lastElementChild;

  // come suggerimento uso i numeri della serie prima
  if (precedente) {
    nuova.querySelector('.serie__kg').placeholder = valoreOSuggerimento(precedente.querySelector('.serie__kg'));
    nuova.querySelector('.serie__rep').placeholder = valoreOSuggerimento(precedente.querySelector('.serie__rep'));
  }

  nuova.querySelector('.serie__kg').focus();
  return nuova;
}

function valoreOSuggerimento(input) {
  return input.value || input.placeholder;
}

/* --------------------------------------------------------------------------
   5. TIMER DI RECUPERO
   -------------------------------------------------------------------------- */

function creaTimerRecupero() {
  const box = document.querySelector('[data-recupero-box]');
  const tempo = box.querySelector('[data-recupero-tempo]');
  const progresso = box.querySelector('[data-recupero-progresso]');

  let fine = 0; // quando finisce (timestamp in millisecondi)
  let totale = 0; // durata totale in secondi
  let intervallo = null;

  function avvia(secondi) {
    totale = secondi;
    fine = Date.now() + secondi * 1000;

    box.hidden = false;
    box.classList.remove('is-finito');
    clearInterval(intervallo);
    intervallo = setInterval(aggiorna, 250);
    aggiorna();
  }

  function aggiorna() {
    const rimasti = Math.max(0, Math.ceil((fine - Date.now()) / 1000));

    tempo.textContent = formattaTempo(rimasti);
    progresso.style.width = (totale ? (rimasti / totale) * 100 : 0) + '%';

    if (rimasti === 0) finito();
  }

  function finito() {
    clearInterval(intervallo);
    box.classList.add('is-finito');
    tempo.textContent = 'Vai!';

    if (navigator.vibrate) navigator.vibrate([200, 100, 200]);

    setTimeout(ferma, 5000);
  }

  function ferma() {
    clearInterval(intervallo);
    box.hidden = true;
  }

  box.querySelector('[data-recupero-piu]').addEventListener('click', () => {
    fine += 15000;
    totale += 15;
    box.classList.remove('is-finito');
    clearInterval(intervallo);
    intervallo = setInterval(aggiorna, 250);
    aggiorna();
  });

  box.querySelector('[data-recupero-stop]').addEventListener('click', ferma);

  return { avvia };
}

/* --------------------------------------------------------------------------
   6. BOZZA (salvata nel localStorage del browser)
   -------------------------------------------------------------------------- */

const DURATA_BOZZA = 12 * 60 * 60 * 1000; // dopo 12 ore una bozza è "vecchia"

function creaBozza(form) {
  const chiave = 'gymlog:bozza:' + form.dataset.allenamento;

  const bozza = {
    inizio: Date.now(),

    /**
     * Salva tutti i campi, le serie segnate come fatte e quante righe ha ogni esercizio.
     */
    salva() {
      const campi = {};
      form.querySelectorAll('input[name^="serie"], select[name^="serie"], [name="note"], [name="peso_corporeo"]').forEach((campo) => {
        if (campo.value !== '') campi[campo.name] = campo.value;
      });

      const righe = {};
      form.querySelectorAll('[data-esercizio]').forEach((esercizio) => {
        righe[esercizio.dataset.esercizio] = esercizio.querySelectorAll('[data-serie]').length;
      });

      const fatte = [...form.querySelectorAll('.serie.is-fatta .serie__kg')].map((input) => input.name);

      scriviStorage(chiave, { inizio: bozza.inizio, aggiornata: Date.now(), campi, righe, fatte });
    },

    /**
     * Se c'è una bozza recente, rimette tutto com'era.
     */
    ripristina() {
      const salvata = leggiStorage(chiave);

      if (!salvata || Date.now() - salvata.aggiornata > DURATA_BOZZA) return;

      bozza.inizio = salvata.inizio;

      // 1. ricreo le righe aggiunte a mano
      Object.entries(salvata.righe || {}).forEach(([id, quante]) => {
        const esercizio = form.querySelector(`[data-esercizio="${id}"]`);
        while (esercizio && esercizio.querySelectorAll('[data-serie]').length < quante) {
          aggiungiSerie(esercizio);
        }
      });

      // 2. rimetto i valori
      Object.entries(salvata.campi || {}).forEach(([nome, valore]) => {
        const campo = form.querySelector(`[name="${CSS.escape(nome)}"]`);
        if (campo && campo.value === '') campo.value = valore;
      });

      // 3. rimetto le spunte
      (salvata.fatte || []).forEach((nome) => {
        const campo = form.querySelector(`[name="${CSS.escape(nome)}"]`);
        if (campo) impostaFatta(campo.closest('[data-serie]'), true);
      });

      document.activeElement?.blur();

      if (Object.keys(salvata.campi || {}).length > 0) {
        document.querySelector('[data-avviso-bozza]').hidden = false;
      }
    },

    cancella() {
      try {
        localStorage.removeItem(chiave);
      } catch (errore) {
        // niente localStorage: niente da cancellare
      }
    },
  };

  return bozza;
}

/**
 * localStorage può non esistere (navigazione privata, browser vecchi):
 * per questo è sempre dentro try/catch.
 */
function leggiStorage(chiave) {
  try {
    return JSON.parse(localStorage.getItem(chiave));
  } catch (errore) {
    return null;
  }
}

function scriviStorage(chiave, valore) {
  try {
    localStorage.setItem(chiave, JSON.stringify(valore));
  } catch (errore) {
    // spazio pieno o storage bloccato: l'app funziona lo stesso, solo senza bozza
  }
}
