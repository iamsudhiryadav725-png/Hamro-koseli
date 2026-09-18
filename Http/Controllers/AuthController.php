<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Show the buyer login page.
     */
    public function showLogin()
    {
        return app(PageController::class)->home();
    }

    /**
     * Redirect user to Google for authentication.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google authentication callback.
     */
    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('email', $googleUser->email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make(rand(10000, 99999)),
            ]);
        }

        if ($user->role === 'admin') {
            return redirect()->route('userlogin')->withErrors([
                'email' => 'Admin accounts cannot log in as buyers. Please use the admin login page.',
            ]);
        }

        Auth::login($user);

        return redirect()->route('home');
    }

    /**
     * Handle buyer login.
     *
     * Vendors are allowed to log in here as buyers too — they share the same
     * users table and both guards use independent session keys, so logging
     * in on the web guard while already logged in on the vendor guard causes
     * no conflict.
     */
    public function login(Request $request): RedirectResponse
    {
        // If the web guard is already authenticated, go home.
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::guard('web')->attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'These credentials do not match our records.',
                ]);
        }

        /** @var User $user */
        $user = Auth::guard('web')->user();

        if (! $user->is_active) {
            Auth::guard('web')->logout();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Your account is inactive. Please contact support.',
                ]);
        }

        if ($user->role === 'admin') {
            Auth::guard('web')->logout();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Admin accounts cannot log in as buyers. Please use the admin login page at /admin.',
                ]);
        }

        $request->session()->regenerate();

        // Buyer login always redirects to the home page,
        // even if the authenticated user is also a vendor.
        return redirect()->route('home');
    }

    /**
     * Log the user out of the web guard only.
     *
     * IMPORTANT: We do NOT call $request->session()->invalidate() here because
     * both the web guard and the vendor guard share the same underlying PHP
     * session. Invalidating the session would destroy the vendor guard token
     * as well, logging the vendor out even though they only clicked the
     * user-side logout button.
     *
     * Instead we:
     *   1. Log out only the web guard (clears its session key).
     *   2. Regenerate the CSRF token so old tokens become invalid.
     *
     * The vendor guard session key is untouched and the vendor stays logged in.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
