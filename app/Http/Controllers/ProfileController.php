<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
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
            $imagePath = $request->file('image')->store('profile-images', 'public');
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

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', __('common.profile_updated'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
