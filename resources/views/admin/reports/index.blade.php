@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Reportes & Analytics</h2>
            <p class="text-sm text-slate-500 mt-1">Métricas de rendimiento de tu negocio.</p>
        </div>

        {{-- Selector de Período + Exportar --}}
        <div class="flex items-center gap-3 flex-wrap">
            <form method="GET" action="{{ route('tenant.admin.reports.index') }}" class="flex gap-2 items-center">
                <select name="period" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-200 bg-white py-2 pl-3 pr-8 text-sm text-slate-700 focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary shadow-sm">
                    <option value="today"  {{ $period === 'today'  ? 'selected' : '' }}>Hoy</option>
                    <option value="week"   {{ $period === 'week'   ? 'selected' : '' }}>Esta semana</option>
                    <option value="month"  {{ $period === 'month'  ? 'selected' : '' }}>Este mes</option>
                    <option value="year"   {{ $period === 'year'   ? 'selected' : '' }}>Este año</option>
                </select>
            </form>
            <a href="{{ route('tenant.admin.reports.export', ['period' => $period]) }}"
                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar CSV
            </a>
        </div>
    </div>

    {{-- KPIs principales --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Ingresos --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ingresos</span>
                <div class="h-9 w-9 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">${{ number_format($summary['revenue'], 2) }}</p>
            @if(!is_null($summary['revenueChange']))
                <p class="text-xs mt-1 font-semibold {{ $summary['revenueChange'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ $summary['revenueChange'] >= 0 ? '▲' : '▼' }} {{ abs($summary['revenueChange']) }}% vs período anterior
                </p>
            @endif
        </div>

        {{-- Pedidos --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pedidos</span>
                <div class="h-9 w-9 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">{{ $summary['count'] }}</p>
            <p class="text-xs mt-1 text-slate-400">Total en el período</p>
        </div>

        {{-- Ticket Promedio --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ticket Promedio</span>
                <div class="h-9 w-9 rounded-lg bg-violet-50 flex items-center justify-center text-violet-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">${{ number_format($summary['avgTicket'], 2) }}</p>
            <p class="text-xs mt-1 text-slate-400">Por pedido</p>
        </div>

        {{-- Clientes Nuevos --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Clientes Nuevos</span>
                <div class="h-9 w-9 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">{{ $customers['newCustomers'] }}</p>
            <p class="text-xs mt-1 text-slate-400">{{ $customers['totalCustomers'] }} clientes en total</p>
        </div>
    </div>

    {{-- Gráficas: Pedidos por estado + Ventas diarias --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Donut: Pedidos por estado --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Pedidos por Estado</h3>
            @php
                $statusColors = [
                    'pending'   => '#f59e0b',
                    'confirmed' => '#3b82f6',
                    'shipped'   => '#8b5cf6',
                    'delivered' => '#10b981',
                    'cancelled' => '#ef4444',
                ];
                $statusLabels = [
                    'pending'   => 'Pendiente',
                    'confirmed' => 'Confirmado',
                    'shipped'   => 'Enviado',
                    'delivered' => 'Entregado',
                    'cancelled' => 'Cancelado',
                ];
                $totalOrders = $ordersByStatus->sum('total') ?: 1;
            @endphp

            @if($ordersByStatus->count() > 0)
                {{-- SVG Donut Chart manual --}}
                <div class="flex items-center justify-center mb-4">
                    <div class="relative" style="width:140px;height:140px;">
                        <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                            @php $offset = 0; @endphp
                            @foreach($ordersByStatus as $row)
                                @php
                                    $pct = ($row->total / $totalOrders) * 100;
                                    $color = $statusColors[$row->status] ?? '#94a3b8';
                                @endphp
                                <circle cx="18" cy="18" r="15.915"
                                    fill="none"
                                    stroke="{{ $color }}"
                                    stroke-width="3.5"
                                    stroke-dasharray="{{ $pct }} {{ 100 - $pct }}"
                                    stroke-dashoffset="{{ 100 - $offset }}"
                                />
                                @php $offset += $pct; @endphp
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-xl font-extrabold text-slate-800">{{ $ordersByStatus->sum('total') }}</span>
                            <span class="text-[10px] text-slate-400 font-medium">pedidos</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach($ordersByStatus as $row)
                        @php $color = $statusColors[$row->status] ?? '#94a3b8'; @endphp
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full flex-shrink-0" style="background-color:{{ $color }}"></span>
                                <span class="text-slate-600">{{ $statusLabels[$row->status] ?? $row->status }}</span>
                            </div>
                            <span class="font-bold text-slate-800">{{ $row->total }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center justify-center h-40 text-slate-400 text-sm">Sin pedidos aún</div>
            @endif
        </div>

        {{-- Línea: Ventas diarias --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm lg:col-span-2">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Evolución de Ventas Diarias</h3>
            @if($dailySales->count() > 0)
                @php
                    $maxRevenue = $dailySales->max('revenue') ?: 1;
                    $dates = $dailySales->pluck('date')->toArray();
                    $revenues = $dailySales->pluck('revenue')->toArray();
                @endphp
                <div class="relative h-40 flex items-end gap-1 border-b border-slate-100 pb-1 overflow-hidden">
                    @foreach($dailySales as $day)
                        @php
                            $height = max(4, round(($day->revenue / $maxRevenue) * 100));
                        @endphp
                        <div class="flex-1 flex flex-col items-center group relative">
                            <div class="absolute bottom-0 left-0 right-0 rounded-t-sm transition-all duration-300 group-hover:opacity-80"
                                style="height:{{ $height }}%; background-color: var(--color-primary, #3b82f6); opacity:0.75">
                            </div>
                            <div class="absolute -top-7 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-all duration-200 bg-slate-900 text-white text-[9px] rounded px-1.5 py-0.5 whitespace-nowrap pointer-events-none z-10">
                                ${{ number_format($day->revenue, 0) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-[10px] text-slate-400">{{ $dailySales->first()->date }}</span>
                    <span class="text-[10px] text-slate-400">{{ $dailySales->last()->date }}</span>
                </div>
            @else
                <div class="flex items-center justify-center h-40 text-slate-400 text-sm">Sin datos de ventas para este período</div>
            @endif
        </div>
    </div>

    {{-- Top Productos --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Productos Más Vendidos</h3>
            <span class="text-xs text-slate-400">Por unidades vendidas</span>
        </div>
        @if($topProducts->count() > 0)
            <div class="divide-y divide-slate-50">
                @php $maxUnits = $topProducts->max('units_sold') ?: 1; @endphp
                @foreach($topProducts as $i => $product)
                    <div class="px-6 py-3 flex items-center gap-4">
                        <span class="text-sm font-bold text-slate-300 w-5 text-center">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $product->product_name }}</p>
                            <div class="mt-1 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-tenant-primary transition-all duration-500"
                                    style="width: {{ round(($product->units_sold / $maxUnits) * 100) }}%"></div>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-slate-800">{{ $product->units_sold }} <span class="text-slate-400 font-medium text-xs">uds.</span></p>
                            <p class="text-xs text-emerald-600 font-semibold">${{ number_format($product->revenue, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex items-center justify-center py-16 text-slate-400 text-sm">
                No hay productos vendidos en este período
            </div>
        @endif
    </div>

</div>
@endsection
