<x-layout title="Utenti" heading="Utenti" subtitle="Chi può entrare e con che ruolo.">
    <x-slot:actions><a href="{{ route('admin.users.create') }}" class="btn btn-pieno"><x-icon name="plus" size="16" /> Nuovo utente</a></x-slot:actions>

    <x-panel>
        <div class="overflow-x-auto">
            <table class="tabella min-w-[640px]">
                <thead><tr><th>Utente</th><th>Ruolo</th><th>Stato</th><th class="text-right">Allenamenti</th><th></th></tr></thead>
                <tbody>
                    @foreach ($users as $user)
                        @php $me = $user->is(auth()->user()); @endphp
                        <tr>
                            <td><span class="font-medium">{{ $user->name }}</span>@if ($me)<span class="text-polvere"> (tu)</span>@endif<br><span class="text-[13px] text-grafite">{{ $user->email }}</span></td>
                            <td><span @class(['etichetta px-1.5 py-0.5 rounded-sm', '!text-white bg-inchiostro' => $user->isAdmin(), 'bg-cemento' => ! $user->isAdmin()])>{{ $user->isAdmin() ? 'admin' : 'user' }}</span></td>
                            <td>{{ $user->is_active ? 'Attivo' : 'Bloccato' }}</td>
                            <td class="font-mono text-right">{{ $user->training_sessions_count }}</td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-muto btn-piccolo !px-2" aria-label="Modifica"><x-icon name="edit" size="15" /></a>
                                @unless ($me)
                                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-muto btn-piccolo !px-2" aria-label="{{ $user->is_active ? 'Blocca' : 'Sblocca' }}" title="{{ $user->is_active ? 'Blocca' : 'Sblocca' }}"><x-icon name="lock" size="15" /></button>
                                    </form>
                                    <x-delete-button :action="route('admin.users.destroy', $user)" :confirm="'Eliminare '.$user->name.' e tutti i suoi dati?'" icon />
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>
</x-layout>
