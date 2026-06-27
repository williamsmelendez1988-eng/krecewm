<?php

namespace App\Http\Controllers\Admin;

use App\Core\Tenant\TenantManager;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Muestra la pantalla de configuración del negocio.
     */
    public function index()
    {
        $tenant = TenantManager::getTenant();

        // Obtener todas las llaves clave/valor para pasarlas a la vista
        $settings = [
            'logo_text'             => $tenant->getSetting('logo_text', $tenant->name),
            'whatsapp_number'       => $tenant->getSetting('whatsapp_number', ''),
            'bank_transfer_info'    => $tenant->getSetting('bank_transfer_info', ''),
            'pago_movil_bank'       => $tenant->getSetting('pago_movil_bank', ''),
            'pago_movil_phone'      => $tenant->getSetting('pago_movil_phone', ''),
            'pago_movil_id'         => $tenant->getSetting('pago_movil_id', ''),
            'facebook_url'          => $tenant->getSetting('facebook_url', ''),
            'instagram_url'         => $tenant->getSetting('instagram_url', ''),
            'exchange_rate_usd_bs'  => $tenant->getSetting('exchange_rate_usd_bs', '0'),
        ];

        return view('admin.settings.index', compact('tenant', 'settings'));
    }

    /**
     * Guarda la configuración del negocio.
     */
    public function update(Request $request)
    {
        $tenant = TenantManager::getTenant();

        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'primary_color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'secondary_color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'contact_email' => ['required', 'email', 'max:150'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,ico', 'max:512'],
            
            // Settings adicionales
            'logo_text' => ['required', 'string', 'max:100'],
            'whatsapp_number' => ['required', 'string', 'max:30'],
            'bank_transfer_info' => ['nullable', 'string'],
            'pago_movil_bank'       => ['nullable', 'string', 'max:100'],
            'pago_movil_phone'      => ['nullable', 'string', 'max:50'],
            'pago_movil_id'         => ['nullable', 'string', 'max:50'],
            'facebook_url'          => ['nullable', 'url', 'max:255'],
            'instagram_url'         => ['nullable', 'url', 'max:255'],
            'exchange_rate_usd_bs'  => ['nullable', 'numeric', 'min:0'],
        ]);

        // 1. Manejo de archivos (Logotipo y Favicon) usando Disk local preparado para la nube
        $tenantData = [
            'name' => $request->name,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'address' => $request->address,
            'city' => $request->city,
        ];

        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            // Guardar el nuevo logotipo
            $logoPath = $request->file('logo')->store("tenants/{$tenant->id}/images", 'public');
            $tenantData['logo'] = $logoPath;
        }

        if ($request->hasFile('favicon')) {
            // Guardar favicon en settings dinámicas para no sobrecargar el modelo
            $faviconPath = $request->file('favicon')->store("tenants/{$tenant->id}/images", 'public');
            $this->saveSetting('favicon', $faviconPath);
        }

        // Actualizar el Tenant
        $tenant->update($tenantData);

        // 2. Actualizar las configuraciones dinámicas (key-value)
        $this->saveSetting('logo_text', $request->logo_text);
        $this->saveSetting('whatsapp_number', preg_replace('/[^0-9]/', '', $request->whatsapp_number));
        $this->saveSetting('bank_transfer_info', $request->bank_transfer_info);
        $this->saveSetting('pago_movil_bank', $request->pago_movil_bank);
        $this->saveSetting('pago_movil_phone', $request->pago_movil_phone);
        $this->saveSetting('pago_movil_id', $request->pago_movil_id);
        $this->saveSetting('facebook_url', $request->facebook_url ?? '');
        $this->saveSetting('instagram_url', $request->instagram_url ?? '');
        $this->saveSetting('exchange_rate_usd_bs', (string) ($request->exchange_rate_usd_bs ?? '0'));

        return redirect()
            ->route('tenant.admin.settings.index')
            ->with('success', 'Configuraciones de identidad y branding actualizadas correctamente.');
    }

    /**
     * Helper para guardar o actualizar una variable de configuración.
     */
    protected function saveSetting(string $key, ?string $value): void
    {
        $tenantId = TenantManager::getTenantId();

        Setting::updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => $key],
            ['value' => $value]
        );
    }
}
