@extends('layouts.superadmin')

@section('content')
<div class="space-y-8">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard General</h2>
        <p class="text-sm text-slate-500">Métricas agregadas e información general del rendimiento de KreceWM.</p>
    </div>

    <!-- Grid de métricas -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Negocios -->
        <div class="overflow-hidden rounded-xl bg-white p-6 shadow-sm border border-slate-200/80 flex items-center">
            <div class="rounded-lg bg-blue-50 p-3 text-blue-600 mr-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 truncate">Total Negocios</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $metrics['total_tenants'] }}</p>
            </div>
        </div>

        <!-- Negocios Activos -->
        <div class="overflow-hidden rounded-xl bg-white p-6 shadow-sm border border-slate-200/80 flex items-center">
            <div class="rounded-lg bg-emerald-50 p-3 text-emerald-600 mr-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 truncate">Negocios Activos</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $metrics['active_tenants'] }}</p>
            </div>
        </div>

        <!-- En Periodo de Prueba -->
        <div class="overflow-hidden rounded-xl bg-white p-6 shadow-sm border border-slate-200/80 flex items-center">
            <div class="rounded-lg bg-amber-50 p-3 text-amber-600 mr-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 truncate">Periodo de Prueba</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $metrics['trial_tenants'] }}</p>
            </div>
        </div>

        <!-- Negocios Suspendidos -->
        <div class="overflow-hidden rounded-xl bg-white p-6 shadow-sm border border-slate-200/80 flex items-center">
            <div class="rounded-lg bg-rose-50 p-3 text-rose-600 mr-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 truncate">Suspendidos</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $metrics['suspended_tenants'] }}</p>
            </div>
        </div>
    </div>

    <!-- Métricas Globales del E-commerce -->
    <div class="space-y-4">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Actividad Transaccional Global</h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Total Ventas Globales -->
            <div class="overflow-hidden rounded-xl bg-white p-6 shadow-sm border border-slate-200/80 flex items-center">
                <div class="rounded-lg bg-violet-50 p-3 text-violet-600 mr-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 8H7m5 8h5" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 truncate">Ventas Totales (USD)</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">${{ number_format($metrics['total_revenue'], 2) }}</p>
                </div>
            </div>

            <!-- Total Pedidos Globales -->
            <div class="overflow-hidden rounded-xl bg-white p-6 shadow-sm border border-slate-200/80 flex items-center">
                <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600 mr-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 truncate">Pedidos Totales</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($metrics['total_orders']) }}</p>
                </div>
            </div>

            <!-- Total Productos Globales -->
            <div class="overflow-hidden rounded-xl bg-white p-6 shadow-sm border border-slate-200/80 flex items-center">
                <div class="rounded-lg bg-teal-50 p-3 text-teal-600 mr-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 truncate">Productos en Catálogos</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($metrics['total_products']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grids de Tablas -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- Lista de últimos negocios -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Últimos Negocios Registrados</h3>
                <a href="{{ route('superadmin.tenants.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-500">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-500">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Negocio</th>
                            <th class="px-6 py-4">Subdominio</th>
                            <th class="px-6 py-4">Plan</th>
                            <th class="px-6 py-4">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($latestTenants as $tenant)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $tenant->name }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-600">{{ $tenant->subdomain }}.krecewm.local</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                        {{ $tenant->plan->name }}
                                    </span>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400 font-medium">No hay negocios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Negocios por Ventas -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Top Negocios por Ventas</h3>
                <span class="text-xs text-slate-400 font-medium font-semibold">Ventas Concluidas (USD)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-500">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Negocio</th>
                            <th class="px-6 py-4">Pedidos Confirmados</th>
                            <th class="px-6 py-4 text-right">Total Facturado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($topTenants as $tenant)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $tenant->name }}</td>
                                <td class="px-6 py-4 font-medium text-slate-650">
                                    {{ $tenant->orders_count ?? 0 }} pedido(s)
                                </td>
                                <td class="px-6 py-4 text-right font-black text-slate-900">
                                    ${{ number_format($tenant->orders_sum_total ?? 0, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-slate-400 font-medium">No hay registros de ventas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
