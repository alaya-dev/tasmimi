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

        return Inertia::render('Admin/ClientTemplates/Show', [
            'clientTemplate' => [
                'id' => $clientTemplate->id,
                'name' => $clientTemplate->name,
                'design_data' => $clientTemplate->design_data, // Utiliser les données brutes
                'raw_design_data' => $clientTemplate->getAttributes()['design_data'], // Données brutes de la DB
                'editable_elements' => $clientTemplate->editable_elements,
                'canvas_size' => $clientTemplate->canvas_dimensions, // Utiliser l'accessor
                'version' => $clientTemplate->version,
                'notes' => $clientTemplate->notes,
                'created_at' => $clientTemplate->created_at->format('Y-m-d H:i:s'),
                'last_edited_at' => $clientTemplate->last_edited_at?->format('Y-m-d H:i:s'),
                'user' => [
                    'id' => $clientTemplate->user->id,
                    'email' => $clientTemplate->user->email,
                ],
                'template' => [
                    'id' => $clientTemplate->template->id,
                    'name' => $clientTemplate->template->name,
                    'category' => $clientTemplate->template->category->name ?? null,
                ],
            ],
        ]);
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