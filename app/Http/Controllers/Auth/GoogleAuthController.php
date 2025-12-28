<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visitor;
use App\Services\DeviceTrackingService;
use App\Services\PointsService;
use App\Mail\WelcomeEmailNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

            if (!$user) {
                Log::info('Creating new user');
                try {
                    // Split name into first and last name
                    $nameParts = explode(' ', $googleUser->name, 2);
                    $firstName = $nameParts[0] ?? $googleUser->name;
                    $lastName = $nameParts[1] ?? '';
                    
                    // Get device fingerprint and location data
                    $request = request();
                    $fingerprints = DeviceTrackingService::generateDeviceFingerprint($request);
                    $deviceInfo = Visitor::detectDevice($request->userAgent());
                    
                    $userData = [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $googleUser->email,
                        'password' => bcrypt(uniqid()),
                        'email_verified_at' => now(),
                        'google_id' => $googleUser->id,
                        'role' => 'USER',
                        'is_active' => true,
                        // Location data
                        'country' => Visitor::getCountryFromIP($request->ip()),
                        'city' => Visitor::getCityFromIP($request->ip()),
                        'timezone' => $request->header('Timezone') ?? config('app.timezone'),
                        // Device tracking data
                        'device_fingerprint' => $fingerprints['device_fingerprint'],
                        'registration_ip' => $request->ip(),
                        'registration_user_agent' => substr($request->userAgent(), 0, 500),
                        'registration_device_type' => $deviceInfo['device_type'],
                        'registration_browser' => $deviceInfo['browser'],
                        'registration_platform' => $deviceInfo['platform'],
                        // Timestamps
                        'registered_at' => now(),
                        'last_seen_at' => now(),
                    ];
                    
                    $user = User::create($userData);
                    
                    // Award points for joining
                    $pointsService = new PointsService();
                    $pointsService->awardPoints($user->id, 'account_created', [
                        'source' => 'google_oauth',
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ]);
                    
                    // Send welcome email
                    Log::info('Attempting to send welcome email', ['user_id' => $user->id, 'email' => $user->email]);
                    
                    try {
                        Mail::to($user->email)->locale($user->locale ?? app()->getLocale())->send(new WelcomeEmailNotification($user));
                        Log::info('Welcome email sent successfully', ['user_id' => $user->id, 'email' => $user->email]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send welcome email', ['user_id' => $user->id, 'email' => $user->email, 'error' => $e->getMessage()]);
                    }
                    
                    // Set session flag for welcome modal
                    session(['show_welcome_modal' => true]);
                    
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
                'message' => __('auth.login_success')
            ]);

        } catch (\Exception $e) {
            return view('auth.oauth-callback', [
                'redirectUrl' => route('login', app()->getLocale()),
                'status' => 'error',
                'message' => __('auth.google_login_failed')
            ]);
        }
    }
}
