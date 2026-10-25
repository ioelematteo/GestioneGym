<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** GESTIONE UTENTI (solo admin: middleware "role:admin" sulle rotte). */
class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::with('roles')->withCount('trainingSessions')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create($request->safe()->only('name', 'email', 'password') + ['email_verified_at' => now()]);
        $user->syncRoles([$request->input('role')]);

        return redirect()->route('admin.users.index')->with('success', "Utente {$user->name} creato.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->update(array_filter($request->safe()->only('name', 'email', 'password')));

        // non puoi togliere il ruolo admin a te stesso (resteresti chiuso fuori)
        if (! $user->is($request->user())) {
            $user->syncRoles([$request->input('role')]);
        }

        return redirect()->route('admin.users.index')->with('success', "Utente {$user->name} aggiornato.");
    }

    /** Blocca / sblocca: un utente bloccato non può fare login. */
    public function toggle(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 403, 'Non puoi bloccare te stesso.');
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? "{$user->name} sbloccato." : "{$user->name} bloccato.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 403, 'Non puoi eliminare te stesso.');
        $user->delete(); // a cascata spariscono schede, allenamenti, misure, foto (righe)

        return redirect()->route('admin.users.index')->with('success', "Utente {$user->name} eliminato.");
    }
}
