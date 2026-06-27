@extends('layouts.tenant')

@section('title', $tenant->name . ' - Catálogo Online')
@section('meta_description', 'Compra en ' . $tenant->name . '. Catálogo de productos online con envío y atención por WhatsApp.')

@section('content')
<div x-data="{ 
    searchQuery: '{{ request('buscar', '') }}',
    clearSearch() {
        this.searchQuery = '';
        window.location.href = '{{ url('/') }}' + ( '{{ request('categoria') }}' ? '?categoria={{ request('categoria') }}' : '' );
    }
}">

    {{-- Hero Banner de la Tienda --}}
    <div class="bg-tenant-primary py-16 relative overflow-hidden">
        {{-- Background decorative shapes --}}
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-white blur-xl"></div>
            <div class="absolute bottom-5 right-5 w-60 h-60 rounded-full bg-white blur-2xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-4xl font-extrabold text-white tracking-tight">
                @if($tenant->logo)
                    <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}" class="h-16 mx-auto mb-4 object-contain">
                @else
                    {{ $logoText }}
                @endif
            </h1>
            <p class="mt-3 text-white/70 text-base max-w-md mx-auto">{{ $tenant->city }} &middot; {{ $tenant->contact_phone }}</p>
            
            {{-- Gran Barra de Búsqueda --}}
            <div class="mt-8 max-w-xl mx-auto">
                <form method="GET" action="{{ url('/') }}" class="relative">
                    @if(request('categoria'))
                        <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                    @endif
                    <div class="relative rounded-2xl shadow-xl">
                        <input type="text" name="buscar" x-model="searchQuery"
                            placeholder="Buscar productos por nombre, SKU o marca..."
                            class="block w-full rounded-2xl border-0 bg-white py-4 pl-5 pr-14 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-tenant-secondary transition-all outline-none">
                        
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-1">
                            {{-- Clear button --}}
                            <button type="button" x-show="searchQuery.length > 0" @click="clearSearch()"
                                class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            {{-- Search button --}}
                            <button type="submit" class="p-2 rounded-xl bg-tenant-primary text-white hover:opacity-90 transition-all">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Layout Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        {{-- Alertas de Sesión --}}
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Grid de Sidebar + Catálogo --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Filtros Sidebar (Categorías) --}}
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-150 p-6 shadow-sm sticky top-24">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="h-4 w-4 text-tenant-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Categorías
                    </h2>
                    
                    <div class="flex flex-col gap-1">
                        <a href="{{ url('/') . (request('buscar') ? '?buscar=' . request('buscar') : '') }}" 
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 
                           {{ !request('categoria') ? 'bg-tenant-primary/5 text-tenant-primary' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span>Todos los productos</span>
                            <span class="text-xs bg-slate-100 text-slate-500 font-mono px-2 py-0.5 rounded-full">All</span>
                        </a>
                        
                        @foreach($categories as $category)
                            @php
                                $isActiveCat = request('categoria') === $category->slug;
                                $catUrl = url('/?categoria=' . $category->slug) . (request('buscar') ? '&buscar=' . request('buscar') : '');
                            @endphp
                            <a href="{{ $catUrl }}" 
                               class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 
                               {{ $isActiveCat ? 'bg-tenant-primary/5 text-tenant-primary' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span>{{ $category->name }}</span>
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Resultados de Catálogo --}}
            <div class="lg:col-span-9 space-y-8">
                
                {{-- Encabezado de Resultados --}}
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">
                            @if(request('categoria'))
                                @php
                                    $activeCategory = $categories->firstWhere('slug', request('categoria'));
                                @endphp
                                {{ $activeCategory ? $activeCategory->name : 'Productos' }}
                            @else
                                Todos los Productos
                            @endif
                        </h2>
                        
                        {{-- Indicador de filtros activos --}}
                        @if(request('buscar') || request('categoria'))
                            <div class="flex flex-wrap gap-2 mt-2">
                                @if(request('categoria') && isset($activeCategory))
                                    <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 text-xs px-2.5 py-1 rounded-md font-semibold">
                                        Categoría: {{ $activeCategory->name }}
                                        <a href="{{ url('/') . (request('buscar') ? '?buscar=' . request('buscar') : '') }}" class="text-slate-400 hover:text-slate-600">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </a>
                                    </span>
                                @endif
                                @if(request('buscar'))
                                    <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 text-xs px-2.5 py-1 rounded-md font-semibold">
                                        Búsqueda: "{{ request('buscar') }}"
                                        <a href="{{ url('/') . (request('categoria') ? '?categoria=' . request('categoria') : '') }}" class="text-slate-400 hover:text-slate-600">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </a>
                                    </span>
                                @endif
                            </div>
                        @else
                            <p class="text-xs text-slate-400 mt-1">Explora nuestro catálogo completo.</p>
                        @endif
                    </div>

                    <span class="text-sm font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-lg">
                        {{ $products->total() }} producto(s)
                    </span>
                </div>

                {{-- Grid de Productos --}}
                @if($products->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white rounded-2xl border border-slate-150 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-200 overflow-hidden group flex flex-col justify-between">
                                <div>
                                    {{-- Imagen --}}
                                    <div class="aspect-square bg-slate-50 overflow-hidden flex items-center justify-center relative">
                                        <a href="{{ route('tenant.product.show', $product->slug) }}" class="w-full h-full">
                                            @if($product->images && count($product->images) > 0)
                                                <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="flex flex-col items-center justify-center text-slate-300 h-full p-4">
                                                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                    <span class="text-xs mt-2 text-center">Sin imagen</span>
                                                </div>
                                            @endif
                                        </a>
                                        
                                        @if($product->sale_price)
                                            <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-md shadow-sm">
                                                Oferta
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Contenido del Producto --}}
                                    <div class="p-4 space-y-1">
                                        @if($product->brand)
                                            <span class="text-[10px] font-bold text-tenant-primary uppercase tracking-widest">{{ $product->brand->name }}</span>
                                        @endif
                                        <h3 class="text-sm font-bold text-slate-800 leading-snug line-clamp-2 hover:text-tenant-primary transition-colors">
                                            <a href="{{ route('tenant.product.show', $product->slug) }}">{{ $product->name }}</a>
                                        </h3>
                                        <p class="text-[10px] text-slate-400 font-mono">SKU: {{ $product->sku }}</p>
                                    </div>
                                </div>
                                
                                <div class="p-4 pt-0">
                                    <div class="flex flex-col gap-0.5 mb-3">
                                        <div class="flex items-baseline justify-between">
                                            @if($product->sale_price)
                                                <span class="text-base font-black text-tenant-primary">${{ number_format($product->sale_price, 2) }}</span>
                                                <span class="text-xs text-slate-400 line-through">${{ number_format($product->price, 2) }}</span>
                                            @else
                                                <span class="text-base font-black text-slate-800">${{ number_format($product->price, 2) }}</span>
                                            @endif
                                        </div>
                                        @if(($exchangeRate ?? 0) > 0)
                                            @php
                                                $priceInBs = ($product->sale_price ?? $product->price) * $exchangeRate;
                                            @endphp
                                            <div class="text-[11px] font-bold text-emerald-600">
                                                Bs. {{ number_format($priceInBs, 2) }}
                                            </div>
                                        @endif
                                    </div>

                                    @php
                                        $stock = $product->inventory ? $product->inventory->quantity : 0;
                                        $allowNegative = $product->inventory ? $product->inventory->allow_negative : true;
                                        $isOutOfStock = !$allowNegative && $stock <= 0;
                                    @endphp

                                    @if($isOutOfStock)
                                        <button disabled class="w-full flex items-center justify-center py-2.5 rounded-xl text-xs font-bold text-slate-400 bg-slate-100 cursor-not-allowed gap-1.5 shadow-sm">
                                            Agotado
                                        </button>
                                    @else
                                        <form action="{{ route('tenant.cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="qty" value="1">
                                            <button type="submit" class="w-full flex items-center justify-center py-2.5 rounded-xl text-xs font-bold text-white bg-tenant-primary hover:bg-tenant-primary/95 transition-all gap-1.5 shadow-sm">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Agregar al Carrito
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Paginación --}}
                    @if($products->hasPages())
                        <div class="mt-8 pt-4 border-t border-slate-100">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    {{-- Estado vacío --}}
                    <div class="bg-white rounded-3xl border border-slate-150 p-16 text-center shadow-sm">
                        <svg class="mx-auto h-16 w-16 text-slate-350" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-bold text-slate-800">No se encontraron productos</h3>
                        <p class="mt-2 text-slate-400 text-sm max-w-md mx-auto">No hay resultados que coincidan con tu búsqueda o categoría seleccionada. Intenta limpiar los filtros o buscar con otro término.</p>
                        <a href="{{ url('/') }}" class="mt-6 inline-flex items-center px-6 py-3 rounded-xl text-sm font-bold text-white bg-tenant-primary bg-tenant-primary-hover shadow-md transition-all">
                            Limpiar Filtros
                        </a>
                    </div>
                @endif

            </div>

        </div>
    </div>

    {{-- Botón flotante de WhatsApp --}}
    @if($tenant->getSetting('whatsapp_number'))
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tenant->getSetting('whatsapp_number')) }}?text=Hola! Quiero hacer una consulta sobre sus productos." target="_blank"
            class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-green-500 hover:bg-green-400 shadow-2xl shadow-green-500/30 hover:-translate-y-1 transition-all duration-200">
            <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                <path d="M12 0C5.374 0 0 5.373 0 12c0 2.117.549 4.099 1.508 5.819L0 24l6.335-1.652A11.962 11.962 0 0012 24c6.626 0 12-5.373 12-12S18.626 0 12 0zm0 22c-1.885 0-3.653-.49-5.192-1.349L2.9 21.89l1.272-3.791A9.972 9.972 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" fill-rule="evenodd" clip-rule="evenodd"/>
            </svg>
        </a>
    @endif

</div>
@endsection
