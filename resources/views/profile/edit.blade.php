<x-layout title="Profilo" heading="Profilo">
    <div class="grid lg:grid-cols-2 gap-3 items-start">
        <x-panel title="Account">
            <dl class="text-sm">
                @foreach (['Nome' => $user->name, 'Email' => $user->email, 'Ruolo' => $user->isAdmin() ? 'Admin' : 'Utente', 'Iscritto dal' => $user->created_at->translatedFormat('j F Y')] as $label => $value)
                    <div class="flex justify-between gap-3 py-2.5 border-t border-riga first:border-t-0"><dt class="text-grafite">{{ $label }}</dt><dd class="font-medium text-right break-all">{{ $value }}</dd></div>
                @endforeach
            </dl>
        </x-panel>

        <x-panel title="Cambia password">
            <form method="POST" action="{{ route('profile.password') }}" class="flex flex-col gap-3">
                @csrf @method('PUT')
                <x-field label="Password attuale" name="current_password">
                    <input id="current_password" type="password" name="current_password" class="input" autocomplete="current-password" required>
                </x-field>
                <x-field label="Nuova password" name="password" hint="Almeno 8 caratteri, non una password già finita in un furto di dati.">
                    <input id="password" type="password" name="password" class="input" autocomplete="new-password" required>
                </x-field>
                <x-field label="Ripeti la nuova password" name="password_confirmation">
                    <input id="password_confirmation" type="password" name="password_confirmation" class="input" autocomplete="new-password" required>
                </x-field>
                <button class="btn btn-pieno self-start">Aggiorna password</button>
            </form>
        </x-panel>
    </div>
</x-layout>
