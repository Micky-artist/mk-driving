<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            Log::info('Google OAuth callback initiated');
            $googleUser = Socialite::driver('google')->stateless()->user();
            Log::info('Google user data received', ['email' => $googleUser->email, 'id' => $googleUser->id]);
            
            // Check if user already exists
            $user = User::where('email', $googleUser->email)->first();
            Log::info('User lookup result', ['exists' => $user ? 'yes' : 'no']);

            if (!$user) {
                Log::info('Creating new user');
                try {
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'password' => bcrypt(uniqid()),
                        'email_verified_at' => now(),
                        'google_id' => $googleUser->id,
                    ]);
                    Log::info('New user created', ['user_id' => $user->id]);
                } catch (\Exception $e) {
                    Log::error('Error creating user', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            } else {
                Log::info('User found', ['user_id' => $user->id]);
                // Update existing user with Google ID if not set
                if (empty($user->google_id)) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                    Log::info('Updated user with Google ID');
                }
            }

            // Log the user in
            Log::info('Attempting to log in user', ['user_id' => $user->id]);
            Auth::login($user);
            Log::info('User logged in successfully', ['user_id' => $user->id]);

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
