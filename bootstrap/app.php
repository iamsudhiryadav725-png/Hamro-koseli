<?php

use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('seller-dashboard', 'product-management', 'create-product', 'edit-product*', 'orders', 'order-details')) {
                return '/seller-login';
            }

            return '/userlogin';
        });

        $middleware->alias([
            'guest' => RedirectIfAuthenticated::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Redirect 403 (wrong role) to the appropriate login page instead of showing forbidden error
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('seller-dashboard', 'product-management', 'create-product', 'edit-product*', 'orders', 'order-details')) {
                return redirect('/seller-login')
                    ->withErrors(['email' => 'You do not have vendor access.']);
            }

            return redirect('/userlogin')
                ->withErrors(['email' => 'You do not have access to this page.']);
        });
    })->create();
