<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditAdminActions
{
    /**
     * Routes that trigger audit logging (method:path pattern).
     */
    protected array $auditRoutes = [
        'POST'   => ['/admin/tickets/', '/admin/lotteries', '/admin/users'],
        'PUT'    => ['/admin/lotteries/', '/admin/users/', '/admin/settings'],
        'PATCH'  => ['/admin/tickets/', '/admin/users/'],
        'DELETE' => ['/admin/'],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check() && auth()->user()->isAdmin() && $this->shouldAudit($request)) {
            AuditLog::record(
                action: $request->method() . ' ' . $request->path(),
                entityType: 'http_request',
                entityId: null,
                oldValues: [],
                newValues: $this->sanitize($request->except(['password', 'screenshot', '_token'])),
            );
        }

        return $response;
    }

    private function shouldAudit(Request $request): bool
    {
        $method = $request->method();
        $path = '/' . $request->path();

        foreach ($this->auditRoutes[$method] ?? [] as $pattern) {
            if (str_contains($path, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function sanitize(array $data): array
    {
        // Remove binary / large data
        unset($data['screenshot'], $data['file']);
        return $data;
    }
}
