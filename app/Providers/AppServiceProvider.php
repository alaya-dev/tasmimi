<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Models\Payment;
use App\Models\TemplatePurchase;
use App\Observers\PaymentObserver;
use App\Observers\TemplatePurchaseObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configuration des règles de mot de passe par défaut
        Password::defaults(function () {
            return Password::min(4); // Minimum 4 caractères au lieu de 8
        });

        // Register model observers for automatic invoice generation
        Payment::observe(PaymentObserver::class);
        TemplatePurchase::observe(TemplatePurchaseObserver::class);
    }
}
