<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visitor;
use App\Services\DeviceTrackingService;
use App\Mail\WelcomeEmailNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('auth.register');
    }
    
    /**
     * Check if the given URL is a valid redirect URL
     * 
     * @param string $url
     * @return bool
     */
    protected function isValidRedirectUrl($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Ensure the URL is from the same domain to prevent open redirects
        $appUrl = config('app.url');
        $urlHost = parse_url($url, PHP_URL_HOST);
        $appHost = parse_url($appUrl, PHP_URL_HOST);
        
        return $urlHost === $appHost;
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::min(6)],
            ], [
                'email.unique' => __('auth.errors.email_exists')
            ]);

            // Get device fingerprint and location data
            $fingerprints = DeviceTrackingService::generateDeviceFingerprint($request);
            $deviceInfo = Visitor::detectDevice($request->userAgent());
            
            $userData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'USER', // Must be one of: 'USER', 'ADMIN', or 'INSTRUCTOR'
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

            // Send welcome email with marketing links
            Mail::to($user->email)->locale($user->locale ?? app()->getLocale())->send(new WelcomeEmailNotification($user));

            event(new Registered($user));
            Auth::login($user);

            // Get return_to from the request or fall back to the URL parameter
            $returnTo = $request->input('return_to') ?? $request->query('return_to');
            
            // Validate the return URL to prevent open redirects
            if ($returnTo && $this->isValidRedirectUrl($returnTo)) {
                return response()->json(['redirect' => $returnTo]);
            }
            
            // Default redirect to dashboard (since user is now logged in)
            return response()->json(['redirect' => route('dashboard', ['locale' => app()->getLocale()])]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'The given data was invalid.'
            ], 422);
        }
    }
}
