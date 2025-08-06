<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactSettingsController extends Controller
{
    /**
     * Display the contact settings form.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/ContactSettings/Index', [
            'settings' => [
                'email' => ContactSetting::get('email', 'support@bitaqati.com'),
                'phone' => ContactSetting::get('phone', '+966 12 345 6789'),
                'address' => ContactSetting::get('address', 'الرياض، المملكة العربية السعودية')
            ]
        ]);
    }

    /**
     * Update the contact settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:500'
        ]);

        // Update each setting
        ContactSetting::set('email', $request->email);
        ContactSetting::set('phone', $request->phone);
        ContactSetting::set('address', $request->address);

        return redirect()->back()->with('success', 'تم حفظ إعدادات الاتصال بنجاح');
    }
}
