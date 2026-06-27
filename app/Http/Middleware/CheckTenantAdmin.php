<?php

namespace App\Http\Middleware;

use App\Core\Tenant\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantAdmin
{
    /**
     * Maneja la petición entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si no está logueado, redirigir al login del administrador del tenant
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }

            return redirect()->route('tenant.admin.login');
        }

        $user = Auth::user();

        // Validar que pertenezca al tenant resuelto de la petición y tenga rol administrativo
        if (!TenantManager::hasTenant() || $user->tenant_id !== TenantManager::getTenantId() || (!$user->isTenantAdmin() && !$user->isTenantStaff())) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Acceso denegado a este panel administrativo.'], 403);
            }

            return redirect()->route('tenant.admin.login')->with('error', 'Acceso denegado. No perteneces a este negocio o no tienes permisos.');
        }

        return $next($request);
    }
}
