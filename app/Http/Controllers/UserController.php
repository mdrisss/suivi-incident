<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::withCount(['incidentsAssignes' => fn ($q) => $q->actifs()])->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('users.form', ['user' => new User(['role' => Role::Technicien]), 'roles' => Role::cases()]);
    }

    public function store(Request $request): RedirectResponse
    {
        User::create($this->valider($request));

        return redirect()->route('users.index')->with('success', 'Utilisateur créé.');
    }

    public function edit(User $user): View
    {
        return view('users.form', ['user' => $user, 'roles' => Role::cases()]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $donnees = $this->valider($request, $user);

        if (empty($donnees['password'])) {
            unset($donnees['password']);
        }

        $user->update($donnees);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($user->incidentsAssignes()->exists() || $user->incidentsDeclares()->exists()) {
            return back()->with('error', 'Cet utilisateur est lié à des incidents : modifiez plutôt son rôle.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
    }

    /**
     * @return array<string, mixed>
     */
    private function valider(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'telephone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::enum(Role::class)],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ], [], ['name' => 'nom', 'password' => 'mot de passe', 'role' => 'rôle']);
    }
}
