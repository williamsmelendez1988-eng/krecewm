@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Identidad y Branding</h2>
        <p class="text-sm text-slate-500">Personaliza la imagen de tu tienda: logo, colores, información de contacto y redes sociales.</p>
    </div>

    @if($errors->any())
        <div class="rounded-lg bg-rose-50 border border-rose-200 p-4 text-rose-800 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tenant.admin.settings.update') }}" enctype="multipart/form-data" class="space-y-8 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        @csrf

        {{-- Sección 1: Información General --}}
        <div class="space-y-4">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2">Información General del Negocio</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-slate-700">Nombre del Negocio</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $tenant->name) }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                </div>
                <div>
                    <label for="logo_text" class="block text-sm font-medium text-slate-700">Texto Logo (si no tiene imagen)</label>
                    <input type="text" name="logo_text" id="logo_text" value="{{ old('logo_text', $settings['logo_text']) }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                </div>
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-slate-700">Correo de Contacto</label>
                    <input type="email" name="contact_email" id="contact_email" required value="{{ old('contact_email', $tenant->contact_email) }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                </div>
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-slate-700">Teléfono de Contacto</label>
                    <input type="text" name="contact_phone" id="contact_phone" required value="{{ old('contact_phone', $tenant->contact_phone) }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium text-slate-700">Dirección</label>
                    <input type="text" name="address" id="address" value="{{ old('address', $tenant->address) }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-slate-700">Ciudad</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $tenant->city) }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                </div>
            </div>
        </div>

        {{-- Sección 2: Branding Visual --}}
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2">Identidad Visual</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="primary_color" class="block text-sm font-medium text-slate-700">Color Primario</label>
                    <div class="mt-1 flex items-center space-x-3">
                        <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', $tenant->primary_color) }}"
                            class="h-11 w-14 rounded-lg border border-slate-300 p-0.5 cursor-pointer">
                        <div>
                            <p class="text-xs font-medium text-slate-700">Color actual: <span class="font-mono">{{ $tenant->primary_color }}</span></p>
                            <p class="text-xs text-slate-400">Botones, encabezados, sidebar.</p>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="secondary_color" class="block text-sm font-medium text-slate-700">Color Secundario / Acento</label>
                    <div class="mt-1 flex items-center space-x-3">
                        <input type="color" name="secondary_color" id="secondary_color" value="{{ old('secondary_color', $tenant->secondary_color) }}"
                            class="h-11 w-14 rounded-lg border border-slate-300 p-0.5 cursor-pointer">
                        <div>
                            <p class="text-xs font-medium text-slate-700">Color actual: <span class="font-mono">{{ $tenant->secondary_color }}</span></p>
                            <p class="text-xs text-slate-400">Badges, alertas, precios destacados.</p>
                        </div>
                    </div>
                </div>

                {{-- Logotipo --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Logotipo</label>
                    <div class="mt-2 flex items-start space-x-4">
                        @if($tenant->logo)
                            <div class="flex-shrink-0 h-16 w-32 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('storage/' . $tenant->logo) }}" alt="Logo actual" class="max-h-14 max-w-full object-contain">
                            </div>
                        @else
                            <div class="flex-shrink-0 h-16 w-32 rounded-lg border border-dashed border-slate-300 bg-slate-50 flex items-center justify-center">
                                <span class="text-xs text-slate-400 font-medium px-2 text-center">Sin logo cargado</span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/jpg,image/svg+xml"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                            <p class="mt-1 text-xs text-slate-400">PNG, JPG, SVG. Máx. 2MB. Recomendado: 200x80px.</p>
                        </div>
                    </div>
                </div>

                {{-- Favicon --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700">Favicon (Ícono del Navegador)</label>
                    <input type="file" name="favicon" id="favicon" accept="image/png,image/x-icon"
                        class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 transition-all">
                    <p class="mt-1 text-xs text-slate-400">PNG o ICO. Máx. 512KB. Recomendado: 32x32px o 64x64px.</p>
                </div>
            </div>
        </div>

        {{-- Sección 3: Pagos y WhatsApp --}}
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2">Métodos de Contacto y Pago</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="whatsapp_number" class="block text-sm font-medium text-slate-700">Número de WhatsApp</label>
                    <div class="mt-1 flex rounded-lg shadow-sm">
                        <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-3 text-slate-500 text-sm">+</span>
                        <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number']) }}"
                            class="block w-full min-w-0 flex-1 rounded-r-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary" placeholder="541199998888 (sin + ni espacios)">
                    </div>
                    <p class="mt-1 text-xs text-slate-400">Incluye código de país. Ej: 541199998888 para Argentina.</p>
                </div>
                <div>
                    <label for="bank_transfer_info" class="block text-sm font-medium text-slate-700">Datos de Transferencia Bancaria</label>
                    <textarea name="bank_transfer_info" id="bank_transfer_info" rows="3"
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">{{ old('bank_transfer_info', $settings['bank_transfer_info']) }}</textarea>
                    <p class="mt-1 text-xs text-slate-400">Banco, CBU, alias o instrucciones de pago manual.</p>
                </div>
                
                {{-- Pago Móvil Venezuela --}}
                <div class="md:col-span-2 border-t border-slate-100/70 pt-4 mt-2">
                    <h4 class="text-sm font-bold text-slate-750 mb-3">Datos de Pago Móvil (Venezuela)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="pago_movil_bank" class="block text-xs font-semibold text-slate-600">Banco</label>
                            <input type="text" name="pago_movil_bank" id="pago_movil_bank" value="{{ old('pago_movil_bank', $settings['pago_movil_bank']) }}"
                                class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all" placeholder="Ej. Banesco">
                        </div>
                        <div>
                            <label for="pago_movil_phone" class="block text-xs font-semibold text-slate-600">Teléfono Pago Móvil</label>
                            <input type="text" name="pago_movil_phone" id="pago_movil_phone" value="{{ old('pago_movil_phone', $settings['pago_movil_phone']) }}"
                                class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all" placeholder="Ej. 04121234567">
                        </div>
                        <div>
                            <label for="pago_movil_id" class="block text-xs font-semibold text-slate-600">Cédula o RIF</label>
                            <input type="text" name="pago_movil_id" id="pago_movil_id" value="{{ old('pago_movil_id', $settings['pago_movil_id']) }}"
                                class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all" placeholder="Ej. V-12345678">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección 4: Moneda y Tasa de Cambio --}}
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="flex items-center gap-3">
                <h3 class="text-base font-bold text-slate-800">💱 Moneda y Tasa de Cambio</h3>
                <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full">Venezuela</span>
            </div>
            <p class="text-xs text-slate-500 -mt-2">Configura la tasa de cambio USD → Bolívares para mostrar precios duales en el catálogo público.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="exchange_rate_usd_bs" class="block text-sm font-medium text-slate-700">Tasa de Cambio: 1 USD = ? Bs</label>
                    <div class="mt-1 flex rounded-lg shadow-sm">
                        <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-3 text-slate-500 text-sm font-semibold">Bs.</span>
                        <input type="number" step="0.01" min="0" name="exchange_rate_usd_bs" id="exchange_rate_usd_bs"
                            value="{{ old('exchange_rate_usd_bs', $settings['exchange_rate_usd_bs']) }}"
                            class="block w-full min-w-0 flex-1 rounded-r-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary"
                            placeholder="Ej: 92.50">
                    </div>
                    <p class="mt-1 text-xs text-slate-400">Ingresa 0 para ocultar el precio en Bolívares.</p>
                </div>
                <div class="flex items-end">
                    @php $rate = (float)($settings['exchange_rate_usd_bs'] ?? 0); @endphp
                    @if($rate > 0)
                    <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 w-full">
                        <p class="text-xs text-emerald-700 font-semibold">Tasa activa:</p>
                        <p class="text-lg font-bold text-emerald-800">$1 USD = Bs. {{ number_format($rate, 2) }}</p>
                        <p class="text-xs text-emerald-600 mt-0.5">Los precios duales aparecerán en el catálogo</p>
                    </div>
                    @else
                    <div class="rounded-lg bg-slate-50 border border-slate-200 px-4 py-3 w-full">
                        <p class="text-xs text-slate-500">No hay tasa de cambio configurada.</p>
                        <p class="text-xs text-slate-400 mt-0.5">Ingresa un valor para activar precios en Bs.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sección 5: Redes Sociales --}}
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2">Redes Sociales</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="facebook_url" class="block text-sm font-medium text-slate-700">Facebook</label>
                    <div class="mt-1 flex rounded-lg shadow-sm">
                        <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-3 text-slate-500 text-xs">facebook.com/</span>
                        <input type="url" name="facebook_url" id="facebook_url" value="{{ old('facebook_url', $settings['facebook_url']) }}"
                            class="block w-full min-w-0 flex-1 rounded-r-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary" placeholder="https://facebook.com/tutienda">
                    </div>
                </div>
                <div>
                    <label for="instagram_url" class="block text-sm font-medium text-slate-700">Instagram</label>
                    <div class="mt-1 flex rounded-lg shadow-sm">
                        <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-3 text-slate-500 text-xs">instagram.com/</span>
                        <input type="url" name="instagram_url" id="instagram_url" value="{{ old('instagram_url', $settings['instagram_url']) }}"
                            class="block w-full min-w-0 flex-1 rounded-r-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary" placeholder="https://instagram.com/tutienda">
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de Acción --}}
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow-md rounded-lg transition-all duration-200">
                Guardar Configuración
            </button>
        </div>
    </form>
</div>
@endsection
