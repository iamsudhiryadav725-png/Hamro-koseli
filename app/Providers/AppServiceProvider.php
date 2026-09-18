<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ─── Rate Limiters ────────────────────────────────────────────────────
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->input('email').'|'.$request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'email' => 'Too many attempts. Please wait a minute before trying again.',
                    ]);
                });
        });

        RateLimiter::for('payment', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->user()?->id ?: $request->ip());
        });

        // ─── Force HTTPS in production ────────────────────────────────────────
        if (! app()->isLocal()) {
            \URL::forceScheme('https');
        }

        // ─── Custom password reset URL (supports both vendor & buyer) ─────────
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            $routeName = $notifiable->isVendor() ? 'seller.password.reset' : 'password.reset';

            return url(route($routeName, [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        });

        // ─── Dynamic public disk URL (fixes Filament preview hangs) ──────────
        if (! app()->runningInConsole()) {
            config(['filesystems.disks.public.url' => asset('storage')]);
        }
    }
}
