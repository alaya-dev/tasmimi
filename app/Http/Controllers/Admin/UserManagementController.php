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
        $currentUser = auth()->user();

        // Restrictions selon le rôle :
        // Admin : peut voir seulement les clients
        // Super_Admin : peut voir tous les utilisateurs
        if ($currentUser->isAdmin() && !$currentUser->isSuperAdmin()) {
            $query->where('role', User::ROLE_CLIENT);
        }

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

        // Définir les rôles disponibles selon les permissions
        $availableRoles = User::getRoles();
        if ($currentUser->isAdmin() && !$currentUser->isSuperAdmin()) {
            // Admin ne peut voir que les clients
            $availableRoles = [User::ROLE_CLIENT];
        }

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['role', 'search']),
            'roles' => $availableRoles,
            'userPermissions' => [
                'canAddUsers' => $currentUser->isSuperAdmin(),
                'canEditAdmins' => $currentUser->isSuperAdmin(),
                'canDeleteClients' => true, // Admin et Super_Admin peuvent supprimer des clients
                'canDeleteAdmins' => $currentUser->isSuperAdmin(),
            ],
        ]);
    }

    /**
     * Afficher le formulaire de création d'utilisateur.
     */
    public function create()
    {
        $currentUser = auth()->user();

        // Only Super_Admin can create users
        if (!$currentUser->isSuperAdmin()) {
            abort(403, 'Only a Super Admin can add users.');
        }

        return Inertia::render('Admin/Users/Create', [
            'roles' => User::getRoles(),
        ]);
    }

    /**
     * Créer un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();

        // Only Super_Admin can create users
        if (!$currentUser->isSuperAdmin()) {
            abort(403, 'Only a Super Admin can add users.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:' . implode(',', User::getRoles()),
        ]);

        // Check permissions
        if ($request->role === User::ROLE_SUPER_ADMIN && !$currentUser->isSuperAdmin()) {
            abort(403, 'Only a Super Admin can create another Super Admin.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * Afficher le formulaire d'édition d'utilisateur.
     */
    public function edit(User $user)
    {
        $currentUser = auth()->user();

        // Admin cannot edit admins or super_admins
        if ($currentUser->isAdmin() && !$currentUser->isSuperAdmin() &&
            ($user->isAdmin() || $user->isSuperAdmin())) {
            abort(403, 'You cannot edit this user.');
        }

        // Only Super Admin can edit other Super Admins (but Super Admin can edit everyone)
        if ($user->isSuperAdmin() && !$currentUser->isSuperAdmin()) {
            abort(403, 'Only a Super Admin can edit another Super Admin.');
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
        $currentUser = auth()->user();

        // Admin cannot edit admins or super_admins
        if ($currentUser->isAdmin() && !$currentUser->isSuperAdmin() &&
            ($user->isAdmin() || $user->isSuperAdmin())) {
            abort(403, 'You cannot edit this user.');
        }

        // Only Super Admin can edit other Super Admins (but Super Admin can edit everyone)
        if ($user->isSuperAdmin() && !$currentUser->isSuperAdmin()) {
            abort(403, 'Only a Super Admin can edit another Super Admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:' . implode(',', User::getRoles()),
        ]);

        // Check permissions for role change
        if ($request->role === User::ROLE_SUPER_ADMIN && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Only a Super Admin can assign the Super Admin role.');
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
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /**
     * Supprimer un utilisateur.
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        // Admin can only delete clients
        if ($currentUser->isAdmin() && !$currentUser->isSuperAdmin() &&
            ($user->isAdmin() || $user->isSuperAdmin())) {
            abort(403, 'You can only delete clients.');
        }

        // Super_Admin can delete admins and clients
        if ($user->isSuperAdmin() && !$currentUser->isSuperAdmin()) {
            abort(403, 'Only a Super Admin can delete another Super Admin.');
        }

        // Prevent deletion of own account
        if ($user->id === auth()->id()) {
            abort(403, 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }
}
