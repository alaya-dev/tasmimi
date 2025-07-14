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

        // Get categories with their active templates count
        $categories = Category::withCount(['activeTemplates'])
            ->orderBy('name')
            ->get();

        // Get featured templates (latest active templates)
        $featuredTemplates = Template::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($template) {
                $template->thumbnail_url = $template->thumbnail ? asset('storage/' . $template->thumbnail) : null;
                return $template;
            });

        return Inertia::render('Client/Home', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'categories' => $categories,
            'featuredTemplates' => $featuredTemplates,
        ]);
    }
}
