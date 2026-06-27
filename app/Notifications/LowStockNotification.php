<?php

namespace App\Notifications;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Inventory $inventory) {}

    /**
     * Los canales por donde se enviará la notificación.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Los datos que se guardarán en la tabla notifications.
     */
    public function toDatabase(object $notifiable): array
    {
        $productName = $this->inventory->product->name ?? 'Producto';
        $qty         = $this->inventory->quantity;
        $minStock    = $this->inventory->min_stock;

        return [
            'type'         => 'low_stock',
            'icon'         => '⚠️',
            'title'        => 'Stock Crítico',
            'message'      => "{$productName}: solo {$qty} disponibles (mínimo: {$minStock})",
            'action_url'   => '/admin/inventory',
            'action_label' => 'Ver Inventario',
            'product_id'   => $this->inventory->product_id,
        ];
    }
}
