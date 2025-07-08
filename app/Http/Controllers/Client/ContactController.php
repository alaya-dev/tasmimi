<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    /**
     * Afficher la page de contact pour les clients
     */
    public function index(): Response
    {
        return Inertia::render('Client/Contact');
    }

    /**
     * Enregistrer un nouveau message de contact
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'يجب أن يكون البريد الإلكتروني صحيحاً',
            'email.max' => 'البريد الإلكتروني طويل جداً',
            'subject.required' => 'الموضوع مطلوب',
            'subject.max' => 'الموضوع طويل جداً',
            'content.required' => 'الرسالة مطلوبة',
            'content.max' => 'الرسالة طويلة جداً',
        ]);

        Contact::create([
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'تم إرسال رسالتك بنجاح!');
    }
}
