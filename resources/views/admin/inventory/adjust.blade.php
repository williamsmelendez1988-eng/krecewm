@extends('layouts.admin')

@section('content')
<div class="max-w-xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 font-sans">Ajuste de Inventario</h2>
            <p class="text-sm text-slate-500">Registra entradas o salidas de mercadería y actualiza el Kárdex de auditoría.</p>
        </div>
        <a href="{{ route('tenant.admin.inventory.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            &larr; Volver
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-lg bg-rose-50 border border-rose-200 p-4 text-rose-800 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- Ficha del Producto --}}
    <div class="bg-slate-100 p-4 rounded-xl border border-slate-200 flex items-center space-x-4">
        <div class="h-14 w-14 rounded-lg bg-white border border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0">
            @if($inventory->product->images && count($inventory->product->images) > 0)
                <img src="{{ asset('storage/' . $inventory->product->images[0]) }}" alt="Thumbnail" class="h-full w-full object-cover">
            @else
                <svg class="h-7 w-7 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            @endif
        </div>
        <div>
            <span class="text-xs font-bold text-tenant-primary uppercase tracking-wider block">Producto seleccionado</span>
            <span class="text-base font-bold text-slate-800 block mt-0.5 leading-tight">{{ $inventory->product->name }}</span>
            <div class="flex items-center space-x-3 mt-1">
                <span class="text-xs font-mono text-slate-500">SKU: {{ $inventory->product->sku }}</span>
                <span class="text-xs font-bold text-slate-700">Stock Actual: {{ $inventory->quantity }} unidades</span>
            </div>
        </div>
    </div>

    {{-- Formulario de Ajuste --}}
    <form method="POST" action="{{ route('tenant.admin.inventory.adjust.post', $inventory->id) }}" class="space-y-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        @csrf

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                {{-- Tipo de Movimiento --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-slate-700">Tipo de Movimiento</label>
                    <select name="type" id="type" required
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                        <option value="in" {{ old('type') === 'in' ? 'selected' : '' }}>Entrada (+) Incrementa Stock</option>
                        <option value="out" {{ old('type') === 'out' ? 'selected' : '' }}>Salida (-) Reduce Stock</option>
                    </select>
                </div>

                {{-- Cantidad --}}
                <div>
                    <label for="quantity" class="block text-sm font-medium text-slate-700">Cantidad (Unidades)</label>
                    <input type="number" name="quantity" id="quantity" min="1" required value="{{ old('quantity', '1') }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                </div>
            </div>

            {{-- Motivo --}}
            <div>
                <label for="reason" class="block text-sm font-medium text-slate-700">Motivo del Ajuste</label>
                <select name="reason" id="reason" required
                    class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                    <option value="adjustment" {{ old('reason') === 'adjustment' ? 'selected' : '' }}>Ajuste de Inventario Manual</option>
                    <option value="purchase" {{ old('reason') === 'purchase' ? 'selected' : '' }}>Compra de Mercadería (Ingreso)</option>
                    <option value="sale" {{ old('reason') === 'sale' ? 'selected' : '' }}>Venta Directa</option>
                    <option value="damage" {{ old('reason') === 'damage' ? 'selected' : '' }}>Mercadería Dañada / Pérdida</option>
                    <option value="return" {{ old('reason') === 'return' ? 'selected' : '' }}>Devolución de Cliente</option>
                </select>
            </div>

            {{-- Descripción --}}
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700">Descripción / Observaciones</label>
                <textarea name="description" id="description" rows="3" placeholder="Ej: Pérdida por caída en góndola / Factura de compra #1255"
                    class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
            <a href="{{ route('tenant.admin.inventory.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow rounded-lg transition-all">
                Registrar Movimiento
            </button>
        </div>
    </form>
</div>
@endsection
