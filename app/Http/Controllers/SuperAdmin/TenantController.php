<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    /**
     * Listado de todos los negocios en la plataforma.
     */
    public function index()
    {
        $tenants = Tenant::with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('superadmin.tenants.index', compact('tenants'));
    }

    /**
     * Muestra el formulario de creación de negocio.
     */
    public function create()
    {
        $plans = Plan::all();
        return view('superadmin.tenants.create', compact('plans'));
    }

    /**
     * Almacena un nuevo negocio en la plataforma.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'subdomain' => ['required', 'string', 'alpha_num', 'max:100', 'unique:tenants,subdomain'],
            'custom_domain' => ['nullable', 'string', 'max:150', 'unique:tenants,custom_domain'],
            'plan_id' => ['required', 'exists:plans,id'],
            'contact_email' => ['required', 'email', 'max:150'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', 'unique:users,email'],
            'owner_password' => ['required', 'string', 'min:8'],
            'primary_color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'secondary_color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
        ]);

        // 1. Crear el Tenant
        $tenant = Tenant::create([
            'plan_id' => $request->plan_id,
            'name' => $request->name,
            'subdomain' => strtolower($request->subdomain),
            'custom_domain' => $request->custom_domain ? strtolower($request->custom_domain) : null,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'status' => 'active',
            'trial_ends_at' => now()->addDays(30),
        ]);

        // 2. Crear el Usuario Dueño del Negocio (Owner)
        User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->owner_name,
            'email' => $request->owner_email,
            'password' => Hash::make($request->owner_password),
            'role' => 'tenant_admin',
            'status' => 'active',
        ]);

        // 3. Inicializar Configuraciones de Branding por defecto
        $defaultSettings = [
            'logo_text' => $tenant->name,
            'whatsapp_number' => preg_replace('/[^0-9]/', '', $tenant->contact_phone),
            'bank_transfer_info' => 'Por favor actualice los datos de transferencia en su panel.',
            'facebook_url' => '',
            'instagram_url' => '',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::create([
                'tenant_id' => $tenant->id,
                'key' => $key,
                'value' => $value,
            ]);
        }

        return redirect()
            ->route('superadmin.tenants.index')
            ->with('success', "Negocio '{$tenant->name}' y cuenta de administrador creados correctamente.");
    }

    /**
     * Muestra el formulario de edición de un negocio.
     */
    public function edit(Tenant $tenant)
    {
        $plans = Plan::all();
        return view('superadmin.tenants.edit', compact('tenant', 'plans'));
    }

    /**
     * Actualiza la configuración de un negocio.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'subdomain' => ['required', 'string', 'alpha_num', 'max:100', Rule::unique('tenants', 'subdomain')->ignore($tenant->id)],
            'custom_domain' => ['nullable', 'string', 'max:150', Rule::unique('tenants', 'custom_domain')->ignore($tenant->id)],
            'plan_id' => ['required', 'exists:plans,id'],
            'contact_email' => ['required', 'email', 'max:150'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'suspended', 'trial'])],
            'primary_color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'secondary_color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
        ]);

        $tenant->update([
            'plan_id' => $request->plan_id,
            'name' => $request->name,
            'subdomain' => strtolower($request->subdomain),
            'custom_domain' => $request->custom_domain ? strtolower($request->custom_domain) : null,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('superadmin.tenants.index')
            ->with('success', "Configuraciones del negocio '{$tenant->name}' actualizadas.");
    }

    /**
     * Suspende o activa un negocio de forma rápida.
     */
    public function toggleStatus(Tenant $tenant)
    {
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        $message = $newStatus === 'suspended' ? 'Negocio suspendido con éxito.' : 'Negocio activado con éxito.';

        return redirect()
            ->route('superadmin.tenants.index')
            ->with('success', "El negocio '{$tenant->name}' ha sido cambiado a: " . strtoupper($newStatus));
    }
}
