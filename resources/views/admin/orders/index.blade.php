@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900 font-Outfit">Pedidos y Ventas</h2>
        <p class="text-sm text-slate-500">Consulta y gestiona las solicitudes de compras de tus clientes.</p>
    </div>

    {{-- Filtros y Búsqueda --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4">
        <form action="{{ route('tenant.admin.orders.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
            {{-- Input de búsqueda --}}
            <div class="relative w-full sm:flex-grow">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Buscar por Nº Pedido, Cliente o Teléfono..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
            </div>

            {{-- Selector de Estado --}}
            <div class="w-full sm:w-[200px]">
                <select name="status" onchange="this.form.submit()"
                    class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                    <option value="">Todos los Estados</option>
                    <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmed" {{ ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmado (Descuenta Stock)</option>
                    <option value="shipped" {{ ($filters['status'] ?? '') === 'shipped' ? 'selected' : '' }}>Enviado</option>
                    <option value="delivered" {{ ($filters['status'] ?? '') === 'delivered' ? 'selected' : '' }}>Entregado</option>
                    <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>

            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-tenant-primary bg-tenant-primary-hover px-5 py-2 text-sm font-semibold text-white shadow-md transition-all duration-200">
                Filtrar
            </button>
            @if(!empty($filters['search']) || !empty($filters['status']))
                <a href="{{ route('tenant.admin.orders.index') }}" class="w-full sm:w-auto text-center px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    {{-- Tabla de Pedidos --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-500">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Nº Pedido</th>
                        <th class="px-6 py-4">Cliente / Teléfono</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Método Pago</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4">Fecha</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('tenant.admin.orders.show', $order->id) }}" class="font-bold text-slate-900 hover:text-tenant-primary transition-colors">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $order->shipping_name }}</div>
                                <div class="text-xs text-slate-400 font-medium">{{ $order->shipping_phone }}</div>
                            </td>
                            <td class="px-6 py-4 font-extrabold text-slate-800">${{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="capitalize text-slate-600">{{ $order->payment_method === 'whatsapp' ? 'Pedido WhatsApp' : 'Transferencia' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold capitalize ring-1 ring-inset 
                                    {{ $order->status === 'pending' ? 'bg-amber-50 text-amber-700 ring-amber-600/20' : '' }}
                                    {{ $order->status === 'confirmed' ? 'bg-blue-50 text-blue-700 ring-blue-600/20' : '' }}
                                    {{ $order->status === 'shipped' ? 'bg-indigo-50 text-indigo-700 ring-indigo-600/20' : '' }}
                                    {{ $order->status === 'delivered' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : '' }}
                                    {{ $order->status === 'cancelled' ? 'bg-rose-50 text-rose-700 ring-rose-600/20' : '' }}
                                ">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-400">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-right text-xs font-semibold space-x-2">
                                <a href="{{ route('tenant.admin.orders.show', $order->id) }}" class="text-tenant-primary hover:underline">Ver detalle</a>
                                <a href="{{ route('tenant.admin.orders.whatsapp', $order->id) }}" target="_blank" class="text-green-600 hover:underline">WhatsApp</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium">No se encontraron pedidos coincidentes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
