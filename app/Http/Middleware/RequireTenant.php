<?php

namespace App\Http\Middleware;

use App\Core\Tenant\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!TenantManager::hasTenant()) {
            abort(404);
        }

        return $next($request);
    }
}
