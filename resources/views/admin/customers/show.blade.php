@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('tenant.admin.customers.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Ficha del Cliente</h2>
            <p class="text-sm text-slate-500">Historial y datos del comprador.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Datos de Perfil --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-6 self-start space-y-6">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Información Básica</h3>
                <div class="space-y-4">
                    <div>
                        <span class="block text-xs font-medium text-slate-400">Nombre</span>
                        <span class="text-base font-bold text-slate-800">{{ $customer->name }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-slate-400">Teléfono (WhatsApp)</span>
                        <span class="text-sm font-semibold text-slate-800">{{ $customer->phone }}</span>
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $customer->phone) }}" target="_blank" class="mt-1 flex items-center text-xs text-green-600 hover:text-green-500 font-semibold gap-1">
                            Enviar WhatsApp direct
                        </a>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-slate-400">Email</span>
                        <span class="text-sm font-semibold text-slate-800 font-mono">{{ $customer->email ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Dirección de Envío</h3>
                <div class="space-y-3">
                    <div>
                        <span class="block text-xs font-medium text-slate-400">Dirección</span>
                        <span class="text-sm font-semibold text-slate-800">{{ $customer->address ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-slate-400">Ciudad</span>
                        <span class="text-sm font-semibold text-slate-800">{{ $customer->city ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-between">
                <div>
                    <span class="block text-xs font-medium text-slate-400">Miembro desde</span>
                    <span class="text-xs font-semibold text-slate-700">{{ $customer->created_at->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-slate-400">Estado</span>
                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $customer->status === 'active' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-slate-50 text-slate-600 ring-slate-500/20' }}">
                        {{ $customer->status === 'active' ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 flex gap-2">
                <a href="{{ route('tenant.admin.customers.edit', $customer->id) }}" class="flex-1 text-center py-2 rounded-lg border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                    Editar Datos
                </a>
            </div>
        </div>

        {{-- Historial de Pedidos --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900">Historial de Pedidos</h3>
                    <span class="text-xs text-slate-500 font-semibold">{{ $orders->total() }} pedidos en total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-500">
                        <thead class="bg-slate-50 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Nº Pedido</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4">Método Pago</th>
                                <th class="px-6 py-4">Estado</th>
                                <th class="px-6 py-4">Fecha</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-800">${{ number_format($order->total, 2) }}</td>
                                    <td class="px-6 py-4 capitalize">{{ $order->payment_method === 'whatsapp' ? 'Pedido WhatsApp' : 'Transferencia' }}</td>
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
                                    <td class="px-6 py-4 text-xs">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 text-right text-xs font-semibold">
                                        <a href="{{ route('tenant.admin.orders.show', $order->id) }}" class="text-tenant-primary hover:underline">Ver detalle</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-medium">Este cliente aún no registra pedidos.</td>
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
    </div>
</div>
@endsection
