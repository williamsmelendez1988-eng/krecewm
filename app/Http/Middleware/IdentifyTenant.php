<?php

namespace App\Http\Middleware;

use App\Core\Tenant\TenantManager;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Maneja la petición entrante para resolver el Tenant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $centralDomain = config('app.central_domain', env('CENTRAL_DOMAIN', 'krecewm.local'));

        // -----------------------------------------------------------------------
        // MODO DESARROLLO: Bypass por query param ?_tenant=subdomain
        // Solo activo cuando APP_ENV=local y el host es 127.0.0.1 / localhost.
        // Permite probar tenants sin necesidad de configurar el archivo hosts.
        // Ejemplo: http://127.0.0.1:8000/admin/login?_tenant=demo
        // -----------------------------------------------------------------------
        if (app()->environment('local') && in_array($host, ['127.0.0.1', 'localhost'])) {
            $devSubdomain = $request->query('_tenant') ?? session('_dev_tenant');

            if ($devSubdomain) {
                // Persistir el tenant de desarrollo en sesión para no tener que
                // pasar ?_tenant= en cada petición del panel admin
                session(['_dev_tenant' => $devSubdomain]);

                $tenant = Tenant::where('subdomain', $devSubdomain)
                    ->whereNull('deleted_at')
                    ->first();

                if ($tenant) {
                    if ($tenant->status === 'suspended') {
                        return response()->view('errors.tenant_suspended', ['tenant' => $tenant], 403);
                    }
                    TenantManager::setTenant($tenant);
                    view()->share('currentTenant', $tenant);
                    return $next($request);
                }
            }

            // Sin ?_tenant= ni sesión activa → dominio central (SuperAdmin)
            return $next($request);
        }

        // Si es el dominio central (ej. krecewm.local) o localhost exacto sin subdominios,
        // no resolvemos ningún tenant (es el portal corporativo o el panel Super Admin).
        if ($host === $centralDomain || $host === 'localhost' || $host === '127.0.0.1') {
            return $next($request);
        }

        $tenant = null;

        // 1. Intentar resolver por dominio personalizado completo (ej: www.tienda.com)
        $tenant = Tenant::where('custom_domain', $host)
            ->whereNull('deleted_at')
            ->first();

        // 2. Si no se resuelve por dominio personalizado, intentar resolver por subdominio
        if (!$tenant) {
            // Extraer el subdominio (ej: "repuestos" de "repuestos.krecewm.local")
            $subdomain = '';
            if (str_ends_with($host, '.' . $centralDomain)) {
                $subdomain = str_replace('.' . $centralDomain, '', $host);
            } else {
                // Caso fallback por si el host contiene subdominios con otros formatos
                $parts = explode('.', $host);
                if (count($parts) > 2) {
                    $subdomain = $parts[0];
                }
            }

            if (!empty($subdomain) && $subdomain !== 'www') {
                $tenant = Tenant::where('subdomain', $subdomain)
                    ->whereNull('deleted_at')
                    ->first();
            }
        }

        // Si no se encuentra el negocio, retornar error 404
        if (!$tenant) {
            abort(404, 'Negocio no encontrado en la plataforma KreceWM.');
        }

        // Si el negocio está suspendido, bloquear acceso
        if ($tenant->status === 'suspended') {
            return response()->view('errors.tenant_suspended', [
                'tenant' => $tenant
            ], 403);
        }

        // Registrar el tenant en memoria
        TenantManager::setTenant($tenant);

        // Compartir el tenant globalmente con las vistas Blade para el branding dinámico
        view()->share('currentTenant', $tenant);

        return $next($request);
    }
}
