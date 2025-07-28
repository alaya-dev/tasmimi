<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Moyasar\PaymentService;
use App\Services\Moyasar\InvoiceService;

class MoyasarServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('moyasar.payment', function ($app) {
            return new PaymentService();
        });

        $this->app->singleton('moyasar.invoice', function ($app) {
            return new InvoiceService();
        });

        // Bind the services for dependency injection
        $this->app->bind(PaymentService::class, function ($app) {
            return $app->make('moyasar.payment');
        });

        $this->app->bind(InvoiceService::class, function ($app) {
            return $app->make('moyasar.invoice');
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
