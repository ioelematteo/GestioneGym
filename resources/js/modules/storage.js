/* localStorage può non esserci (navigazione privata): sempre dentro try/catch. */
export const storage = {
    read(key) {
        try { return JSON.parse(localStorage.getItem(key)); } catch { return null; }
    },
    write(key, value) {
        try { localStorage.setItem(key, JSON.stringify(value)); } catch { /* niente bozza, l'app va lo stesso */ }
    },
    remove(key) {
        try { localStorage.removeItem(key); } catch { /* niente da fare */ }
    },
};

export const draftKey = (workoutId) => `gymlog:draft:${workoutId}`;
