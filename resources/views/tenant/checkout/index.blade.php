@extends('layouts.tenant')

@section('title', 'Finalizar Compra - ' . $tenant->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-8">Finalizar tu Compra</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Formulario de Datos --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8">
                <h2 class="text-lg font-bold text-slate-800 mb-6 pb-2 border-b border-slate-100">Información del Comprador y Envío</h2>

                <form action="{{ route('tenant.checkout.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nombre Completo *</label>
                            <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="Ej. Juan Pérez"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                            @error('name')
                                <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Teléfono Móvil (WhatsApp) *</label>
                            <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}" placeholder="Ej. 3001234567"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                            <span class="text-[10px] text-slate-400 mt-1 block font-medium">Requerido para coordinar la entrega y el pago por WhatsApp.</span>
                            @error('phone')
                                <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Correo Electrónico (Opcional)</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="juan@correo.com"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                        @error('email')
                            <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Dirección de Entrega *</label>
                            <input type="text" id="address" name="address" required value="{{ old('address') }}" placeholder="Calle 123 #45-67, Apto 201"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                            @error('address')
                                <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Ciudad / Municipio *</label>
                            <input type="text" id="city" name="city" required value="{{ old('city') }}" placeholder="Medellín"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                            @error('city')
                                <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Pasarelas de Pago simplificadas --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Método de Pago</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3" x-data="{ selectedMethod: 'whatsapp' }">
                            
                            {{-- Opción 1: WhatsApp instant --}}
                            <label class="border-2 rounded-2xl p-4 flex items-start cursor-pointer hover:bg-slate-50 transition-all duration-200 relative"
                                :class="selectedMethod === 'whatsapp' ? 'border-tenant-primary bg-white' : 'border-slate-100 bg-slate-50/50'">
                                <input type="radio" name="payment_method" value="whatsapp" checked x-model="selectedMethod" class="sr-only">
                                <div class="flex h-5 items-center">
                                    <div class="h-4 w-4 rounded-full border border-slate-300 flex items-center justify-center" :class="selectedMethod === 'whatsapp' ? 'border-tenant-primary bg-tenant-primary' : ''">
                                        <div class="h-1.5 w-1.5 rounded-full bg-white" x-show="selectedMethod === 'whatsapp'"></div>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <span class="block text-sm font-bold text-slate-800">Pedido por WhatsApp</span>
                                    <span class="block text-xs text-slate-400 mt-1">Completa el pedido y envíalo directamente por chat.</span>
                                </div>
                            </label>

                            {{-- Opción 2: Transferencia Bancaria --}}
                            <label class="border-2 rounded-2xl p-4 flex items-start cursor-pointer hover:bg-slate-50 transition-all duration-200 relative"
                                :class="selectedMethod === 'transfer' ? 'border-tenant-primary bg-white' : 'border-slate-100 bg-slate-50/50'">
                                <input type="radio" name="payment_method" value="transfer" x-model="selectedMethod" class="sr-only">
                                <div class="flex h-5 items-center">
                                    <div class="h-4 w-4 rounded-full border border-slate-300 flex items-center justify-center" :class="selectedMethod === 'transfer' ? 'border-tenant-primary bg-tenant-primary' : ''">
                                        <div class="h-1.5 w-1.5 rounded-full bg-white" x-show="selectedMethod === 'transfer'"></div>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <span class="block text-sm font-bold text-slate-800">Transferencia Bancaria</span>
                                    <span class="block text-xs text-slate-400 mt-1">Te mostraremos los datos bancarios para transferir.</span>
                                </div>
                            </label>

                            {{-- Opción 3: Pago Móvil --}}
                            <label class="border-2 rounded-2xl p-4 flex items-start cursor-pointer hover:bg-slate-50 transition-all duration-200 relative"
                                :class="selectedMethod === 'pago_movil' ? 'border-tenant-primary bg-white' : 'border-slate-100 bg-slate-50/50'">
                                <input type="radio" name="payment_method" value="pago_movil" x-model="selectedMethod" class="sr-only">
                                <div class="flex h-5 items-center">
                                    <div class="h-4 w-4 rounded-full border border-slate-300 flex items-center justify-center" :class="selectedMethod === 'pago_movil' ? 'border-tenant-primary bg-tenant-primary' : ''">
                                        <div class="h-1.5 w-1.5 rounded-full bg-white" x-show="selectedMethod === 'pago_movil'"></div>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <span class="block text-sm font-bold text-slate-800">Pago Móvil</span>
                                    <span class="block text-xs text-slate-400 mt-1">Realiza tu pago instantáneo mediante Pago Móvil.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    @if(($exchangeRate ?? 0) > 0)
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Moneda de Pago</label>
                        <div class="grid grid-cols-2 gap-3" x-data="{ selectedCurrency: 'USD' }">
                            <label class="border-2 rounded-2xl p-4 flex items-center cursor-pointer hover:bg-slate-50 transition-all duration-200"
                                :class="selectedCurrency === 'USD' ? 'border-tenant-primary bg-white' : 'border-slate-100 bg-slate-50/50'">
                                <input type="radio" name="currency" value="USD" checked x-model="selectedCurrency" class="sr-only">
                                <div class="h-4 w-4 rounded-full border border-slate-300 flex items-center justify-center mr-3" :class="selectedCurrency === 'USD' ? 'border-tenant-primary bg-tenant-primary' : ''">
                                    <div class="h-1.5 w-1.5 rounded-full bg-white" x-show="selectedCurrency === 'USD'"></div>
                                </div>
                                <div>
                                    <span class="block text-sm font-bold text-slate-800">Dólares (USD)</span>
                                </div>
                            </label>

                            <label class="border-2 rounded-2xl p-4 flex items-center cursor-pointer hover:bg-slate-50 transition-all duration-200"
                                :class="selectedCurrency === 'VES' ? 'border-tenant-primary bg-white' : 'border-slate-100 bg-slate-50/50'">
                                <input type="radio" name="currency" value="VES" x-model="selectedCurrency" class="sr-only">
                                <div class="h-4 w-4 rounded-full border border-slate-300 flex items-center justify-center mr-3" :class="selectedCurrency === 'VES' ? 'border-tenant-primary bg-tenant-primary' : ''">
                                    <div class="h-1.5 w-1.5 rounded-full bg-white" x-show="selectedCurrency === 'VES'"></div>
                                </div>
                                <div>
                                    <span class="block text-sm font-bold text-slate-800">Bolívares (VES)</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Notas o Instrucciones Especiales (Opcional)</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Instrucciones para la entrega o detalles adicionales..."
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="w-full flex items-center justify-center px-4 py-3.5 rounded-xl text-sm font-bold text-white bg-tenant-primary hover:bg-tenant-primary/95 transition-all duration-200 shadow-md">
                        Completar Pedido y Continuar
                    </button>
                </form>
            </div>
        </div>

        {{-- Resumen lateral --}}
        <div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <h2 class="text-sm font-bold text-slate-800 pb-3 border-b border-slate-100">Resumen del Pedido</h2>

                <div class="divide-y divide-slate-100 max-h-[250px] overflow-y-auto pr-1">
                    @foreach($cart as $item)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div class="min-w-0 pr-2">
                                <span class="font-bold text-slate-800 block truncate">{{ $item['name'] }}</span>
                                <span class="text-slate-400 font-medium">Cant: {{ $item['qty'] }} x ${{ number_format($item['price'], 2) }}</span>
                            </div>
                            <span class="font-bold text-slate-800 flex-shrink-0">${{ number_format($item['price'] * $item['qty'], 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>Subtotal</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Envío</span>
                        <span class="text-green-600 font-semibold">Gratis</span>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-baseline">
                        <span class="text-sm font-bold text-slate-800">Total</span>
                        <span class="text-xl font-extrabold text-tenant-primary">${{ number_format($total, 2) }}</span>
                    </div>
                    @if(($exchangeRate ?? 0) > 0)
                    <div class="pt-1 flex justify-between items-baseline text-slate-500">
                        <span class="text-xs">Total en Bolívares</span>
                        <span class="text-sm font-bold text-emerald-600">Bs. {{ number_format($total * $exchangeRate, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
