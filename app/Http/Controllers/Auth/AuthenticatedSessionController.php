<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $locale = $request->route('locale') ?? app()->getLocale();
        $redirectTo = $request->session()->pull('url.intended', route('dashboard', ['locale' => $locale]));
        
        // If the intended URL doesn't have a locale, ensure we add it
        if (!preg_match('/\/[a-z]{2}\//', $redirectTo)) {
            $path = parse_url($redirectTo, PHP_URL_PATH);
            $redirectTo = url($locale . $path);
        }
        
        return response()->json(['redirect' => $redirectTo]);
    }

    /**
     * Destroy an authenticated session.
     */
    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // Get the current locale before logging out
        $locale = $request->route('locale') ?? app()->getLocale();
        
        // Logout from the application
        Auth::guard('web')->logout();
        
        // Invalidate the session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();
        
        // Clear any OAuth session data
        $request->session()->forget('auth_locale');
        $request->session()->forget('socialite_google_state');
        
        // Redirect to the localized homepage
        return redirect()->route('home', ['locale' => $locale]);
    }
}
