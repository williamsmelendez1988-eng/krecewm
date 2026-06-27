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

        $pdf = Pdf::loadView('admin.invoices.pdf', compact('order', 'tenant', 'exchangeRate'))
            ->setPaper('a4', 'portrait');

        $filename = 'pedido-' . $order->order_number . '.pdf';

        return $pdf->download($filename);
    }
}
