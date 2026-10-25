<x-layout title="Utenti" heading="Utenti" subtitle="Chi può entrare e con che ruolo.">
    <x-slot:actions><a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" size="16" /> Nuovo utente</a></x-slot:actions>

    <div class="card">
        <div class="rows">
            @foreach ($users as $user)
                @php $me = $user->is(auth()->user()); @endphp
                <div class="flex items-center gap-3 px-4 sm:px-5 py-3.5">
                    <span class="grid place-items-center size-10 rounded-full bg-surface-2 num text-[14px] {{ $user->is_active ? 'text-accent' : 'text-fg-3' }}">{{ mb_substr($user->name, 0, 1) }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center gap-2">
                            <span class="text-[15px] truncate" style="font-weight: 580">{{ $user->name }}</span>
                            @if ($me)<span class="text-[12px] text-fg-3">tu</span>@endif
                            @if ($user->isAdmin())<span class="chip !h-5 !text-[11px]">admin</span>@endif
                            @unless ($user->is_active)<span class="chip !h-5 !text-[11px] !text-danger">bloccato</span>@endunless
                        </span>
                        <span class="block text-[13px] text-fg-3 truncate">{{ $user->email }} · {{ $user->training_sessions_count }} allenamenti</span>
                    </span>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-sm btn-icon" aria-label="Modifica"><x-icon name="edit" size="16" /></a>
                    @unless ($me)
                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline-flex">
                            @csrf @method('PATCH')
                            <button class="btn btn-ghost btn-sm btn-icon" aria-label="{{ $user->is_active ? 'Blocca' : 'Sblocca' }}" title="{{ $user->is_active ? 'Blocca' : 'Sblocca' }}"><x-icon name="lock" size="16" /></button>
                        </form>
                        <x-delete-button :action="route('admin.users.destroy', $user)" :confirm="'Eliminare '.$user->name.' e tutti i suoi dati?'" icon />
                    @endunless
                </div>
            @endforeach
        </div>
    </div>
</x-layout>
