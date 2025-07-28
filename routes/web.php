<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminClientTemplateController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Client\ClientTemplateController;
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

Route::get('/', [App\Http\Controllers\Client\HomeController::class, 'index'])->name('client.home');

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

        // Routes pour la gestion des templates clients
        Route::get('/my-designs', [ClientTemplateController::class, 'index'])->name('my-designs');
        Route::get('/templates/{template}/create', [ClientTemplateController::class, 'create'])->name('templates.create');
        Route::get('/designs/{clientTemplate}/edit', [ClientTemplateController::class, 'edit'])->name('designs.edit');
        Route::post('/designs', [ClientTemplateController::class, 'store'])->name('designs.store');
        Route::put('/designs/{clientTemplate}', [ClientTemplateController::class, 'update'])->name('designs.update');
        Route::patch('/designs/{clientTemplate}/name', [ClientTemplateController::class, 'updateName'])->name('designs.update-name');
        Route::delete('/designs/{clientTemplate}', [ClientTemplateController::class, 'destroy'])->name('designs.destroy');
        Route::post('/designs/{clientTemplate}/export', [ClientTemplateController::class, 'export'])->name('designs.export');
        Route::post('/designs/{clientTemplate}/duplicate', [ClientTemplateController::class, 'duplicate'])->name('designs.duplicate');

        // Route pour sauvegarder le design côté client
        Route::post('/templates/{template}/design', [ClientTemplateController::class, 'saveDesign'])->name('client.templates.design.save');

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

        // Routes de paiement et abonnements
        Route::get('/subscriptions', [App\Http\Controllers\SubscriptionPaymentController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/{subscription}', [App\Http\Controllers\SubscriptionPaymentController::class, 'show'])->name('subscriptions.show');
        Route::post('/subscriptions/{subscription}/payment', [App\Http\Controllers\MoyasarPaymentController::class, 'processPayment'])->name('subscriptions.payment');
        Route::get('/subscription/manage', [App\Http\Controllers\SubscriptionPaymentController::class, 'manage'])->name('subscription.manage');
        Route::post('/subscription/cancel', [App\Http\Controllers\SubscriptionPaymentController::class, 'cancel'])->name('subscription.cancel');
        Route::post('/subscription/change/{subscription}', [App\Http\Controllers\SubscriptionPaymentController::class, 'changePlan'])->name('subscription.change');

        // Routes de paiement Moyasar
        Route::get('/payment/{subscription}', [App\Http\Controllers\MoyasarPaymentController::class, 'show'])->name('payment.show');
        Route::post('/payment/{subscription}/process', [App\Http\Controllers\MoyasarPaymentController::class, 'processPayment'])->name('payment.process');
        Route::get('/payment/{subscription}/moyasar', [App\Http\Controllers\MoyasarPaymentController::class, 'payWithMoyasar'])->name('payment.moyasar');
        Route::get('/payment/{subscription}/simple', [App\Http\Controllers\MoyasarPaymentController::class, 'showSimple'])->name('payment.simple');
        Route::get('/payment/callback', [App\Http\Controllers\MoyasarPaymentController::class, 'callback'])->name('payment.callback');
        Route::post('/payment/status', [App\Http\Controllers\MoyasarPaymentController::class, 'checkStatus'])->name('payment.status');
        Route::get('/payment/verify/{payment}', [App\Http\Controllers\MoyasarPaymentController::class, 'verifyPayment'])->name('payment.verify');
        Route::get('/payment/check/{payment}', [App\Http\Controllers\MoyasarPaymentController::class, 'showVerification'])->name('payment.check');
        Route::get('/payment/return/{type}', [App\Http\Controllers\MoyasarPaymentController::class, 'showReturn'])->name('payment.return');
        Route::post('/payment/check-recent', [App\Http\Controllers\MoyasarPaymentController::class, 'checkRecentPayments'])->name('payment.check-recent');

        // Routes d'achat de templates
        Route::get('/templates/{template}/purchase', [App\Http\Controllers\Client\TemplatePurchaseController::class, 'show'])->name('templates.purchase');
        Route::post('/templates/{template}/purchase', [App\Http\Controllers\Client\TemplatePurchaseController::class, 'processPayment'])->name('templates.purchase.process');
        Route::get('/templates/{template}/purchase/moyasar', [App\Http\Controllers\Client\TemplatePurchaseController::class, 'payWithMoyasar'])->name('templates.purchase.moyasar');
        Route::get('/templates/{template}/purchase/simple', [App\Http\Controllers\Client\TemplatePurchaseController::class, 'showSimple'])->name('templates.purchase.simple');
        Route::get('/template-purchase/callback', [App\Http\Controllers\Client\TemplatePurchaseController::class, 'callback'])->name('template-purchase.callback');
        Route::get('/template-purchases', [App\Http\Controllers\Client\TemplatePurchaseController::class, 'index'])->name('template-purchases.index');
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

    // Gestion des templates clients (بطاقات الحرفاء)
    Route::resource('client-templates', AdminClientTemplateController::class)->only(['index', 'show', 'update', 'destroy']);

    // Gestion des abonnements clients
    Route::get('client-subscriptions', [App\Http\Controllers\Admin\ClientSubscriptionController::class, 'index'])->name('client-subscriptions.index');
    Route::delete('client-subscriptions/{subscription}', [App\Http\Controllers\Admin\ClientSubscriptionController::class, 'destroy'])->name('client-subscriptions.destroy');

    // Gestion des ventes de templates
    Route::get('template-sales', [App\Http\Controllers\Admin\TemplateSalesController::class, 'index'])->name('template-sales.index');
    Route::get('template-sales/{template}', [App\Http\Controllers\Admin\TemplateSalesController::class, 'show'])->name('template-sales.show');
    Route::get('template-sales-export', [App\Http\Controllers\Admin\TemplateSalesController::class, 'export'])->name('template-sales.export');



    // Éditeur de design pour templates
    Route::get('templates/{template}/design', [App\Http\Controllers\Admin\TemplateController::class, 'designEditor'])->name('templates.design');

    // Enhanced design operations
    Route::post('templates/{template}/design', [App\Http\Controllers\Admin\TemplateDesignController::class, 'saveDesign'])->name('templates.design.save');
    Route::post('templates/{template}/background', [App\Http\Controllers\Admin\TemplateDesignController::class, 'uploadBackground'])->name('templates.background.upload');
    Route::post('templates/{template}/thumbnail', [App\Http\Controllers\Admin\TemplateDesignController::class, 'generateThumbnail'])->name('templates.thumbnail.generate');
    Route::get('templates/{template}/export', [App\Http\Controllers\Admin\TemplateDesignController::class, 'exportDesign'])->name('templates.design.export');
    Route::post('templates/{template}/import', [App\Http\Controllers\Admin\TemplateDesignController::class, 'importDesign'])->name('templates.design.import');

    // Secure export with watermark
    Route::post('templates/{template}/export-secure', [App\Http\Controllers\Admin\TemplateExportController::class, 'exportWithWatermark'])->name('templates.export.secure');
    Route::post('templates/{template}/export-client', [App\Http\Controllers\Admin\TemplateExportController::class, 'exportForClient'])->name('templates.export.client');

    // Gestion des ressources utilisateur (fichiers)
    Route::prefix('user-files')->name('user-files.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserFileController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\UserFileController::class, 'store'])->name('store');
        Route::put('{userFile}', [App\Http\Controllers\Admin\UserFileController::class, 'update'])->name('update');
        Route::delete('{userFile}', [App\Http\Controllers\Admin\UserFileController::class, 'destroy'])->name('destroy');
        Route::get('folders', [App\Http\Controllers\Admin\UserFileController::class, 'folders'])->name('folders');
        Route::get('storage-info', [App\Http\Controllers\Admin\UserFileController::class, 'storageInfo'])->name('storage-info');
        Route::post('{userFile}/mark-used', [App\Http\Controllers\Admin\UserFileController::class, 'markAsUsed'])->name('mark-used');
    });

    // API Pexels pour images libres de droits
    Route::prefix('pexels')->name('pexels.')->group(function () {
        Route::get('search', [App\Http\Controllers\Admin\PexelsController::class, 'search'])->name('search');
        Route::get('curated', [App\Http\Controllers\Admin\PexelsController::class, 'curated'])->name('curated');
        Route::post('download', [App\Http\Controllers\Admin\PexelsController::class, 'download'])->name('download');
        Route::get('status', [App\Http\Controllers\Admin\PexelsController::class, 'status'])->name('status');
    });

    // API Remove.bg pour suppression d'arrière-plan
    Route::prefix('background-removal')->name('background-removal.')->group(function () {
        Route::post('remove-from-file', [App\Http\Controllers\Admin\BackgroundRemovalController::class, 'removeFromFile'])->name('remove-from-file');
        Route::post('remove-from-url', [App\Http\Controllers\Admin\BackgroundRemovalController::class, 'removeFromUrl'])->name('remove-from-url');
        Route::get('account-info', [App\Http\Controllers\Admin\BackgroundRemovalController::class, 'accountInfo'])->name('account-info');
        Route::get('status', [App\Http\Controllers\Admin\BackgroundRemovalController::class, 'status'])->name('status');
        Route::post('estimate-credits', [App\Http\Controllers\Admin\BackgroundRemovalController::class, 'estimateCredits'])->name('estimate-credits');
    });

    // Gestion des abonnements (super admin seulement)
    Route::middleware('super_admin')->group(function () {
        Route::resource('subscriptions', App\Http\Controllers\SubscriptionController::class)->except(['show', 'create', 'edit']);
    });
});

require __DIR__.'/auth.php';

// CSRF token refresh endpoint
Route::get('/csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
})->middleware('web');

// Route de test pour les abonnements
Route::get('/test-subscriptions', [App\Http\Controllers\TestController::class, 'testSubscriptions'])->name('test.subscriptions');

// Moyasar Webhook (must be accessible without authentication)
Route::post('/moyasar/webhook', [App\Http\Controllers\MoyasarWebhookController::class, 'handleWebhook'])->name('moyasar.webhook');

// Test routes (only in development)
if (app()->environment(['local', 'testing'])) {
    require __DIR__.'/test.php';

    // Route pour simuler un callback Moyasar réussi
    Route::get('/simulate-callback/{paymentId}', function ($paymentId) {
        return redirect("/client/payment/callback?id={$paymentId}&status=paid");
    })->name('simulate.callback');

    // Route pour simuler un callback d'achat de template réussi
    Route::get('/simulate-template-callback/{paymentId}', function ($paymentId) {
        return redirect("/client/template-purchase/callback?id={$paymentId}&status=paid");
    })->name('simulate.template.callback');
}
