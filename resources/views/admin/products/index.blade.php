@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 font-sans">Catálogo de Productos</h2>
            <p class="text-sm text-slate-500">Administra tus artículos, precios, variaciones y niveles de stock.</p>
        </div>
        <a href="{{ route('tenant.admin.products.create') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow rounded-lg transition-all duration-200">
            Nuevo Producto
        </a>
    </div>

    {{-- Panel de Filtros --}}
    <form method="GET" action="{{ route('tenant.admin.products.index') }}" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm grid grid-cols-1 md:grid-cols-5 gap-4">
        {{-- Búsqueda --}}
        <div class="md:col-span-2">
            <label for="search" class="block text-xs font-semibold text-slate-500 uppercase">Buscar</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nombre, SKU o código interno..."
                class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2 px-3 text-sm focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
        </div>

        {{-- Categoría --}}
        <div>
            <label for="category_id" class="block text-xs font-semibold text-slate-500 uppercase">Categoría</label>
            <select name="category_id" id="category_id" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2 px-3 text-sm focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
                <option value="">Todas</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Marca --}}
        <div>
            <label for="brand_id" class="block text-xs font-semibold text-slate-500 uppercase">Marca</label>
            <select name="brand_id" id="brand_id" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2 px-3 text-sm focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
                <option value="">Todas</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Botón de Acción --}}
        <div class="flex items-end space-x-2">
            <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow rounded-lg transition-all">
                Filtrar
            </button>
            <a href="{{ route('tenant.admin.products.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors" title="Limpiar Filtros">
                &times;
            </a>
        </div>
        
        {{-- Segunda línea de filtros ocultable/secundaria --}}
        <div class="md:col-span-5 grid grid-cols-1 md:grid-cols-4 gap-4 border-t border-slate-100 pt-3 mt-1">
            {{-- Estado --}}
            <div>
                <select name="status" class="block w-full rounded-lg border border-slate-300 bg-slate-50 py-1.5 px-3 text-xs focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
                    <option value="">Cualquier Estado</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Borrador</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            
            {{-- Alerta Stock --}}
            <div>
                <select name="stock_status" class="block w-full rounded-lg border border-slate-300 bg-slate-50 py-1.5 px-3 text-xs focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
                    <option value="">Cualquier Inventario</option>
                    <option value="normal" {{ request('stock_status') === 'normal' ? 'selected' : '' }}>Stock Normal</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Bajo Stock Mínimo</option>
                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Sin Stock</option>
                </select>
            </div>
        </div>
    </form>

    {{-- Tabla de Productos --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Producto</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Categoría / Marca</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Precio</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Stock</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th scope="col" class="relative px-6 py-3.5">
                        <span class="sr-only">Acciones</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($products as $product)
                    <tr class="hover:bg-slate-50 transition-colors">
                        {{-- Producto Info --}}
                        <td class="px-6 py-4 flex items-center">
                            <div class="h-12 w-12 rounded-lg border border-slate-200 bg-slate-50 flex-shrink-0 flex items-center justify-center overflow-hidden mr-4">
                                @if($product->images && count($product->images) > 0)
                                    <img src="{{ asset('storage/' . $product->images[0]) }}" alt="Thumbnail" class="h-full w-full object-cover">
                                @else
                                    <svg class="h-6 w-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm font-bold text-slate-800 block leading-tight">{{ $product->name }}</span>
                                <span class="text-xs font-mono text-slate-400 block mt-0.5">SKU: {{ $product->sku }}</span>
                            </div>
                        </td>

                        {{-- Categoría y Marca --}}
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-700 block font-medium">{{ $product->category->name }}</span>
                            <span class="text-xs text-slate-400 block">{{ $product->brand ? $product->brand->name : 'Sin Marca' }}</span>
                        </td>

                        {{-- Precios --}}
                        <td class="px-6 py-4">
                            @if($product->sale_price)
                                <span class="text-sm font-bold text-tenant-primary block">${{ number_format($product->sale_price, 2) }}</span>
                                <span class="text-xs text-slate-400 line-through block">${{ number_format($product->price, 2) }}</span>
                            @else
                                <span class="text-sm font-bold text-slate-800 block">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </td>

                        {{-- Stock actual e indicadores --}}
                        <td class="px-6 py-4">
                            @if($product->inventory)
                                @php
                                    $qty = $product->inventory->quantity;
                                    $min = $product->inventory->min_stock;
                                @endphp
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-bold {{ $qty <= 0 ? 'text-rose-600' : ($qty <= $min ? 'text-amber-500' : 'text-slate-800') }}">
                                        {{ $qty }} uds
                                    </span>
                                    
                                    @if($qty <= 0)
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-100">Sin Stock</span>
                                    @elseif($qty <= $min)
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Bajo Mínimo</span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-400 block mt-0.5">Mínimo alertable: {{ $min }}</span>
                            @else
                                <span class="text-xs text-slate-400 italic block">Sin inventario</span>
                            @endif
                        </td>

                        {{-- Estado --}}
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($product->status === 'active')
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Activo</span>
                            @elseif($product->status === 'draft')
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Borrador</span>
                            @else
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">Inactivo</span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('tenant.admin.products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors">Editar</a>
                            <form method="POST" action="{{ route('tenant.admin.products.destroy', $product->id) }}" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este producto? Esto también eliminará el stock y Kárdex asociado.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-900 transition-colors">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <h3 class="mt-4 text-sm font-medium text-slate-900">No hay productos</h3>
                            <p class="mt-1 text-sm text-slate-500">Agrega tu primer artículo al catálogo o realiza una carga masiva.</p>
                            <div class="mt-6 flex justify-center space-x-3">
                                <a href="{{ route('tenant.admin.import.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
                                    Carga Masiva (Excel)
                                </a>
                                <a href="{{ route('tenant.admin.products.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow rounded-lg">
                                    Nuevo Producto
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($products->hasPages())
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
