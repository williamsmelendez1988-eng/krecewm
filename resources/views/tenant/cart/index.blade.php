@extends('layouts.tenant')

@section('title', 'Carrito de Compras - ' . $tenant->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-8">Tu Carrito de Compras</h1>

    {{-- Alertas de Sesión --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-center gap-2 shadow-sm">
            <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center gap-2 shadow-sm">
            <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    @if(empty($cart))
        <div class="text-center py-20 bg-white rounded-2xl border border-slate-100 shadow-sm">
            <svg class="mx-auto h-20 w-20 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <h2 class="mt-4 text-xl font-bold text-slate-800">El carrito está vacío</h2>
            <p class="mt-2 text-slate-500 text-sm">Explora nuestro catálogo y agrega productos espectaculares.</p>
            <a href="{{ route('catalog.index') }}" class="mt-6 inline-flex items-center px-6 py-3 rounded-xl text-sm font-bold text-white bg-tenant-primary hover:bg-tenant-primary/90 transition-all duration-200 shadow-md">
                Volver al catálogo
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Lista de Items --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart as $id => $item)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center justify-between gap-4">
                        {{-- Imagen --}}
                        <div class="h-20 w-20 bg-slate-50 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center border border-slate-100">
                            @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                            @else
                                <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            @endif
                        </div>

                        {{-- Información --}}
                        <div class="flex-grow min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 truncate leading-snug">{{ $item['name'] }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">SKU: {{ $item['sku'] }}</p>
                            <p class="text-sm font-bold text-tenant-primary mt-1">${{ number_format($item['price'], 2) }}</p>
                        </div>

                        {{-- Controles de cantidad y precio subtotal --}}
                        <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3">
                            <form action="{{ route('tenant.cart.update') }}" method="POST" class="flex items-center border border-slate-200 rounded-lg overflow-hidden bg-slate-50">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                <button type="submit" name="qty" value="{{ $item['qty'] - 1 }}" class="px-2 py-1 text-slate-500 hover:bg-slate-200 transition-colors {{ $item['qty'] <= 1 ? 'pointer-events-none opacity-50' : '' }}">-</button>
                                <input type="text" readonly value="{{ $item['qty'] }}" class="w-10 text-center text-xs font-bold bg-transparent border-none focus:outline-none focus:ring-0 text-slate-800">
                                <button type="submit" name="qty" value="{{ $item['qty'] + 1 }}" class="px-2 py-1 text-slate-500 hover:bg-slate-200 transition-colors">+</button>
                            </form>

                            <div class="text-right min-w-[70px]">
                                <span class="text-sm font-extrabold text-slate-800">${{ number_format($item['price'] * $item['qty'], 2) }}</span>
                            </div>

                            <form action="{{ route('tenant.cart.remove') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Eliminar artículo">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach

                {{-- Limpiar carrito --}}
                <div class="flex justify-start">
                    <form action="{{ route('tenant.cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-red-500 transition-colors flex items-center gap-1.5 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Vaciar todo el carrito
                        </button>
                    </form>
                </div>
            </div>

            {{-- Resumen de Compra --}}
            <div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-24">
                    <h2 class="text-lg font-bold text-slate-800 mb-4 pb-4 border-b border-slate-100">Resumen de Compra</h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-slate-500">
                            <span>Artículos ({{ collect($cart)->sum('qty') }})</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Envío</span>
                            <span class="font-medium text-green-600">Gratis / WhatsApp</span>
                        </div>
                        
                        <div class="pt-4 border-t border-slate-100 flex justify-between items-baseline">
                            <span class="text-base font-bold text-slate-800">Total</span>
                            <span class="text-2xl font-extrabold text-tenant-primary">${{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('tenant.checkout.index') }}" class="mt-6 w-full flex items-center justify-center px-4 py-3 rounded-xl text-sm font-bold text-white bg-tenant-primary hover:bg-tenant-primary/95 transition-all duration-200 shadow-md">
                        Proceder al Checkout
                    </a>
                    
                    <a href="{{ route('catalog.index') }}" class="mt-3 w-full flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-50 hover:bg-slate-100 transition-all duration-200">
                        Seguir Comprando
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
