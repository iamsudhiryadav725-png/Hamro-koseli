<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureVendor
{
    public function handle(Request $request, Closure $next)
    {
        // Use the default guard (web) – the login flow authenticates via Auth::attempt()
        // and stores the user in the default session. The original code tried to
        // read a separate "vendor" guard that never gets populated, which caused a
        // 403 for every logged‑in vendor.
        if (! Auth::guard('vendor')->check()) {
            return redirect()->route('seller.login')
                ->withErrors(['email' => 'Please login to access the seller dashboard.']);
        }

        $user = Auth::guard('vendor')->user();
        if ($user->role !== 'vendor' || ! $user->hasRole('vendor')) {
            return redirect()->route('seller.login')
                ->withErrors(['email' => 'You do not have vendor access.']);
        }

        return $next($request);
    }
}
