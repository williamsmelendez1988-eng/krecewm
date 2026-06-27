@extends('layouts.superadmin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Gestión de Negocios</h2>
            <p class="text-sm text-slate-500">Crea, edita, suspende o activa las tiendas de la plataforma KreceWM.</p>
        </div>
        <a href="{{ route('superadmin.tenants.create') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-all duration-200">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Crear Negocio
        </a>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-500">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Negocio</th>
                        <th class="px-6 py-4">Subdominio / Dominio</th>
                        <th class="px-6 py-4">Plan</th>
                        <th class="px-6 py-4">Contacto</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-slate-50/50 transition-all duration-150">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                <span>{{ $tenant->name }}</span>
                                <span class="block text-slate-400 font-normal text-xs">ID: {{ $tenant->id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="http://{{ $tenant->subdomain }}.krecewm.local:8000" target="_blank" class="block text-blue-600 hover:underline font-mono text-xs font-medium">{{ $tenant->subdomain }}.krecewm.local</a>
                                @if($tenant->custom_domain)
                                    <a href="http://{{ $tenant->custom_domain }}:8000" target="_blank" class="block text-slate-500 hover:underline font-mono text-[10px] mt-0.5">{{ $tenant->custom_domain }}</a>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                    {{ $tenant->plan->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="block text-slate-800 text-xs">{{ $tenant->contact_email }}</span>
                                <span class="block text-slate-400 text-xs mt-0.5">{{ $tenant->contact_phone }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($tenant->status === 'active')
                                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 capitalize">Activo</span>
                                @elseif($tenant->status === 'suspended')
                                    <span class="inline-flex items-center rounded-md bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/20 capitalize">Suspendido</span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20 capitalize">Prueba</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-medium space-x-3 flex items-center">
                                <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="text-blue-600 hover:text-blue-500 transition-all">Editar</a>
                                
                                <form method="POST" action="{{ route('superadmin.tenants.toggle', $tenant) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($tenant->status === 'active')
                                        <button type="submit" class="text-rose-600 hover:text-rose-500 transition-all">Suspender</button>
                                    @else
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-500 transition-all">Activar</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-medium">No hay negocios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tenants->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
