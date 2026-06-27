<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Order $order) {}

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
        return [
            'type'         => 'new_order',
            'icon'         => '🛒',
            'title'        => 'Nuevo Pedido Recibido',
            'message'      => "Pedido #{$this->order->order_number} de {$this->order->shipping_name} por \${$this->order->total}",
            'action_url'   => '/admin/orders/' . $this->order->id,
            'action_label' => 'Ver Pedido',
            'order_id'     => $this->order->id,
        ];
    }
}
