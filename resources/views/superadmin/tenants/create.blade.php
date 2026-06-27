@extends('layouts.superadmin')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Crear Nuevo Negocio</h2>
        <p class="text-sm text-slate-500">Provee una nueva tienda digital con su base de datos aislada y su cuenta administrativa de dueño.</p>
    </div>

    @if($errors->any())
        <div class="rounded-lg bg-rose-50 border border-rose-200 p-4 text-rose-800 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.tenants.store') }}" class="space-y-8 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        @csrf

        <!-- Sección 1: Datos del Negocio -->
        <div class="space-y-4">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2">Información del Negocio</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Nombre Comercial</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: Repuestos López">
                </div>
                <div>
                    <label for="subdomain" class="block text-sm font-medium text-slate-700">Subdominio</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="text" name="subdomain" id="subdomain" required value="{{ old('subdomain') }}" class="block w-full min-w-0 flex-1 rounded-l-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500" placeholder="ej: repuestoslopez">
                        <span class="inline-flex items-center rounded-r-md border border-l-0 border-slate-300 bg-slate-100 px-3 text-slate-500 text-sm font-mono">.krecewm.local</span>
                    </div>
                </div>
                <div>
                    <label for="custom_domain" class="block text-sm font-medium text-slate-700">Dominio Personalizado (Opcional)</label>
                    <input type="text" name="custom_domain" id="custom_domain" value="{{ old('custom_domain') }}" class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: www.repuestoslopez.com">
                </div>
                <div>
                    <label for="plan_id" class="block text-sm font-medium text-slate-700">Plan de Suscripción</label>
                    <select name="plan_id" id="plan_id" required class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }} - ${{ $plan->price }}/mes</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-slate-700">Correo del Negocio</label>
                    <input type="email" name="contact_email" id="contact_email" required value="{{ old('contact_email') }}" class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500" placeholder="contacto@empresa.com">
                </div>
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-slate-700">Teléfono del Negocio</label>
                    <input type="text" name="contact_phone" id="contact_phone" required value="{{ old('contact_phone') }}" class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: +541199998888">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div>
                    <label for="primary_color" class="block text-sm font-medium text-slate-700">Color Primario Corporativo</label>
                    <div class="mt-1 flex items-center space-x-2">
                        <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', '#1e293b') }}" class="h-10 w-12 rounded border border-slate-300 p-0.5 cursor-pointer">
                        <span class="text-xs text-slate-500">Este color se usará en botones principales y barra superior.</span>
                    </div>
                </div>
                <div>
                    <label for="secondary_color" class="block text-sm font-medium text-slate-700">Color Secundario Corporativo</label>
                    <div class="mt-1 flex items-center space-x-2">
                        <input type="color" name="secondary_color" id="secondary_color" value="{{ old('secondary_color', '#f59e0b') }}" class="h-10 w-12 rounded border border-slate-300 p-0.5 cursor-pointer">
                        <span class="text-xs text-slate-500">Este color se usará para acentos y elementos destacados.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 2: Cuenta del Dueño -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2">Cuenta Administrativa del Dueño</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <label for="owner_name" class="block text-sm font-medium text-slate-700">Nombre Completo</label>
                    <input type="text" name="owner_name" id="owner_name" required value="{{ old('owner_name') }}" class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: Carlos López">
                </div>
                <div class="md:col-span-1">
                    <label for="owner_email" class="block text-sm font-medium text-slate-700">Correo de Acceso (Login)</label>
                    <input type="email" name="owner_email" id="owner_email" required value="{{ old('owner_email') }}" class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500" placeholder="owner@empresa.com">
                </div>
                <div class="md:col-span-1">
                    <label for="owner_password" class="block text-sm font-medium text-slate-700">Contraseña inicial</label>
                    <input type="password" name="owner_password" id="owner_password" required class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500" placeholder="Mínimo 8 caracteres">
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
            <a href="{{ route('superadmin.tenants.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-lg border border-slate-200 transition-all duration-200">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 shadow-md hover:shadow-blue-500/20 rounded-lg transition-all duration-200">
                Crear e Inicializar Negocio
            </button>
        </div>
    </form>
</div>
@endsection
