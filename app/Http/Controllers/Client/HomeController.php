<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Template;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the home page with categories and templates
     */
    public function index(): Response
    {
        // Redirect only admin and super admin users away from landing page
        if (auth()->check()) {
            $user = auth()->user();

            // Redirect to admin dashboard if user has admin privileges (admin or super_admin)
            if ($user->hasAdminPrivileges()) {
                return redirect()->route('admin.dashboard');
            }
        }

        // Get all categories with their active templates
        $categories = Category::with(['activeTemplates' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->orderBy('name')->get();

        // Prepare a flat list of all active templates, grouped by category
        $allTemplates = [];
        foreach ($categories as $category) {
            foreach ($category->activeTemplates as $template) {
                // Convert to array and add thumbnail_url with correct storage path
                $templateArray = $template->toArray();
                $templateArray['thumbnail_url'] = $template->thumbnail ? asset('storage/' . $template->thumbnail) : null;
                $allTemplates[$category->id][] = $templateArray;
            }
        }

        return Inertia::render('Client/Home', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'categories' => $categories,
            'allTemplates' => $allTemplates,
        ]);
    }
}
