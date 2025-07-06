<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    /**
     * Afficher la liste des utilisateurs.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtrer par rôle si spécifié
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        // Recherche par nom ou email
        if ($request->has('search') && $request->search !== '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['role', 'search']),
            'roles' => User::getRoles(),
        ]);
    }

    /**
     * Afficher le formulaire de création d'utilisateur.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => User::getRoles(),
        ]);
    }

    /**
     * Créer un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:' . implode(',', User::getRoles()),
        ]);

        // Vérifier les permissions
        if ($request->role === User::ROLE_SUPER_ADMIN && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Seul un Super Admin peut créer un autre Super Admin.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Afficher le formulaire d'édition d'utilisateur.
     */
    public function edit(User $user)
    {
        // Vérifier les permissions
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Seul un Super Admin peut modifier un autre Super Admin.');
        }

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => User::getRoles(),
        ]);
    }

    /**
     * Mettre à jour un utilisateur.
     */
    public function update(Request $request, User $user)
    {
        // Vérifier les permissions
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Seul un Super Admin peut modifier un autre Super Admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:' . implode(',', User::getRoles()),
        ]);

        // Vérifier les permissions pour le changement de rôle
        if ($request->role === User::ROLE_SUPER_ADMIN && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Seul un Super Admin peut attribuer le rôle Super Admin.');
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprimer un utilisateur.
     */
    public function destroy(User $user)
    {
        // Vérifier les permissions
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Seul un Super Admin peut supprimer un autre Super Admin.');
        }

        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            abort(403, 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
