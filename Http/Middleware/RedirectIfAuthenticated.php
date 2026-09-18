<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $isSellerRoute = $request->is('seller-login')
            || $request->is('seller/forgot-password')
            || $request->is('seller/reset-password*')
            || $request->is('vendor/register');

        $isUserRoute = $request->is('userlogin')
            || $request->is('login')
            || $request->is('register');

        // ── Vendor guard check (seller routes only) ───────────────────────────
        if (Auth::guard('vendor')->check()) {
            // Already logged in as vendor and hitting a seller guest route → dashboard
            if ($isSellerRoute) {
                return redirect()->route('dashboard');
            }
            // Vendor hitting user login/register → allow through so they can also
            // log in on the user side (vendor cookie is separate, no conflict)
        }

        // ── Web guard check (user routes) ─────────────────────────────────────
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            if ($user->role === 'admin') {
                Auth::guard('web')->logout();

                return redirect()->route('userlogin')->withErrors([
                    'email' => 'Admin accounts cannot log in as buyers. Please use the admin login page at /admin.',
                ]);
            }

            // Logged-in web user hitting a seller guest route → let them through
            // (they may want to log in as vendor separately)
            if ($isSellerRoute) {
                return $next($request);
            }

            // Logged-in web user hitting a user guest route → send home
            if ($isUserRoute) {
                return redirect()->route('home');
            }
        }

        return $next($request);
    }
}
