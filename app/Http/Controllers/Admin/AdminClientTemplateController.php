<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminClientTemplateController extends Controller
{
    /**
     * Afficher la liste des templates clients pour les admins
     */
    public function index(Request $request)
    {
        // Vérifier que l'utilisateur est admin ou super_admin
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        $query = ClientTemplate::with(['user', 'template.category']);

        // Recherche par nom de template ou email utilisateur
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filtrer par utilisateur si spécifié
        if ($request->has('user_id') && $request->user_id !== '') {
            $query->where('user_id', $request->user_id);
        }

        $clientTemplates = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Obtenir la liste des clients pour le filtre
        $clients = User::where('role', User::ROLE_CLIENT)
                      ->orderBy('email')
                      ->get(['id', 'email']);

        return Inertia::render('Admin/ClientTemplates/Index', [
            'clientTemplates' => $clientTemplates,
            'filters' => $request->only(['search', 'user_id']),
            'clients' => $clients,
        ]);
    }

    /**
     * Afficher un template client en mode lecture seule
     */
    public function show(ClientTemplate $clientTemplate)
    {
        // Vérifier que l'utilisateur est admin ou super_admin
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        $clientTemplate->load(['user', 'template.category']);

        return Inertia::render('Admin/ClientTemplates/Design', [
            'clientTemplate' => $clientTemplate->load('user', 'template'),
        ]);
    }



    /**
     * Mettre à jour un template client (pour la sauvegarde depuis l'éditeur)
     */
    public function update(Request $request, ClientTemplate $clientTemplate)
    {
        // Vérifier que l'utilisateur est admin ou super_admin
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'design_data' => 'required|string',
            'canvas_size' => 'nullable|string',
            'last_edited_at' => 'nullable|string',
        ]);

        try {
            $clientTemplate->update([
                'design_data' => $validated['design_data'],
                'canvas_size' => $validated['canvas_size'] ?? $clientTemplate->canvas_size,
                'last_edited_at' => now(),
                'version' => $clientTemplate->version + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ التصميم بنجاح',
                'template' => $clientTemplate->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ التصميم'
            ], 500);
        }
    }

    /**
     * Supprimer un template client
     */
    public function destroy(ClientTemplate $clientTemplate)
    {
        // Vérifier que l'utilisateur est admin ou super_admin
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        try {
            // Supprimer la miniature si elle existe
            if ($clientTemplate->thumbnail) {
                \Storage::disk('public')->delete($clientTemplate->thumbnail);
            }

            // Supprimer l'image générée si elle existe
            if ($clientTemplate->generated_image_url) {
                \Storage::disk('public')->delete($clientTemplate->generated_image_url);
            }

            $clientTemplate->delete();

            return redirect()->route('admin.client-templates.index')
                ->with('success', 'تم حذف التصميم بنجاح');

        } catch (\Exception $e) {
            \Log::error('Admin client template delete error: ' . $e->getMessage(), [
                'client_template_id' => $clientTemplate->id,
                'admin_user_id' => auth()->id(),
            ]);

            return redirect()->route('admin.client-templates.index')
                ->with('error', 'حدث خطأ أثناء حذف التصميم');
        }
    }
}
