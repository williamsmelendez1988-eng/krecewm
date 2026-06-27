<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
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
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autorizado. Se requieren permisos de Super Administrador.'], 403);
            }

            return redirect()->route('login')->with('error', 'Acceso denegado. Se requieren permisos de Super Administrador.');
        }

        return $next($request);
    }
}
