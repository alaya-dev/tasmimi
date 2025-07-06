<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     *
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, ['en', 'ar'])) {
            return redirect()->back();
        }
        
        // Set the locale in session
        Session::put('locale', $locale);
        App::setLocale($locale);
        
        return redirect()->back();
    }
    
    /**
     * Get current locale
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function current()
    {
        return response()->json([
            'current_locale' => App::getLocale(),
            'available_locales' => ['en', 'ar']
        ]);
    }
}