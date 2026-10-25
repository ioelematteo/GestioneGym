<x-layout title="Profilo" heading="Profilo">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 items-start">
        <x-panel title="Account">
            <div class="rows -mx-4 sm:-mx-5 -mb-2 text-[14px]">
                @foreach (['Nome' => $user->name, 'Email' => $user->email, 'Ruolo' => $user->isAdmin() ? 'Admin' : 'Atleta', 'Dal' => $user->created_at->translatedFormat('j F Y')] as $label => $value)
                    <div class="flex justify-between gap-3 px-4 sm:px-5 py-3"><span class="text-fg-3">{{ $label }}</span><span class="text-right break-all">{{ $value }}</span></div>
                @endforeach
            </div>
        </x-panel>

        <x-panel title="Cambia password">
            <form method="POST" action="{{ route('profile.password') }}" class="flex flex-col gap-3">
                @csrf @method('PUT')
                <x-field label="Password attuale" name="current_password">
                    <input id="current_password" type="password" name="current_password" class="input" autocomplete="current-password" required>
                </x-field>
                <x-field label="Nuova password" name="password" hint="Almeno 8 caratteri, non finita in furti di dati.">
                    <input id="password" type="password" name="password" class="input" autocomplete="new-password" required>
                </x-field>
                <x-field label="Ripeti la nuova password" name="password_confirmation">
                    <input id="password_confirmation" type="password" name="password_confirmation" class="input" autocomplete="new-password" required>
                </x-field>
                <button class="btn btn-primary mt-1">Aggiorna password</button>
            </form>
        </x-panel>
    </div>
</x-layout>
