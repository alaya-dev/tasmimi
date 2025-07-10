<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // Redirect only admin and super admin users away from landing page
    if (auth()->check()) {
        $user = auth()->user();
        
        // Redirect to admin dashboard if user has admin privileges (admin or super_admin)
        if ($user->hasAdminPrivileges()) {
            return redirect()->route('admin.dashboard');
        }
    }
    
    // Show landing page to visitors and clients
    return Inertia::render('Client/Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('client.home');

Route::get('/dashboard', function () {
    $user = auth()->user();

    // Rediriger vers l'interface d'administration si l'utilisateur a des privilèges admin
    if ($user->hasAdminPrivileges()) {
        return redirect()->route('admin.dashboard');
    }

    // Rediriger les clients vers la landing page
    return redirect()->route('client.home');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes pour le changement de langue
Route::post('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
Route::get('/language/current', [LanguageController::class, 'current'])->name('language.current');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Client Routes
Route::prefix('client')->name('client.')->group(function () {
    // Public client routes
    Route::get('/templates', function () {
        return Inertia::render('Client/Templates');
    })->name('templates');

    Route::get('/pricing', [App\Http\Controllers\Client\PricingController::class, 'index'])->name('pricing');

    // Authenticated client routes (clients only)
    Route::middleware(['auth', 'client'])->group(function () {
        Route::get('/profile', [ClientProfileController::class, 'show'])->name('profile');
        Route::patch('/profile', [ClientProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ClientProfileController::class, 'updatePassword'])->name('password.update');

        // Contact routes (clients only)
        Route::get('/contact', [App\Http\Controllers\Client\ContactController::class, 'index'])->name('contact');
        Route::post('/contact', [App\Http\Controllers\Client\ContactController::class, 'store'])->name('contact.store');

        Route::get('/my-designs', function () {
            return Inertia::render('Client/MyDesigns', [
                'designs' => []
            ]);
        })->name('my-designs');

        Route::get('/editor/{template?}', function ($template = null) {
            return Inertia::render('Client/Editor', [
                'template' => $template ? ['id' => $template, 'name' => 'قالب تجريبي'] : null
            ]);
        })->name('editor');

        Route::get('/cart', function () {
            return Inertia::render('Client/Cart', [
                'cartItems' => []
            ]);
        })->name('cart');

        Route::get('/checkout', function () {
            return Inertia::render('Client/Checkout', [
                'orderItems' => [],
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0
            ]);
        })->name('checkout');

        Route::get('/orders', function () {
            return Inertia::render('Client/Orders', [
                'orders' => []
            ]);
        })->name('orders');

        Route::get('/invoices', function () {
            return Inertia::render('Client/Invoices', [
                'invoices' => []
            ]);
        })->name('invoices');
    });
});

// Routes d'administration
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Gestion des utilisateurs
    Route::resource('users', UserManagementController::class);
    
    // Gestion des contacts/réclamations
    Route::resource('contacts', App\Http\Controllers\Admin\ContactController::class)->except(['create', 'store', 'edit']);

    // Gestion des catégories
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->except(['show', 'create', 'edit']);

    // Gestion des templates
    Route::resource('templates', App\Http\Controllers\Admin\TemplateController::class)->except(['show', 'create', 'edit']);
    Route::patch('templates/{template}/toggle-status', [App\Http\Controllers\Admin\TemplateController::class, 'toggleStatus'])->name('templates.toggle-status');

    // Gestion des abonnements (super admin seulement)
    Route::middleware('super_admin')->group(function () {
        Route::resource('subscriptions', App\Http\Controllers\SubscriptionController::class)->except(['show', 'create', 'edit']);
    });
});

require __DIR__.'/auth.php';

// Route de test pour les abonnements
Route::get('/test-subscriptions', [App\Http\Controllers\TestController::class, 'testSubscriptions'])->name('test.subscriptions');
