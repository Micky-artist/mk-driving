<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Show the user's profile
     */
    public function show()
    {
        return view('profile.show', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.show', ['locale' => app()->getLocale()])
            ->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:1024'],
        ]);

        $user = $request->user();
        
        // Delete old photo if exists
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Store new photo
        $path = $request->file('photo')->store('profile-photos', 'public');
        
        $user->forceFill([
            'profile_photo_path' => $path,
        ])->save();

        return back()->with('status', 'photo-updated');
    }

    /**
     * Delete the user's profile photo.
     */
    public function deletePhoto(Request $request)
    {
        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            
            $user->forceFill([
                'profile_photo_path' => null,
            ])->save();
        }

        return back()->with('status', 'photo-deleted');
    }
}
