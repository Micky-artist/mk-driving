<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        // Store the current locale in the session
        session(['auth_locale' => app()->getLocale()]);

        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // Check if user already exists
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => bcrypt(uniqid()),
                    'email_verified_at' => now(),
                    'google_id' => $googleUser->id,
                ]);
            } else {
                // Update existing user with Google ID if not set
                if (empty($user->google_id)) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                }
            }

            // Log the user in
            Auth::login($user);

            // Get the stored locale or default to 'en'
            $locale = session('auth_locale', 'en');
            
            // Clear the stored locale
            session()->forget('auth_locale');
            
            // Return a view that will close the popup and redirect the parent window
            return view('auth.oauth-callback', [
                'redirectUrl' => url("/{$locale}/dashboard"),
                'status' => 'success',
                'message' => 'Login successful!'
            ]);

        } catch (\Exception $e) {
            return view('auth.oauth-callback', [
                'redirectUrl' => route('login', app()->getLocale()),
                'status' => 'error',
                'message' => 'Unable to login using Google. Please try again.'
            ]);
        }
    }
}
