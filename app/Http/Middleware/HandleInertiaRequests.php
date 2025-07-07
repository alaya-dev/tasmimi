<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'locale' => app()->getLocale(),
            'translations' => $this->getTranslations(),
            'appName' => config('app.name'),
        ];
    }

    /**
     * Get all translations for the current locale
     */
    private function getTranslations(): array
    {
        $locale = app()->getLocale();
        $translations = [];

        // Load common translations
        $commonPath = resource_path("lang/{$locale}/common.php");
        if (file_exists($commonPath)) {
            $common = include $commonPath;
            foreach ($common as $key => $value) {
                $translations["common.{$key}"] = $value;
            }
        }

        // Load auth translations
        $authPath = resource_path("lang/{$locale}/auth.php");
        if (file_exists($authPath)) {
            $auth = include $authPath;
            foreach ($auth as $key => $value) {
                $translations["auth.{$key}"] = $value;
            }
        }

        // Load validation translations
        $validationPath = resource_path("lang/{$locale}/validation.php");
        if (file_exists($validationPath)) {
            $validation = include $validationPath;
            foreach ($validation as $key => $value) {
                $translations["validation.{$key}"] = $value;
            }
        }

        return $translations;
    }
}
