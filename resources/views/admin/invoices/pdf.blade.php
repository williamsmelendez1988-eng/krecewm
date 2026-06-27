<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido {{ $order->order_number }} — {{ $tenant->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.5;
        }

        /* ── Header ── */
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            padding: 28px 36px;
            margin-bottom: 0;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .brand-name {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #f8fafc;
        }
        .brand-sub {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
        }
        .doc-title {
            text-align: right;
        }
        .doc-type {
            font-size: 20px;
            font-weight: 700;
            color: #f8fafc;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .doc-number {
            font-size: 14px;
            color: #60a5fa;
            font-weight: 600;
            margin-top: 4px;
        }
        .doc-date {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* ── Status Badge ── */
        .status-bar {
            background: #f8fafc;
            border-bottom: 3px solid #e2e8f0;
            padding: 10px 36px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pending    { background: #fef3c7; color: #92400e; border: 1px solid #fbbf24; }
        .status-confirmed  { background: #dbeafe; color: #1e40af; border: 1px solid #3b82f6; }
        .status-processing { background: #ede9fe; color: #5b21b6; border: 1px solid #7c3aed; }
        .status-shipped    { background: #cffafe; color: #155e75; border: 1px solid #06b6d4; }
        .status-delivered  { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        .status-cancelled  { background: #ffe4e6; color: #9f1239; border: 1px solid #f43f5e; }
        .payment-info {
            font-size: 10px;
            color: #64748b;
        }
        .payment-method {
            font-weight: 700;
            color: #1e293b;
        }

        /* ── Parties ── */
        .parties {
            display: flex;
            gap: 20px;
            padding: 24px 36px;
            background: #ffffff;
        }
        .party-box {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
        }
        .party-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 6px;
        }
        .party-name {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .party-detail {
            font-size: 11px;
            color: #475569;
            line-height: 1.6;
        }

        /* ── Items Table ── */
        .table-section {
            padding: 0 36px;
        }
        .table-header-row {
            background: #1e293b;
            color: white;
            padding: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead th {
            background: #1e293b;
            color: white;
            padding: 10px 14px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        thead th:last-child { text-align: right; }
        thead th.center { text-align: center; }

        tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td {
            padding: 10px 14px;
            font-size: 11px;
            color: #374151;
        }
        tbody td.right { text-align: right; }
        tbody td.center { text-align: center; }
        .product-name { font-weight: 600; color: #1e293b; }
        .product-sku  { font-size: 9px; color: #94a3b8; margin-top: 2px; }

        /* ── Totales ── */
        .totals-section {
            padding: 20px 36px;
            display: flex;
            justify-content: flex-end;
        }
        .totals-box {
            width: 220px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 14px;
            font-size: 11px;
            border-bottom: 1px solid #f1f5f9;
        }
        .totals-row:last-child { border-bottom: none; }
        .totals-row.grand {
            background: #1e293b;
            color: white;
            font-weight: 700;
            font-size: 14px;
            padding: 12px 14px;
        }
        .totals-row .label { color: #64748b; }
        .totals-row.grand .label { color: #94a3b8; }

        /* ── Pago Móvil / Instrucciones ── */
        .payment-details {
            margin: 0 36px 24px;
            border-radius: 8px;
            overflow: hidden;
        }
        .payment-details-header {
            background: #0f172a;
            color: white;
            padding: 10px 16px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .payment-details-body {
            border: 1px solid #e2e8f0;
            border-top: none;
            padding: 14px 16px;
            font-size: 11px;
            color: #374151;
            line-height: 1.8;
            background: #f8fafc;
        }

        /* ── Footer ── */
        .footer {
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
            padding: 16px 36px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            margin-top: 20px;
        }
        .footer-brand {
            font-weight: 700;
            color: #1e293b;
        }
        .footer-powered {
            margin-top: 4px;
            font-size: 9px;
        }
    </style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-top">
            <div>
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="{{ $tenant->name }}" style="max-height: 48px; max-width: 180px; margin-bottom: 6px;">
                @endif
                <div class="brand-name">{{ $tenant->name }}</div>
                <div class="brand-sub">
                    {{ $tenant->city ?? '' }}{{ $tenant->city && $tenant->contact_phone ? ' · ' : '' }}{{ $tenant->contact_phone ?? '' }}
                    @if($tenant->contact_email)<br>{{ $tenant->contact_email }}@endif
                </div>
            </div>
            <div class="doc-title">
                <div class="doc-type">Cotización / Pedido</div>
                <div class="doc-number">{{ $order->order_number }}</div>
                <div class="doc-date">Fecha: {{ $order->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    {{-- ── STATUS BAR ── --}}
    @php
        $statusMap = [
            'pending'    => ['label' => 'Pendiente',    'class' => 'status-pending'],
            'confirmed'  => ['label' => 'Confirmado',   'class' => 'status-confirmed'],
            'processing' => ['label' => 'En Proceso',   'class' => 'status-processing'],
            'shipped'    => ['label' => 'Enviado',      'class' => 'status-shipped'],
            'delivered'  => ['label' => 'Entregado',    'class' => 'status-delivered'],
            'cancelled'  => ['label' => 'Cancelado',    'class' => 'status-cancelled'],
        ];
        $st = $statusMap[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'status-confirmed'];

        $paymentLabels = [
            'whatsapp'        => 'Pedido por WhatsApp',
            'pago_movil'      => 'Pago Móvil',
            'transfer'        => 'Transferencia Bancaria',
            'efectivo'        => 'Efectivo',
            'cash_on_delivery'=> 'Contra Entrega',
        ];
        $paymentLabel = $paymentLabels[$order->payment_method] ?? ucfirst($order->payment_method);
    @endphp
    <div class="status-bar">
        <div>
            Estado: <span class="status-badge {{ $st['class'] }}">{{ $st['label'] }}</span>
        </div>
        <div class="payment-info">
            Método de Pago: <span class="payment-method">{{ $paymentLabel }}</span>
        </div>
    </div>

    {{-- ── PARTIES ── --}}
    <div class="parties">
        {{-- Emisor --}}
        <div class="party-box">
            <div class="party-label">🏪 Vendedor</div>
            <div class="party-name">{{ $tenant->name }}</div>
            <div class="party-detail">
                @if($tenant->address){{ $tenant->address }}<br>@endif
                @if($tenant->city){{ $tenant->city }}<br>@endif
                @if($tenant->contact_email){{ $tenant->contact_email }}<br>@endif
                @if($tenant->contact_phone)Tel: {{ $tenant->contact_phone }}@endif
            </div>
        </div>

        {{-- Receptor --}}
        <div class="party-box">
            <div class="party-label">👤 Cliente</div>
            <div class="party-name">{{ $order->shipping_name }}</div>
            <div class="party-detail">
                @if($order->shipping_phone)Tel: {{ $order->shipping_phone }}<br>@endif
                @if($order->shipping_address){{ $order->shipping_address }}<br>@endif
                @if($order->shipping_city){{ $order->shipping_city }}@endif
                @if($order->customer && $order->customer->email)<br>{{ $order->customer->email }}@endif
            </div>
        </div>
    </div>

    {{-- ── TABLA DE PRODUCTOS ── --}}
    <div class="table-section">
        <table>
            <thead>
                <tr>
                    <th style="width:40%">Producto</th>
                    <th class="center" style="width:10%">Cant.</th>
                    <th class="right" style="width:20%">P. Unit. (USD)</th>
                    @if($exchangeRate > 0)
                    <th class="right" style="width:20%">P. Unit. (Bs)</th>
                    @endif
                    <th class="right" style="width:20%">Subtotal (USD)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="product-name">{{ $item->product_name }}</div>
                        @if($item->product && $item->product->sku)
                            <div class="product-sku">SKU: {{ $item->product->sku }}</div>
                        @endif
                    </td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">${{ number_format($item->price, 2) }}</td>
                    @if($exchangeRate > 0)
                    <td class="right">Bs. {{ number_format($item->price * $exchangeRate, 2) }}</td>
                    @endif
                    <td class="right">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── TOTALES ── --}}
    <div class="totals-section">
        <div class="totals-box">
            <div class="totals-row">
                <span class="label">Subtotal:</span>
                <span>${{ number_format($order->total, 2) }}</span>
            </div>
            <div class="totals-row">
                <span class="label">Descuento:</span>
                <span>$0.00</span>
            </div>
            <div class="totals-row grand">
                <span class="label">TOTAL USD:</span>
                <span>${{ number_format($order->total, 2) }}</span>
            </div>
            @if($exchangeRate > 0)
            <div class="totals-row" style="background:#f0fdf4; border-top: 1px solid #bbf7d0;">
                <span style="color:#065f46; font-size:10px;">TOTAL Bs. ({{ number_format($exchangeRate, 2) }}):</span>
                <span style="color:#065f46; font-weight:700;">Bs. {{ number_format($order->total * $exchangeRate, 2) }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── INSTRUCCIONES DE PAGO ── --}}
    @php
        $pagoMovilBank  = $tenant->getSetting('pago_movil_bank', '');
        $pagoMovilPhone = $tenant->getSetting('pago_movil_phone', '');
        $pagoMovilId    = $tenant->getSetting('pago_movil_id', '');
        $bankTransfer   = $tenant->getSetting('bank_transfer_info', '');
        $whatsapp       = $tenant->getSetting('whatsapp_number', '');
    @endphp

    @if($order->payment_method === 'pago_movil' && ($pagoMovilBank || $pagoMovilPhone))
    <div class="payment-details">
        <div class="payment-details-header">📱 Datos para Pago Móvil</div>
        <div class="payment-details-body">
            @if($pagoMovilBank)<strong>Banco:</strong> {{ $pagoMovilBank }}<br>@endif
            @if($pagoMovilPhone)<strong>Teléfono:</strong> {{ $pagoMovilPhone }}<br>@endif
            @if($pagoMovilId)<strong>Cédula / RIF:</strong> {{ $pagoMovilId }}<br>@endif
            @if($exchangeRate > 0)<strong>Monto a pagar:</strong> Bs. {{ number_format($order->total * $exchangeRate, 2) }}@endif
        </div>
    </div>
    @elseif($order->payment_method === 'transfer' && $bankTransfer)
    <div class="payment-details">
        <div class="payment-details-header">🏦 Datos de Transferencia Bancaria</div>
        <div class="payment-details-body">{{ $bankTransfer }}</div>
    </div>
    @elseif($whatsapp)
    <div class="payment-details">
        <div class="payment-details-header">💬 Coordinación por WhatsApp</div>
        <div class="payment-details-body">
            Contáctenos al <strong>+{{ $whatsapp }}</strong> para coordinar el pago y la entrega de su pedido.
        </div>
    </div>
    @endif

    @if($order->notes)
    <div class="payment-details" style="margin-top: 0;">
        <div class="payment-details-header">📝 Notas del Pedido</div>
        <div class="payment-details-body">{{ $order->notes }}</div>
    </div>
    @endif

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-brand">{{ $tenant->name }}</div>
        <div>
            Este documento es una cotización generada digitalmente. No constituye factura fiscal.
        </div>
        <div class="footer-powered">
            Generado con <strong>KreceWM</strong> · Plataforma SaaS de Digitalización Comercial
        </div>
    </div>

</body>
</html>
