<?php

namespace App\Http\Controllers\Auth;

use App\Core\Tenant\TenantManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLoginForm()
    {
        // Si ya está logueado, redirigir al dashboard correspondiente
        if (Auth::check()) {
            return $this->redirectAfterLogin();
        }

        if (TenantManager::hasTenant()) {
            return view('auth.tenant_login', [
                'tenant' => TenantManager::getTenant()
            ]);
        }

        return view('auth.superadmin_login');
    }

    /**
     * Procesa la petición de inicio de sesión.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // 1. Si estamos en el dominio central, el usuario DEBE ser superadmin
            if (!TenantManager::hasTenant()) {
                if ($user->isSuperAdmin()) {
                    $request->session()->regenerate();
                    return $this->redirectAfterLogin();
                }

                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Acceso denegado. Este panel es exclusivo para Super Administradores de KreceWM.',
                ]);
            }

            // 2. Si estamos en un tenant, el usuario DEBE pertenecer a este tenant y tener rol adecuado
            if (TenantManager::hasTenant()) {
                if ($user->tenant_id === TenantManager::getTenantId() && ($user->isTenantAdmin() || $user->isTenantStaff())) {
                    $request->session()->regenerate();
                    return $this->redirectAfterLogin();
                }

                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Credenciales inválidas para este negocio o no tiene permisos de acceso.',
                ]);
            }
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Cierra la sesión activa.
     */
    public function logout(Request $request)
    {
        $hasTenant = TenantManager::hasTenant();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($hasTenant) {
            return redirect()->route('tenant.admin.login');
        }

        return redirect()->route('login');
    }

    /**
     * Redirección dinámica según el rol del usuario autenticado.
     */
    protected function redirectAfterLogin()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->isTenantAdmin() || $user->isTenantStaff()) {
            return redirect()->route('tenant.admin.dashboard');
        }

        return redirect('/');
    }
}
