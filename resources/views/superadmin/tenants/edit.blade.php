@extends('layouts.superadmin')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Editar Negocio: {{ $tenant->name }}</h2>
        <p class="text-sm text-slate-500">Actualiza las configuraciones globales, dominio, plan de suscripción o estado del negocio.</p>
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

    <form method="POST" action="{{ route('superadmin.tenants.update', $tenant) }}" class="space-y-8 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2">Configuración del Sistema</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Nombre Comercial</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $tenant->name) }}" class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="subdomain" class="block text-sm font-medium text-slate-700">Subdominio</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="text" name="subdomain" id="subdomain" required value="{{ old('subdomain', $tenant->subdomain) }}" class="block w-full min-w-0 flex-1 rounded-l-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500">
                        <span class="inline-flex items-center rounded-r-md border border-l-0 border-slate-300 bg-slate-100 px-3 text-slate-500 text-sm font-mono">.krecewm.local</span>
                    </div>
                </div>
                <div>
                    <label for="custom_domain" class="block text-sm font-medium text-slate-700">Dominio Personalizado</label>
                    <input type="text" name="custom_domain" id="custom_domain" value="{{ old('custom_domain', $tenant->custom_domain) }}" class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: www.repuestoslopez.com">
                </div>
                <div>
                    <label for="plan_id" class="block text-sm font-medium text-slate-700">Plan de Suscripción</label>
                    <select name="plan_id" id="plan_id" required class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id', $tenant->plan_id) == $plan->id ? 'selected' : '' }}>{{ $plan->name }} - ${{ $plan->price }}/mes</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-slate-700">Correo del Negocio</label>
                    <input type="email" name="contact_email" id="contact_email" required value="{{ old('contact_email', $tenant->contact_email) }}" class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-slate-700">Teléfono del Negocio</label>
                    <input type="text" name="contact_phone" id="contact_phone" required value="{{ old('contact_phone', $tenant->contact_phone) }}" class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700">Estado del Negocio</label>
                    <select name="status" id="status" required class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 py-2 px-3 text-sm border focus:ring-blue-500 focus:border-blue-500">
                        <option value="active" {{ old('status', $tenant->status) == 'active' ? 'selected' : '' }}>Activo (Acceso completo)</option>
                        <option value="suspended" {{ old('status', $tenant->status) == 'suspended' ? 'selected' : '' }}>Suspendido (Acceso denegado)</option>
                        <option value="trial" {{ old('status', $tenant->status) == 'trial' ? 'selected' : '' }}>Prueba (Trial limitado)</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div>
                    <label for="primary_color" class="block text-sm font-medium text-slate-700">Color Primario Corporativo</label>
                    <div class="mt-1 flex items-center space-x-2">
                        <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', $tenant->primary_color) }}" class="h-10 w-12 rounded border border-slate-300 p-0.5 cursor-pointer">
                        <span class="text-xs text-slate-500">Usado en botones y fondos primarios.</span>
                    </div>
                </div>
                <div>
                    <label for="secondary_color" class="block text-sm font-medium text-slate-700">Color Secundario Corporativo</label>
                    <div class="mt-1 flex items-center space-x-2">
                        <input type="color" name="secondary_color" id="secondary_color" value="{{ old('secondary_color', $tenant->secondary_color) }}" class="h-10 w-12 rounded border border-slate-300 p-0.5 cursor-pointer">
                        <span class="text-xs text-slate-500">Usado para llamados a la acción y bordes destacados.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
            <a href="{{ route('superadmin.tenants.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-lg border border-slate-200 transition-all duration-200">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 shadow-md hover:shadow-blue-500/20 rounded-lg transition-all duration-200">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
