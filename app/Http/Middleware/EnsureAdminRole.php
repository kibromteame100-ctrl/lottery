<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    public function handle(Request $request, Response|Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth()->user();

        if (empty($roles)) {
            // Any admin role is fine
            if (!$user->isAdmin()) {
                abort(403, __('auth.unauthorized'));
            }
        } else {
            if (!$user->hasAnyRole($roles)) {
                abort(403, __('auth.unauthorized'));
            }
        }

        return $next($request);
    }
}
