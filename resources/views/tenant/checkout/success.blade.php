@extends('layouts.tenant')

@section('title', '¡Pedido Recibido! - ' . $tenant->name)

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
    
    {{-- Animación / Ícono de Éxito --}}
    <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-green-50 text-green-500 mb-8 border border-green-200">
        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
    </div>

    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">¡Muchas gracias por tu compra!</h1>
    <p class="mt-3 text-slate-500 text-base">Tu pedido <span class="font-bold text-slate-800">#{{ $order->order_number }}</span> ha sido registrado con éxito.</p>

    {{-- Botón de WhatsApp --}}
    <div class="mt-8 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8 text-center max-w-lg mx-auto">
        <h2 class="text-base font-bold text-slate-800 mb-3">Paso final: Envía el detalle a WhatsApp</h2>
        <p class="text-xs text-slate-500 mb-6 leading-relaxed">Para completar tu compra y coordinar la entrega o confirmar tu pago, haz clic en el botón de abajo. Esto abrirá un chat directo con nuestro asesor con todo el detalle de tu pedido listo para enviar.</p>
        
        <a href="{{ $whatsappUrl }}" target="_blank"
            class="inline-flex w-full items-center justify-center px-6 py-3.5 rounded-xl bg-green-500 hover:bg-green-400 text-sm font-bold text-white transition-all duration-200 shadow-lg shadow-green-500/20 hover:-translate-y-0.5 gap-2">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                <path d="M12 0C5.374 0 0 5.373 0 12c0 2.117.549 4.099 1.508 5.819L0 24l6.335-1.652A11.962 11.962 0 0012 24c6.626 0 12-5.373 12-12S18.626 0 12 0zm0 22c-1.885 0-3.653-.49-5.192-1.349L2.9 21.89l1.272-3.791A9.972 9.972 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" fill-rule="evenodd" clip-rule="evenodd"/>
            </svg>
            Enviar Pedido a WhatsApp
        </a>
        <div class="mt-4">
            <a href="{{ route('tenant.checkout.pdf', $order->id) }}"
                class="inline-flex w-full items-center justify-center px-6 py-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm font-bold text-slate-700 transition-all duration-200 shadow-sm gap-2">
                <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Ver / Descargar Cotización PDF
            </a>
        </div>
    </div>

    {{-- Instrucciones de Transferencia Bancaria --}}
    @if($order->payment_method === 'transfer')
        <div class="mt-6 bg-slate-50 border border-slate-200 rounded-2xl p-6 text-left max-w-lg mx-auto">
            <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-1.5">
                <svg class="h-5 w-5 text-tenant-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Instrucciones de Transferencia Bancaria
            </h3>
            <p class="text-xs text-slate-600 leading-relaxed mb-4">Por favor realiza la transferencia electrónica a la siguiente cuenta y envía el comprobante adjunto cuando chatees por WhatsApp:</p>
            <div class="bg-white rounded-xl p-4 border border-slate-150 text-xs text-slate-700 whitespace-pre-line">
                @if($tenant->getSetting('bank_transfer_info'))
                    {{ $tenant->getSetting('bank_transfer_info') }}
                @else
                    Banco: Bancolombia
                    Tipo: Ahorros
                    Número: 123-456789-01
                    Titular: {{ $tenant->name }}
                @endif
                <div class="mt-2 font-mono font-bold text-slate-900"><span class="text-slate-500 font-semibold font-sans">Valor exacto:</span> ${{ number_format($order->total, 2) }}</div>
            </div>
        </div>
    @endif

    {{-- Instrucciones de Pago Móvil --}}
    @if($order->payment_method === 'pago_movil')
        <div class="mt-6 bg-slate-50 border border-slate-200 rounded-2xl p-6 text-left max-w-lg mx-auto">
            <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-1.5">
                <svg class="h-5 w-5 text-tenant-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Instrucciones de Pago Móvil (Venezuela)
            </h3>
            <p class="text-xs text-slate-600 leading-relaxed mb-4">Por favor realiza el Pago Móvil con los siguientes datos y envía el capture de pantalla del comprobante cuando chatees por WhatsApp:</p>
            <div class="bg-white rounded-xl p-4 border border-slate-150 font-mono text-xs space-y-1.5 text-slate-700">
                <div><span class="font-bold text-slate-500">Banco:</span> {{ $tenant->getSetting('pago_movil_bank') ?: 'Banco de Venezuela (0102)' }}</div>
                <div><span class="font-bold text-slate-500">Teléfono:</span> {{ $tenant->getSetting('pago_movil_phone') ?: $tenant->contact_phone }}</div>
                <div><span class="font-bold text-slate-500">Cédula / RIF:</span> {{ $tenant->getSetting('pago_movil_id') ?: 'V-12345678' }}</div>
                <div><span class="font-bold text-slate-500">Monto exacto:</span> ${{ number_format($order->total, 2) }} (o al cambio oficial del día)</div>
            </div>
        </div>
    @endif

    {{-- Detalles del Pedido --}}
    <div class="mt-8 border-t border-slate-100 pt-8 max-w-lg mx-auto text-left">
        <h3 class="text-sm font-bold text-slate-800 mb-4">Detalle del Pedido</h3>
        <div class="divide-y divide-slate-100 bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
            @foreach($order->items as $item)
                <div class="py-3 flex justify-between text-xs">
                    <span class="text-slate-600"><span class="font-bold text-slate-800">{{ $item->quantity }}x</span> {{ $item->product_name }}</span>
                    <span class="font-bold text-slate-800">${{ number_format($item->total, 2) }}</span>
                </div>
            @endforeach
            <div class="pt-3 flex justify-between text-sm font-extrabold text-slate-800">
                <span>Total</span>
                <span class="text-tenant-primary">${{ number_format($order->total, 2) }}</span>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <a href="{{ route('catalog.index') }}" class="text-xs font-semibold text-slate-500 hover:text-tenant-primary transition-colors">
                ← Volver al inicio
            </a>
        </div>
    </div>

</div>
@endsection
