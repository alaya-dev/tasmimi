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
                
                // Load app translations
                $appFile = resource_path("lang/{$locale}/app.php");
                if (file_exists($appFile)) {
                    $appTranslations = include $appFile;
                    foreach ($appTranslations as $key => $value) {
                        $translations["app.{$key}"] = $value;
                    }
                }
                
                // Load validation translations
                $validationFile = resource_path("lang/{$locale}/validation.php");
                if (file_exists($validationFile)) {
                    $validationTranslations = include $validationFile;
                    foreach ($validationTranslations as $key => $value) {
                        $translations["validation.{$key}"] = $value;
                    }
                }
                
                // Load pagination translations
                $paginationFile = resource_path("lang/{$locale}/pagination.php");
                if (file_exists($paginationFile)) {
                    $paginationTranslations = include $paginationFile;
                    foreach ($paginationTranslations as $key => $value) {
                        $translations["pagination.{$key}"] = $value;
                    }
                }
                
                return $translations;
            },
        ]);
    }
}