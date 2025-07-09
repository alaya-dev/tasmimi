<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Vérifier si l'utilisateur a des privilèges admin
        if ($user->hasAdminPrivileges()) {
            // Rediriger les admins et super admins vers leur interface d'administration
            return redirect()->route('admin.dashboard')->with('error', 'هذه المنطقة مخصصة للعملاء فقط');
        }

        // L'utilisateur est un client, autoriser l'accès
        return $next($request);
    }
}
