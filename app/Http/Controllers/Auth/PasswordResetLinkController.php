<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'email' => [__($status)],
                ]);
            }
            
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'passwords.sent',
                'message' => __($status),
                'email' => $request->email
            ]);
        }

        return back()->with([
            'status' => 'passwords.sent',
            'email' => $request->email
        ]);
    }
}
