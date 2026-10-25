@php $editing = $user->exists; $me = $editing && $user->is(auth()->user()); @endphp
<x-layout :title="$editing ? 'Modifica utente' : 'Nuovo utente'" :heading="$editing ? $user->name : 'Nuovo utente'" :subtitle="$editing ? 'Lascia la password vuota per non cambiarla.' : 'Comunica tu la password: potrà cambiarla dal Profilo.'">
    <x-panel class="max-w-2xl">
        <form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="grid sm:grid-cols-2 gap-4">
            @csrf
            @if ($editing) @method('PUT') @endif
            <x-field label="Nome" name="name"><input id="name" name="name" class="input" value="{{ old('name', $user->name) }}" required></x-field>
            <x-field label="Email" name="email"><input id="email" type="email" name="email" class="input" value="{{ old('email', $user->email) }}" required></x-field>
            <x-field label="Password" name="password"><input id="password" type="password" name="password" class="input" autocomplete="new-password" @unless ($editing) required @endunless></x-field>
            <x-field label="Ruolo" name="role">
                @if ($me)
                    <input type="hidden" name="role" value="admin"><p class="text-sm text-grafite py-2">Admin (non puoi cambiare il tuo ruolo)</p>
                @else
                    <select id="role" name="role" class="input">
                        <option value="user" @selected(old('role', $editing && $user->isAdmin() ? 'admin' : 'user') === 'user')>User — si allena</option>
                        <option value="admin" @selected(old('role', $editing && $user->isAdmin() ? 'admin' : 'user') === 'admin')>Admin — gestisce anche gli utenti</option>
                    </select>
                @endif
            </x-field>
            <div class="sm:col-span-2 flex gap-2">
                <button class="btn btn-pieno">{{ $editing ? 'Salva' : 'Crea utente' }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-muto">Annulla</a>
            </div>
        </form>
    </x-panel>
</x-layout>
