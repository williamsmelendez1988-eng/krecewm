@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="dashboardCharts()">

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Panel de Control</h2>
            <p class="text-sm text-slate-500 mt-0.5">
                <span class="font-medium text-slate-700">{{ $tenant->name }}</span> · Datos en tiempo real
            </p>
        </div>
        <a href="/" target="_blank"
           class="inline-flex items-center gap-2 rounded-lg bg-tenant-primary bg-tenant-primary-hover px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-200">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Ver Tienda Online
        </a>
    </div>

    {{-- ── KPI Cards de Ventas ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

        {{-- Hoy --}}
        <div class="col-span-1 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-indigo-200">Hoy</p>
                <svg class="h-5 w-5 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="mt-3 text-2xl font-bold leading-tight">${{ number_format($metrics['sales_today'], 2) }}</p>
            <p class="mt-1 text-xs text-indigo-200">Ventas del día</p>
        </div>

        {{-- Esta Semana --}}
        <div class="col-span-1 rounded-2xl bg-gradient-to-br from-violet-500 to-violet-700 p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-violet-200">Semana</p>
                <svg class="h-5 w-5 text-violet-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="mt-3 text-2xl font-bold leading-tight">${{ number_format($metrics['sales_week'], 2) }}</p>
            <p class="mt-1 text-xs text-violet-200">Ventas esta semana</p>
        </div>

        {{-- Este Mes --}}
        <div class="col-span-1 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-200">Mes</p>
                @if($metrics['month_growth'] >= 0)
                    <span class="flex items-center gap-1 text-xs font-bold text-emerald-100 bg-emerald-600/50 rounded-full px-2 py-0.5">
                        ↑ {{ $metrics['month_growth'] }}%
                    </span>
                @else
                    <span class="flex items-center gap-1 text-xs font-bold text-red-100 bg-red-600/50 rounded-full px-2 py-0.5">
                        ↓ {{ abs($metrics['month_growth']) }}%
                    </span>
                @endif
            </div>
            <p class="mt-3 text-2xl font-bold leading-tight">${{ number_format($metrics['sales_month'], 2) }}</p>
            <p class="mt-1 text-xs text-emerald-200">vs mes anterior</p>
        </div>

        {{-- Pedidos Pendientes --}}
        <div class="col-span-1 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-amber-200">Pendientes</p>
                @if($metrics['pending_orders'] > 0)
                    <span class="h-5 w-5 rounded-full bg-white text-amber-700 text-xs font-bold flex items-center justify-center">
                        {{ $metrics['pending_orders'] }}
                    </span>
                @endif
            </div>
            <p class="mt-3 text-2xl font-bold leading-tight">{{ $metrics['pending_orders'] }}</p>
            <p class="mt-1 text-xs text-amber-200">pedidos por atender</p>
        </div>
    </div>

    {{-- ── Métricas Generales ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-xl bg-white border border-slate-200/80 p-5 shadow-sm flex items-center gap-4">
            <div class="rounded-lg bg-slate-100 p-2.5 text-slate-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <p class="text-xs text-slate-500">Productos</p>
                <p class="text-xl font-bold text-slate-900">{{ $metrics['total_products'] }}</p>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200/80 p-5 shadow-sm flex items-center gap-4">
            <div class="rounded-lg bg-blue-50 p-2.5 text-blue-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-slate-500">Clientes</p>
                <p class="text-xl font-bold text-slate-900">{{ $metrics['total_customers'] }}</p>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200/80 p-5 shadow-sm flex items-center gap-4">
            <div class="rounded-lg bg-purple-50 p-2.5 text-purple-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="text-xs text-slate-500">Total Pedidos</p>
                <p class="text-xl font-bold text-slate-900">{{ $metrics['total_orders'] }}</p>
            </div>
        </div>
        <a href="{{ route('tenant.admin.inventory.index') }}"
           class="rounded-xl {{ $metrics['low_stock_count'] > 0 ? 'bg-rose-50 border-rose-200' : 'bg-white border-slate-200/80' }} border p-5 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="rounded-lg {{ $metrics['low_stock_count'] > 0 ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-600' }} p-2.5">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="text-xs {{ $metrics['low_stock_count'] > 0 ? 'text-rose-500' : 'text-slate-500' }}">Stock Crítico</p>
                <p class="text-xl font-bold {{ $metrics['low_stock_count'] > 0 ? 'text-rose-700' : 'text-slate-900' }}">{{ $metrics['low_stock_count'] }}</p>
            </div>
        </a>
    </div>

    {{-- ── Gráficas ─────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Gráfica de barras: Últimos 7 días --}}
        <div class="lg:col-span-2 rounded-2xl bg-white border border-slate-200/80 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Ventas Últimos 7 Días</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Solo pedidos confirmados / entregados</p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span> USD
                </span>
            </div>
            <div class="h-56">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Gráfica de dona: por estado --}}
        <div class="rounded-2xl bg-white border border-slate-200/80 p-6 shadow-sm">
            <h3 class="text-base font-semibold text-slate-900 mb-6">Pedidos por Estado</h3>
            <div class="h-44 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
            {{-- Leyenda manual --}}
            <div class="mt-4 space-y-1.5">
                @php
                    $statusColors = [
                        'pending'    => ['bg-amber-400',  'Pendiente'],
                        'confirmed'  => ['bg-blue-500',   'Confirmado'],
                        'processing' => ['bg-violet-500', 'Procesando'],
                        'shipped'    => ['bg-cyan-500',   'Enviado'],
                        'delivered'  => ['bg-emerald-500','Entregado'],
                        'cancelled'  => ['bg-rose-400',   'Cancelado'],
                    ];
                @endphp
                @foreach($ordersByStatus as $status => $count)
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5 text-slate-600">
                            <span class="h-2.5 w-2.5 rounded-full {{ $statusColors[$status][0] ?? 'bg-slate-400' }}"></span>
                            {{ $statusColors[$status][1] ?? ucfirst($status) }}
                        </span>
                        <span class="font-semibold text-slate-900">{{ $count }}</span>
                    </div>
                @endforeach
                @if(empty($ordersByStatus))
                    <p class="text-xs text-slate-400 text-center py-2">Sin pedidos aún</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Top Productos + Últimos Pedidos ─────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top 5 Productos del Mes --}}
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">🏆 Top Productos del Mes</h3>
                <a href="{{ route('tenant.admin.reports.index') }}" class="text-xs text-indigo-600 hover:underline font-medium">Ver reportes →</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($topProducts as $index => $item)
                    <div class="px-6 py-3.5 flex items-center gap-4">
                        <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold
                            {{ $index === 0 ? 'bg-amber-100 text-amber-700' : ($index === 1 ? 'bg-slate-100 text-slate-600' : 'bg-orange-50 text-orange-600') }}">
                            #{{ $index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $item->product->name ?? 'Producto eliminado' }}</p>
                            <p class="text-xs text-slate-400">{{ $item->product->sku ?? '' }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-slate-900">{{ $item->total_sold }} uds.</p>
                            <p class="text-xs text-emerald-600 font-medium">${{ number_format($item->revenue, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-slate-400">Sin ventas este mes aún.</div>
                @endforelse
            </div>
        </div>

        {{-- Últimos 5 Pedidos --}}
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">🛒 Últimos Pedidos</h3>
                <a href="{{ route('tenant.admin.orders.index') }}" class="text-xs text-indigo-600 hover:underline font-medium">Ver todos →</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($latestOrders as $order)
                    <a href="{{ route('tenant.admin.orders.show', $order) }}"
                       class="flex items-center gap-4 px-6 py-3.5 hover:bg-slate-50/70 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $order->shipping_name }}</p>
                            <p class="text-xs text-slate-400">{{ $order->order_number }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-slate-900">${{ number_format($order->total, 2) }}</p>
                            @php
                                $statusBadge = [
                                    'pending'    => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                    'confirmed'  => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                    'processing' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
                                    'shipped'    => 'bg-cyan-50 text-cyan-700 ring-cyan-600/20',
                                    'delivered'  => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                    'cancelled'  => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                                ][$order->status] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20';
                            @endphp
                            <span class="inline-flex items-center rounded-md {{ $statusBadge }} px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset capitalize mt-1">
                                {{ $order->status }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-slate-400">Aún no hay pedidos registrados.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Alertas de Stock Bajo ────────────────────────────────────────── --}}
    @if($lowStockItems->count() > 0)
    <div class="rounded-2xl bg-rose-50 border border-rose-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-rose-200 flex items-center gap-3">
            <svg class="h-5 w-5 text-rose-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h3 class="text-sm font-semibold text-rose-800">⚠️ Productos con Stock Crítico</h3>
            <span class="ml-auto text-xs font-semibold text-rose-700 bg-rose-200 rounded-full px-2 py-0.5">{{ $metrics['low_stock_count'] }} alerta(s)</span>
        </div>
        <div class="divide-y divide-rose-100">
            @foreach($lowStockItems as $inv)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-rose-900">{{ $inv->product->name ?? 'Producto' }}</p>
                        <p class="text-xs text-rose-500">SKU: {{ $inv->product->sku ?? '—' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-rose-700">{{ $inv->quantity }} disponibles</p>
                        <p class="text-xs text-rose-400">Mínimo: {{ $inv->min_stock }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="px-6 py-3 bg-rose-100/60 border-t border-rose-200">
            <a href="{{ route('tenant.admin.inventory.index') }}" class="text-xs font-semibold text-rose-700 hover:underline">
                → Ir al módulo de inventario
            </a>
        </div>
    </div>
    @endif

</div>

{{-- ── Chart.js CDN + Script de gráficas ───────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@push('scripts')
<script>
function dashboardCharts() {
    return {
        init() {
            this.renderSalesChart();
            this.renderStatusChart();
        },

        renderSalesChart() {
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;

            const labels = @json($chartLabels);
            const data   = @json($chartData);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ventas USD',
                        data: data,
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 0,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => `$${ctx.raw.toFixed(2)}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#94a3b8' }
                        },
                        y: {
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                font: { size: 11 },
                                color: '#94a3b8',
                                callback: v => `$${v}`
                            }
                        }
                    }
                }
            });
        },

        renderStatusChart() {
            const ctx = document.getElementById('statusChart');
            if (!ctx) return;

            const rawData = @json($ordersByStatus);
            const labels  = Object.keys(rawData).map(s => s.charAt(0).toUpperCase() + s.slice(1));
            const data    = Object.values(rawData);
            const colors  = ['#f59e0b','#3b82f6','#8b5cf6','#06b6d4','#10b981','#f43f5e'];

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data.length ? data : [1],
                        backgroundColor: data.length ? colors.slice(0, data.length) : ['#e2e8f0'],
                        borderWidth: 0,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` ${ctx.raw} pedido(s)`
                            }
                        }
                    }
                }
            });
        }
    }
}
</script>
@endpush
@endsection
