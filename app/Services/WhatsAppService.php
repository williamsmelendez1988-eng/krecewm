<?php

namespace App\Services;

use App\Models\Order;
use App\Core\Tenant\TenantManager;

class WhatsAppService
{
    /**
     * Generates a wa.me link for a given order.
     */
    public function generateOrderLink(Order $order): string
    {
        $tenant = TenantManager::getTenant();
        
        // Fetch phone number from tenant config settings first, then fallback to tenant contact phone
        $phone = null;
        if ($tenant) {
            $phone = $tenant->getSetting('whatsapp_number') ?: ($tenant->getSetting('whatsapp_phone') ?: $tenant->contact_phone);
        }

        // Clean phone number (keep only digits)
        $phone = preg_replace('/\D/', '', $phone);
        
        // Default to a fallback if none configured
        if (empty($phone)) {
            $phone = '584120000000'; // Default Venezuela code or placeholder
        }

        // Ensure country code is present (e.g. if length is 10, default to prefix 58 for VE since we clean spaces/non-digits)
        if (strlen($phone) === 10) {
            $phone = '58' . $phone;
        }

        $businessName = $tenant ? $tenant->name : 'KW Store';

        // Build message
        $message = "*¡Hola! Nuevo Pedido desde la Tienda Virtual*\n\n";
        $message .= "*Detalles de la Orden:*\n";
        $message .= "• *Pedido:* #{$order->order_number}\n";
        $message .= "• *Cliente:* {$order->shipping_name}\n";
        $message .= "• *Teléfono:* {$order->shipping_phone}\n";
        
        if ($order->shipping_address) {
            $message .= "• *Dirección:* {$order->shipping_address}";
            if ($order->shipping_city) {
                $message .= ", {$order->shipping_city}";
            }
            $message .= "\n";
        }
        
        $message .= "• *Método de Pago:* " . strtoupper($order->payment_method) . "\n";
        if ($order->currency && $order->currency !== 'USD') {
            $message .= "• *Moneda Seleccionada:* {$order->currency}\n";
        }
        $message .= "\n";
        
        $message .= "*Productos:*\n";
        foreach ($order->items as $item) {
            if ($order->currency === 'VES' && $order->exchange_rate > 1) {
                $unitBs = $item->price * $order->exchange_rate;
                $totalBs = $item->total * $order->exchange_rate;
                $message .= "- {$item->quantity}x {$item->product_name} (Bs. " . number_format($unitBs, 2) . ") = Bs. " . number_format($totalBs, 2) . "\n";
            } else {
                $message .= "- {$item->quantity}x {$item->product_name} ($" . number_format($item->price, 2) . ") = $" . number_format($item->total, 2) . "\n";
            }
        }
        
        if ($order->currency === 'VES' && $order->exchange_rate > 1) {
            $totalBs = $order->total * $order->exchange_rate;
            $message .= "\n*Total a Pagar:* Bs. " . number_format($totalBs, 2) . " (o $" . number_format($order->total, 2) . ")\n";
        } else {
            $message .= "\n*Total a Pagar:* $" . number_format($order->total, 2) . "\n";
        }
        
        if ($order->notes) {
            $message .= "\n*Notas:* {$order->notes}\n";
        }

        $message .= "\n_Por favor, confírmeme los detalles de entrega y pago._";

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }
}
