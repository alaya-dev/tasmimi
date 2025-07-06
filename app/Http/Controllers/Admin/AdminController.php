<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Afficher le tableau de bord d'administration.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', User::ROLE_ADMIN)->count(),
            'total_clients' => User::where('role', User::ROLE_CLIENT)->count(),
            'recent_users' => User::latest()->take(5)->get(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats
        ]);
    }
}
