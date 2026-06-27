@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 font-sans">Historial de Inventario (Kárdex)</h2>
            <p class="text-sm text-slate-500">Consulta el registro de auditoría de todas las entradas y salidas de stock realizadas.</p>
        </div>
        <a href="{{ route('tenant.admin.inventory.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            &larr; Existencias Actuales
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('tenant.admin.inventory.movements') }}" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Búsqueda --}}
        <div>
            <label for="search" class="block text-xs font-semibold text-slate-500 uppercase">Buscar Producto</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nombre o SKU..."
                class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2 px-3 text-sm focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
        </div>

        {{-- Tipo --}}
        <div>
            <label for="type" class="block text-xs font-semibold text-slate-500 uppercase">Tipo</label>
            <select name="type" id="type" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2 px-3 text-sm focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
                <option value="">Todos</option>
                <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Entradas (+)</option>
                <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Salidas (-)</option>
            </select>
        </div>

        {{-- Motivo --}}
        <div>
            <label for="reason" class="block text-xs font-semibold text-slate-500 uppercase">Motivo</label>
            <select name="reason" id="reason" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2 px-3 text-sm focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
                <option value="">Todos</option>
                <option value="adjustment" {{ request('reason') === 'adjustment' ? 'selected' : '' }}>Ajuste Manual</option>
                <option value="purchase" {{ request('reason') === 'purchase' ? 'selected' : '' }}>Compra</option>
                <option value="sale" {{ request('reason') === 'sale' ? 'selected' : '' }}>Venta</option>
                <option value="damage" {{ request('reason') === 'damage' ? 'selected' : '' }}>Daño/Pérdida</option>
                <option value="return" {{ request('reason') === 'return' ? 'selected' : '' }}>Devolución</option>
            </select>
        </div>

        {{-- Botones --}}
        <div class="flex items-end space-x-2">
            <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow rounded-lg transition-all">
                Filtrar
            </button>
            <a href="{{ route('tenant.admin.inventory.movements') }}" class="inline-flex items-center px-3 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50" title="Limpiar">
                &times;
            </a>
        </div>
    </form>

    {{-- Tabla de Movimientos --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha / Hora</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Producto (SKU)</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tipo</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Cantidad</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Motivo</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Observaciones</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Responsable</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($movements as $movement)
                    <tr class="hover:bg-slate-50 transition-colors">
                        {{-- Fecha --}}
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">
                            {{ $movement->created_at->setTimezone('America/Argentina/Buenos_Aires')->format('d/m/Y H:i') }} hs
                        </td>

                        {{-- Producto --}}
                        <td class="px-6 py-4">
                            @if($movement->inventory && $movement->inventory->product)
                                <span class="text-sm font-bold text-slate-800 block leading-tight">{{ $movement->inventory->product->name }}</span>
                                <span class="text-xs font-mono text-slate-400 block mt-0.5">SKU: {{ $movement->inventory->product->sku }}</span>
                            @else
                                <span class="text-xs text-rose-500 italic">Producto Eliminado</span>
                            @endif
                        </td>

                        {{-- Tipo --}}
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($movement->type === 'in')
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Entrada (+)
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-rose-50 text-rose-700 border border-rose-100">
                                    Salida (-)
                                </span>
                            @endif
                        </td>

                        {{-- Cantidad --}}
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-slate-800 font-mono">
                            {{ $movement->quantity }} uds
                        </td>

                        {{-- Motivo --}}
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700 font-medium">
                            @php
                                $reasons = [
                                    'purchase' => 'Compra de Stock',
                                    'sale' => 'Venta Directa',
                                    'adjustment' => 'Ajuste Manual',
                                    'damage' => 'Daño / Pérdida',
                                    'return' => 'Devolución'
                                ];
                            @endphp
                            {{ $reasons[$movement->reason] ?? $movement->reason }}
                        </td>

                        {{-- Observaciones --}}
                        <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate" title="{{ $movement->description }}">
                            {{ $movement->description ?: '-' }}
                        </td>

                        {{-- Responsable --}}
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600 font-medium">
                            {{ $movement->user ? $movement->user->name : 'Sistema (Carga Masiva)' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                            </svg>
                            <h3 class="mt-4 text-sm font-medium text-slate-900">No hay movimientos registrados</h3>
                            <p class="mt-1 text-sm text-slate-500">Las modificaciones de stock se verán listadas en este panel.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($movements->hasPages())
        <div class="mt-4">
            {{ $movements->links() }}
        </div>
    @endif
</div>
@endsection
