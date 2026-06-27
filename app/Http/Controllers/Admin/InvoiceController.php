<?php

namespace App\Http\Controllers\Admin;

use App\Core\Tenant\TenantManager;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Genera y descarga el PDF de una cotización/factura del pedido.
     */
    public function download(Order $order)
    {
        $tenant       = TenantManager::getTenant();
        $exchangeRate = (float) $tenant->getSetting('exchange_rate_usd_bs', 0);

        // Cargar relaciones necesarias
        $order->load(['items.product', 'customer']);

        // Convertir logo del tenant a base64 data URI para que dompdf pueda
        // renderizarlo correctamente (dompdf no soporta URLs locales en Windows)
        $logoBase64 = null;
        if ($tenant->logo) {
            $logoPath = storage_path('app/public/' . $tenant->logo);
            if (file_exists($logoPath)) {
                $logoContents = file_get_contents($logoPath);
                $logoMime     = mime_content_type($logoPath) ?: 'image/png';
                $logoBase64   = 'data:' . $logoMime . ';base64,' . base64_encode($logoContents);
            }
        }

        $pdf = Pdf::loadView('admin.invoices.pdf', compact('order', 'tenant', 'exchangeRate', 'logoBase64'))
            ->setPaper('a4', 'portrait');

        $filename = 'pedido-' . $order->order_number . '.pdf';

        return $pdf->download($filename);
    }
}
