@extends('layouts.tenant')

@section('title', $product->name . ' - ' . $tenant->name)
@section('meta_description', Str::limit(strip_tags($product->description ?? ''), 150, '...'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Breadcrumbs / Migas de Pan --}}
    <nav class="flex mb-8 text-xs font-semibold text-slate-400 gap-2 items-center">
        <a href="{{ url('/') }}" class="hover:text-tenant-primary transition-colors">Inicio</a>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        @if($product->category)
            <a href="{{ url('/?categoria=' . $product->category->slug) }}" class="hover:text-tenant-primary transition-colors">{{ $product->category->name }}</a>
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        @endif
        <span class="text-slate-600 truncate">{{ $product->name }}</span>
    </nav>

    {{-- Contenedor de Producto --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 bg-white rounded-3xl border border-slate-150 shadow-sm p-6 sm:p-8">
        
        {{-- Galería de Imágenes (Alpine.js) --}}
        @php
            $hasImages = $product->images && count($product->images) > 0;
            $firstImage = $hasImages ? asset('storage/' . $product->images[0]) : null;
        @endphp
        
        <div class="lg:col-span-6 flex flex-col gap-4" 
             x-data="{ activeImage: '{{ $firstImage ?? '' }}' }">
            
            {{-- Contenedor Imagen Principal --}}
            <div class="aspect-square bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 flex items-center justify-center relative group">
                @if($hasImages)
                    <img :src="activeImage" alt="{{ $product->name }}" class="w-full h-full object-cover transition-all duration-300 group-hover:scale-105">
                @else
                    <div class="flex flex-col items-center justify-center text-slate-350 p-6">
                        <svg class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span class="text-sm mt-3 font-semibold">Sin imagen disponible</span>
                    </div>
                @endif

                {{-- Badges sobre la imagen --}}
                @if($product->sale_price)
                    <span class="absolute top-4 left-4 bg-red-500 text-white text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-lg shadow-md">
                        Oferta
                    </span>
                @endif
            </div>

            {{-- Miniaturas --}}
            @if($hasImages && count($product->images) > 1)
                <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-thin">
                    @foreach($product->images as $img)
                        @php $imgUrl = asset('storage/' . $img); @endphp
                        <button type="button" 
                                @click="activeImage = '{{ $imgUrl }}'"
                                :class="activeImage === '{{ $imgUrl }}' ? 'border-tenant-primary ring-2 ring-tenant-primary/20' : 'border-slate-200 hover:border-slate-300'"
                                class="h-20 w-20 flex-shrink-0 bg-slate-50 rounded-xl border-2 overflow-hidden transition-all duration-200">
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Información de Compra y Detalles --}}
        <div class="lg:col-span-6 flex flex-col justify-between">
            <div class="space-y-5">
                {{-- Marca y SKU --}}
                <div class="flex items-center justify-between">
                    @if($product->brand)
                        <span class="text-xs font-bold text-tenant-primary uppercase tracking-widest bg-tenant-primary/5 px-3 py-1.5 rounded-lg">
                            {{ $product->brand->name }}
                        </span>
                    @else
                        <span></span>
                    @endif
                    <span class="text-xs font-semibold text-slate-400 font-mono">SKU: {{ $product->sku }}</span>
                </div>

                {{-- Nombre --}}
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
                    {{ $product->name }}
                </h1>

                {{-- Precios --}}
                <div class="flex flex-col gap-1">
                    <div class="flex items-baseline gap-3">
                        @if($product->sale_price)
                            <span class="text-3xl font-extrabold text-tenant-primary">${{ number_format($product->sale_price, 2) }}</span>
                            <span class="text-base text-slate-400 line-through font-semibold">${{ number_format($product->price, 2) }}</span>
                            
                            @php
                                $discount = (($product->price - $product->sale_price) / $product->price) * 100;
                            @endphp
                            <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-1 rounded-md">
                                -{{ round($discount) }}% OFF
                            </span>
                        @else
                            <span class="text-3xl font-extrabold text-slate-800">${{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    @if(($exchangeRate ?? 0) > 0)
                        @php
                            $detailPriceVES = ($product->sale_price ?? $product->price) * $exchangeRate;
                        @endphp
                        <div class="text-base font-bold text-emerald-600">
                            Equivale a: <span class="text-lg font-extrabold font-sans">Bs. {{ number_format($detailPriceVES, 2) }}</span>
                            <span class="text-[10px] text-slate-400 font-normal ml-1">(Tasa: {{ number_format($exchangeRate, 2) }})</span>
                        </div>
                    @endif
                </div>

                <hr class="border-slate-100">

                {{-- Stock badge y Disponibilidad --}}
                @php
                    $stock = $product->inventory ? $product->inventory->quantity : 0;
                    $allowNegative = $product->inventory ? $product->inventory->allow_negative : true;
                    $isOutOfStock = !$allowNegative && $stock <= 0;
                @endphp

                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-slate-500">Disponibilidad:</span>
                    @if($isOutOfStock)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                            Agotado
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            @if($allowNegative)
                                Disponible
                            @else
                                {{ $stock }} unidades disponibles
                            @endif
                        </span>
                    @endif
                </div>

                {{-- Descripción --}}
                @if($product->description)
                    <div class="space-y-2">
                        <h3 class="text-sm font-bold text-slate-800">Descripción del Producto</h3>
                        <div class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">
                            {{ $product->description }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sección de Compra (Alpine.js selector de cantidad) --}}
            <div class="mt-8 pt-6 border-t border-slate-100 space-y-4" 
                 x-data="{ qty: 1, maxQty: {{ !$allowNegative ? $stock : 999 }} }">
                
                @if(!$isOutOfStock)
                    <div class="flex flex-col sm:flex-row gap-4">
                        {{-- Selector de Cantidad --}}
                        <div class="flex items-center justify-between bg-slate-100 rounded-xl px-4 py-2 sm:w-36">
                            <span class="text-xs font-bold text-slate-500 sm:hidden">Cantidad</span>
                            <div class="flex items-center gap-4">
                                <button type="button" 
                                        @click="if(qty > 1) qty--"
                                        class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-600 hover:bg-slate-200 transition-colors font-bold text-lg">
                                    -
                                </button>
                                <span class="font-extrabold text-sm text-slate-800 w-6 text-center" x-text="qty"></span>
                                <button type="button" 
                                        @click="if(maxQty === 0 || qty < maxQty) qty++"
                                        class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-600 hover:bg-slate-200 transition-colors font-bold text-lg">
                                    +
                                </button>
                            </div>
                        </div>

                        {{-- Botón Agregar al Carrito --}}
                        <form action="{{ route('tenant.cart.add') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="qty" :value="qty">
                            <button type="submit" 
                                    class="w-full flex items-center justify-center px-6 py-4 rounded-xl text-sm font-bold text-white bg-tenant-primary bg-tenant-primary-hover shadow-lg transition-all duration-200 gap-2">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Agregar al Carrito
                            </button>
                        </form>
                    </div>

                    {{-- Botón Rápido WhatsApp --}}
                    @if($tenant->getSetting('whatsapp_number'))
                        @php
                            $whatsPrice = $product->sale_price ?? $product->price;
                            $whatsText = "Hola! Estoy interesado en comprar el producto " . $product->name . " (SKU: " . $product->sku . ") con precio $" . number_format($whatsPrice, 2) . ". Aquí está el enlace: " . request()->url();
                            $whatsUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $tenant->getSetting('whatsapp_number')) . "?text=" . urlencode($whatsText);
                        @endphp
                        <a href="{{ $whatsUrl }}" target="_blank"
                           class="w-full flex items-center justify-center px-6 py-3 rounded-xl text-sm font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-all duration-200 gap-2">
                            <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                <path d="M12 0C5.374 0 0 5.373 0 12c0 2.117.549 4.099 1.508 5.819L0 24l6.335-1.652A11.962 11.962 0 0012 24c6.626 0 12-5.373 12-12S18.626 0 12 0zm0 22c-1.885 0-3.653-.49-5.192-1.349L2.9 21.89l1.272-3.791A9.972 9.972 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" fill-rule="evenodd" clip-rule="evenodd"/>
                            </svg>
                            Comprar rápido por WhatsApp
                        </a>
                    @endif
                @else
                    <button disabled 
                            class="w-full flex items-center justify-center px-6 py-4 rounded-xl text-sm font-bold text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed gap-2">
                        Producto Agotado
                    </button>
                    @if($tenant->getSetting('whatsapp_number'))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tenant->getSetting('whatsapp_number')) }}?text=Hola! Quería consultar disponibilidad del producto agotado {{ urlencode($product->name) }}" target="_blank"
                           class="w-full flex items-center justify-center px-6 py-3 rounded-xl text-sm font-bold text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition-all duration-200 gap-2">
                            Consultar próxima reposición por WhatsApp
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Productos Relacionados --}}
    @if($related->isNotEmpty())
        <div class="mt-16 space-y-6">
            <h2 class="text-xl font-bold text-slate-900">Productos Relacionados</h2>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach($related as $rel)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-200 overflow-hidden group">
                        {{-- Imagen --}}
                        <div class="aspect-square bg-slate-50 overflow-hidden flex items-center justify-center relative">
                            <a href="{{ route('tenant.product.show', $rel->slug) }}" class="w-full h-full">
                                @if($rel->images && count($rel->images) > 0)
                                    <img src="{{ asset('storage/' . $rel->images[0]) }}" alt="{{ $rel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="flex flex-col items-center justify-center text-slate-300 h-full p-4">
                                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <span class="text-[10px] mt-2 text-center">Sin imagen</span>
                                    </div>
                                @endif
                            </a>
                        </div>
                        
                        {{-- Info --}}
                        <div class="p-4">
                            <h3 class="text-xs font-semibold text-slate-800 leading-tight line-clamp-2 hover:text-tenant-primary transition-colors">
                                <a href="{{ route('tenant.product.show', $rel->slug) }}">{{ $rel->name }}</a>
                            </h3>
                            
                            <div class="mt-3 flex flex-col gap-0.5">
                                <div class="flex items-baseline justify-between">
                                    @if($rel->sale_price)
                                        <span class="text-sm font-bold text-tenant-primary">${{ number_format($rel->sale_price, 2) }}</span>
                                    @else
                                        <span class="text-sm font-bold text-slate-800">${{ number_format($rel->price, 2) }}</span>
                                    @endif
                                </div>
                                @if(($exchangeRate ?? 0) > 0)
                                    @php
                                        $relPriceVES = ($rel->sale_price ?? $rel->price) * $exchangeRate;
                                    @endphp
                                    <div class="text-[10px] font-bold text-emerald-600">
                                        Bs. {{ number_format($relPriceVES, 2) }}
                                    </div>
                                @endif
                            </div>

                            @php
                                $relStock = $rel->inventory ? $rel->inventory->quantity : 0;
                                $relAllowNeg = $rel->inventory ? $rel->inventory->allow_negative : true;
                                $relOutOfStock = !$relAllowNeg && $relStock <= 0;
                            @endphp

                            @if($relOutOfStock)
                                <span class="mt-2 block text-center py-1 rounded bg-slate-100 text-[10px] font-bold text-slate-400">Agotado</span>
                            @else
                                <form action="{{ route('tenant.cart.add') }}" method="POST" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $rel->id }}">
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="w-full flex items-center justify-center py-1.5 rounded-lg text-[10px] font-bold text-white bg-tenant-primary hover:bg-tenant-primary/95 transition-all gap-1 shadow-sm">
                                        Agregar +
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
