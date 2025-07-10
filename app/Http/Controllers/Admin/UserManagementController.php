<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminAccountCreated;
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

        // Exclure les super_admin de la liste
        $query->where('role', '!=', User::ROLE_SUPER_ADMIN);

        // Filtrer par rôle si spécifié
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        // Recherche par email
        if ($request->has('search') && $request->search !== '') {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->paginate(10)->withQueryString();

        // Filtrer les rôles disponibles (exclure super_admin)
        $availableRoles = array_filter(User::getRoles(), function($role) {
            return $role !== User::ROLE_SUPER_ADMIN;
        });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['role', 'search']),
            'roles' => array_values($availableRoles),
        ]);
    }

    /**
     * Afficher le formulaire de création d'utilisateur.
     */
    public function create()
    {
        // Seul le Super Admin peut créer des utilisateurs
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Seul le Super Admin peut créer des utilisateurs.');
        }

        // Filtrer les rôles disponibles (exclure super_admin pour la création)
        $availableRoles = array_filter(User::getRoles(), function($role) {
            return $role !== User::ROLE_SUPER_ADMIN;
        });

        return Inertia::render('Admin/Users/Create', [
            'roles' => array_values($availableRoles),
        ]);
    }

    /**
     * Créer un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        // Seul le Super Admin peut créer des utilisateurs
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Seul le Super Admin peut créer des utilisateurs.');
        }

        $request->validate([
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:' . implode(',', User::getRoles()),
        ]);

        // Vérifier les permissions
        if ($request->role === User::ROLE_SUPER_ADMIN && !auth()->user()->isSuperAdmin()) {
            abort(403, __('common.only_super_admin_can_create_super_admin'));
        }

        // Stocker le mot de passe en clair pour l'email
        $plainPassword = $request->password;

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(),
        ]);

        // Envoyer l'email avec les informations de compte seulement pour les admins
        if ($request->role === User::ROLE_ADMIN) {
            $loginUrl = url('/login');
            $user->notify(new AdminAccountCreated($plainPassword, $loginUrl));
        }

        return redirect()->route('admin.users.index')
            ->with('success', __('common.user_created_successfully'));
    }

    /**
     * Afficher le formulaire d'édition d'utilisateur.
     */
    public function edit(User $user)
    {
        // Vérifier les permissions
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, __('common.only_super_admin_can_edit_super_admin'));
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
            abort(403, __('common.only_super_admin_can_edit_super_admin'));
        }

        $request->validate([
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:' . implode(',', User::getRoles()),
        ]);

        // Vérifier les permissions pour le changement de rôle
        if ($request->role === User::ROLE_SUPER_ADMIN && !auth()->user()->isSuperAdmin()) {
            abort(403, __('common.only_super_admin_can_assign_super_admin_role'));
        }

        $data = [
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', __('common.user_updated_successfully'));
    }

    /**
     * Supprimer un utilisateur.
     */
    public function destroy(User $user)
    {
        // Vérifier les permissions
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, __('common.only_super_admin_can_delete_super_admin'));
        }

        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            abort(403, __('common.cannot_delete_own_account'));
        }

        // Si l'utilisateur connecté est un Admin, il ne peut pas supprimer un autre Admin
        if (auth()->user()->isAdmin() && $user->isAdmin()) {
            abort(403, 'Un Admin ne peut pas supprimer un autre Admin.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', __('common.user_deleted_successfully'));
    }
}