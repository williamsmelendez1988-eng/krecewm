@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 font-sans">Control de Stock e Inventario</h2>
            <p class="text-sm text-slate-500">Supervisa las existencias físicas de tus productos y gestiona su ubicación en tienda.</p>
        </div>
        <a href="{{ route('tenant.admin.inventory.movements') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 shadow rounded-lg hover:bg-slate-50 transition-all duration-200">
            Ver Historial (Kárdex) &rarr;
        </a>
    </div>

    {{-- Filtros Rápidos --}}
    <form method="GET" action="{{ route('tenant.admin.inventory.index') }}" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Buscador --}}
        <div>
            <label for="search" class="block text-xs font-semibold text-slate-500 uppercase">Buscar Producto</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nombre o SKU..."
                class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2 px-3 text-sm focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
        </div>

        {{-- Ubicación --}}
        <div>
            <label for="location" class="block text-xs font-semibold text-slate-500 uppercase">Ubicación Física</label>
            <input type="text" name="location" id="location" value="{{ request('location') }}" placeholder="Pasillo, estante..."
                class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2 px-3 text-sm focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
        </div>

        {{-- Alerta Stock --}}
        <div>
            <label for="stock_status" class="block text-xs font-semibold text-slate-500 uppercase">Estado de Stock</label>
            <select name="stock_status" id="stock_status" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2 px-3 text-sm focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
                <option value="">Todos</option>
                <option value="normal" {{ request('stock_status') === 'normal' ? 'selected' : '' }}>Stock Normal</option>
                <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Bajo Stock Mínimo</option>
                <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Sin Existencias</option>
            </select>
        </div>

        {{-- Botones --}}
        <div class="flex items-end space-x-2">
            <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow rounded-lg transition-all">
                Filtrar
            </button>
            <a href="{{ route('tenant.admin.inventory.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50" title="Limpiar">
                &times;
            </a>
        </div>
    </form>

    {{-- Tabla de Inventario --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Código / SKU</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Producto</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Ubicación Física</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Alerta Mínimo</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Stock Actual</th>
                    <th scope="col" class="relative px-6 py-3.5">
                        <span class="sr-only">Ajustes</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($inventories as $inventory)
                    <tr class="hover:bg-slate-50 transition-colors">
                        {{-- SKU --}}
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-mono text-slate-700 font-medium">
                            {{ $inventory->product->sku }}
                        </td>

                        {{-- Producto --}}
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-slate-800 block leading-tight">{{ $inventory->product->name }}</span>
                            <span class="text-xs text-slate-400 block mt-0.5">{{ $inventory->product->category->name }}</span>
                        </td>

                        {{-- Ubicación --}}
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">
                            {{ $inventory->location ?: 'Sin asignar' }}
                        </td>

                        {{-- Alerta Mínimo --}}
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500 font-mono">
                            {{ $inventory->min_stock }} unidades
                        </td>

                        {{-- Stock Actual --}}
                        <td class="whitespace-nowrap px-6 py-4">
                            @php
                                $qty = $inventory->quantity;
                                $min = $inventory->min_stock;
                            @endphp
                            <div class="flex items-center space-x-2">
                                <span class="text-base font-bold {{ $qty <= 0 ? 'text-rose-600' : ($qty <= $min ? 'text-amber-500' : 'text-slate-800') }}">
                                    {{ $qty }} uds
                                </span>
                                @if($qty <= 0)
                                    <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-100 text-[10px] font-semibold">Agotado</span>
                                @elseif($qty <= $min)
                                    <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-100 text-[10px] font-semibold">Crítico</span>
                                @endif
                            </div>
                        </td>

                        {{-- Ajuste --}}
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <a href="{{ route('tenant.admin.inventory.adjust', $inventory->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-900 rounded-md shadow-sm transition-colors">
                                Ajustar Stock
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-4 text-sm font-medium text-slate-900">No hay existencias</h3>
                            <p class="mt-1 text-sm text-slate-500">Crea productos para inicializar los registros de stock físico.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($inventories->hasPages())
        <div class="mt-4">
            {{ $inventories->links() }}
        </div>
    @endif
</div>
@endsection
