<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Inertia::share([
            'locale' => function () {
                return App::getLocale();
            },
            'translations' => function () {
                $locale = App::getLocale();
                $translations = [];
                
                // Load auth translations
                $authFile = resource_path("lang/{$locale}/auth.php");
                if (file_exists($authFile)) {
                    $authTranslations = include $authFile;
                    foreach ($authTranslations as $key => $value) {
                        $translations["auth.{$key}"] = $value;
                    }
                }
                
                // Load common translations
                $commonFile = resource_path("lang/{$locale}/common.php");
                if (file_exists($commonFile)) {
                    $commonTranslations = include $commonFile;
                    foreach ($commonTranslations as $key => $value) {
                        $translations["common.{$key}"] = $value;
                    }
                }
                
                return $translations;
            },
        ]);
    }
}