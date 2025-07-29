<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'phone' => 'nullable|string|regex:/^[0-9]+$/|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'agree_terms' => 'required|accepted',
        ], [
            'phone.regex' => 'رقم الهاتف يجب أن يحتوي على أرقام فقط',
            'agree_terms.required' => 'يجب الموافقة على اتفاقية الاستخدام وسياسة الخصوصية',
            'agree_terms.accepted' => 'يجب الموافقة على اتفاقية الاستخدام وسياسة الخصوصية',
        ]);

        $user = User::create([
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_CLIENT, // Attribuer automatiquement le rôle client
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}