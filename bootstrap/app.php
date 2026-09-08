<?php

use App\Http\Middleware\AuditAdminActions;
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\SetLocale;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Redirect unauthenticated users to the admin login page
        $middleware->redirectGuestsTo(fn (Request $request) => route('admin.login'));

        // Apply locale and audit middleware globally to web routes
        $middleware->web(append: [
            SetLocale::class,
            AuditAdminActions::class,
        ]);

        // Register named middleware aliases
        $middleware->alias([
            'admin.role' => EnsureAdminRole::class,
            'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Return JSON errors for API requests
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
