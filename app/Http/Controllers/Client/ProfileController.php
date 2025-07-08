<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the client's profile page.
     */
    public function show(Request $request): Response
    {
        $user = $request->user();
        
        // Get user statistics
        $stats = [
            'designs_count' => 0, // You can implement this based on your designs model
            'orders_count' => 0,  // You can implement this based on your orders model
        ];

        return Inertia::render('Client/Profile', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Update the client's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && str_starts_with($user->image, '/storage/')) {
                $oldImagePath = str_replace('/storage/', '', $user->image);
                Storage::disk('public')->delete($oldImagePath);
            }
            
            // Store new image
            $imagePath = $request->file('image')->store('client-profile-images', 'public');
            $validated['image'] = '/storage/' . $imagePath;
        } elseif ($request->has('remove_image') && $request->remove_image) {
            // Remove image if requested
            if ($user->image && str_starts_with($user->image, '/storage/')) {
                $oldImagePath = str_replace('/storage/', '', $user->image);
                Storage::disk('public')->delete($oldImagePath);
            }
            $validated['image'] = null;
        }

        $user->fill($validated);
        $user->save();

        return redirect()->route('client.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * Update the client's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('client.profile')->with('success', 'تم تحديث كلمة المرور بنجاح');
    }
}