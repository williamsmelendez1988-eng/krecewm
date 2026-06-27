@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('tenant.admin.orders.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div class="flex-1">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Detalle de Pedido</h2>
            <p class="text-sm text-slate-500">Orden {{ $order->order_number }} &middot; Creado el {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        {{-- Botón Descargar PDF --}}
        <a href="{{ route('tenant.admin.orders.invoice', $order->id) }}"
           target="_blank"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
            <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Descargar PDF
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Productos del Pedido & Cambios de Estado --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Tabla de Productos --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900">Artículos Comprados</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-500">
                        <thead class="bg-slate-50 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Producto</th>
                                <th class="px-6 py-4 text-center">Cantidad</th>
                                <th class="px-6 py-4 text-right">Precio Unitario</th>
                                <th class="px-6 py-4 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <span class="block font-semibold text-slate-900">{{ $item->product_name }}</span>
                                        <span class="block text-slate-400 text-xs font-mono">SKU: {{ $item->sku }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-slate-800">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-right font-medium text-slate-700">${{ number_format($item->price, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-slate-900">${{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-50/50">
                                <td colspan="3" class="px-6 py-4 font-bold text-slate-800 text-right">Subtotal:</td>
                                <td class="px-6 py-4 font-bold text-slate-950 text-right">${{ number_format($order->total, 2) }}</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td colspan="3" class="px-6 py-4 font-extrabold text-slate-900 text-right">Total:</td>
                                <td class="px-6 py-4 font-extrabold text-tenant-primary text-lg text-right">${{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Cambio de Estado --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-4">Actualizar Estado del Pedido</h3>
                
                <form action="{{ route('tenant.admin.orders.status', $order->id) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Estado</label>
                            <select id="status" name="status" required
                                class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pendiente (Esperando confirmación)</option>
                                <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmado (Descuenta Stock)</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Enviado</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Entregado</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelado (Devuelve Stock)</option>
                            </select>
                        </div>

                        <div>
                            <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Comentarios de Administración</label>
                            <textarea id="notes" name="notes" rows="1" placeholder="Ej. Pago verificado por transferencia..."
                                class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">{{ $order->notes }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-tenant-primary bg-tenant-primary-hover px-5 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-200">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Información del Cliente, Envío y Botón de WhatsApp --}}
        <div class="space-y-6">
            {{-- Datos de Envío --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-6 space-y-6">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Cliente / Comprador</h3>
                    <div class="space-y-2 text-sm text-slate-700">
                        @if($order->customer)
                            <a href="{{ route('tenant.admin.customers.show', $order->customer->id) }}" class="font-bold text-slate-900 hover:text-tenant-primary hover:underline block">
                                {{ $order->shipping_name }}
                            </a>
                        @else
                            <div class="font-bold text-slate-900">{{ $order->shipping_name }} (Comprador Invitado)</div>
                        @endif
                        <div><span class="text-slate-400 font-medium">Teléfono:</span> {{ $order->shipping_phone }}</div>
                        @if($order->customer && $order->customer->email)
                            <div><span class="text-slate-400 font-medium">Email:</span> {{ $order->customer->email }}</div>
                        @endif
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Dirección de Entrega</h3>
                    <div class="space-y-2 text-sm text-slate-700">
                        <div><span class="text-slate-400 font-medium">Dirección:</span> {{ $order->shipping_address ?: '-' }}</div>
                        <div><span class="text-slate-400 font-medium">Ciudad:</span> {{ $order->shipping_city ?: '-' }}</div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Detalle de Pago</h3>
                    <div class="space-y-2 text-sm text-slate-700">
                        <div><span class="text-slate-400 font-medium">Método:</span> <span class="capitalize">{{ $order->payment_method === 'whatsapp' ? 'Pedido WhatsApp' : 'Transferencia Bancaria' }}</span></div>
                        <div><span class="text-slate-400 font-medium">Estado Pago:</span> <span class="font-bold uppercase text-xs">{{ $order->payment_status }}</span></div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4 space-y-3">
                    <a href="{{ route('tenant.admin.orders.whatsapp', $order->id) }}" target="_blank"
                        class="w-full flex items-center justify-center px-4 py-3 rounded-xl bg-green-500 hover:bg-green-400 text-sm font-bold text-white transition-all duration-200 shadow-md gap-2">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.374 0 0 5.373 0 12c0 2.117.549 4.099 1.508 5.819L0 24l6.335-1.652A11.962 11.962 0 0012 24c6.626 0 12-5.373 12-12S18.626 0 12 0zm0 22c-1.885 0-3.653-.49-5.192-1.349L2.9 21.89l1.272-3.791A9.972 9.972 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" fill-rule="evenodd" clip-rule="evenodd"/>
                        </svg>
                        Contactar Cliente
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
