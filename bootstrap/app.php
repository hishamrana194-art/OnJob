<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TenantScope;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // SetLocale must run first so Spatie Translatable uses the right language
        // for every subsequent middleware and controller in the request cycle.
        $middleware->api(prepend: [
            SetLocale::class,
        ]);

        // Register TenantScope middleware for API routes
        $middleware->api(append: [
            TenantScope::class,
        ]);

        // Register as named alias for route-level use
        $middleware->alias([
            'tenant.scope' => TenantScope::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Format AuthorizationException (thrown by $this->authorize() in controllers
        // and FormRequests) to match the project's standard API envelope so clients
        // always receive {"status":403,"message":"...","data":null}.
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => 403,
                    'message' => $e->getMessage() ?: 'This action is unauthorized.',
                    'data'    => null,
                ], 403);
            }
        });
    })->create();
