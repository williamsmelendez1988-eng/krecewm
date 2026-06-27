<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Core\Tenant\TenantManager;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    /**
     * Crea un nuevo pedido desde el checkout público.
     * Genera número de pedido único, crea items y descuenta stock.
     */
    public function createFromCart(
        array $cartItems,
        array $shippingData,
        string $paymentMethod = 'whatsapp',
        string $currency = 'USD',
        float $exchangeRate = 1.0000
    ): Order {
        return DB::transaction(function () use ($cartItems, $shippingData, $paymentMethod, $currency, $exchangeRate) {
            $tenantId = TenantManager::getTenantId();

            // Calcular total
            $total = collect($cartItems)->sum(fn($i) => $i['price'] * $i['qty']);

            // Resolver o crear cliente si viene email/teléfono
            $customerId = null;
            if (!empty($shippingData['phone'])) {
                $customer = Customer::firstOrCreate(
                    ['tenant_id' => $tenantId, 'phone' => $shippingData['phone']],
                    [
                        'tenant_id' => $tenantId,
                        'name'      => $shippingData['name'],
                        'email'     => $shippingData['email'] ?? null,
                        'phone'     => $shippingData['phone'],
                        'address'   => $shippingData['address'] ?? null,
                        'city'      => $shippingData['city'] ?? null,
                        'status'    => 'active',
                    ]
                );
                $customerId = $customer->id;
            }

            // Crear el pedido
            $order = Order::create([
                'tenant_id'        => $tenantId,
                'customer_id'      => $customerId,
                'order_number'     => $this->generateOrderNumber($tenantId),
                'total'            => $total,
                'status'           => 'pending',
                'shipping_name'    => $shippingData['name'],
                'shipping_phone'   => $shippingData['phone'],
                'shipping_address' => $shippingData['address'] ?? null,
                'shipping_city'    => $shippingData['city'] ?? null,
                'payment_method'   => $paymentMethod,
                'payment_status'   => 'pending',
                'notes'            => $shippingData['notes'] ?? null,
                'currency'         => $currency,
                'exchange_rate'    => $exchangeRate,
            ]);

            // Crear los items del pedido
            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) continue;

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'sku'          => $product->sku,
                    'quantity'     => $item['qty'],
                    'price'        => $item['price'],
                    'total'        => $item['price'] * $item['qty'],
                ]);
            }

            return $order;
        });
    }

    /**
     * Cambia el estado de un pedido.
     * Si pasa a 'confirmed', descuenta el stock de cada item.
     * Si pasa a 'cancelled', devuelve el stock (si fue confirmado antes).
     */
    public function changeStatus(Order $order, string $newStatus, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($order, $newStatus, $notes) {
            $oldStatus = $order->status;

            $order->update([
                'status' => $newStatus,
                'notes'  => $notes ?? $order->notes,
            ]);

            // Al confirmar: descontar stock
            if ($newStatus === 'confirmed' && $oldStatus === 'pending') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $inventory = $item->product->inventory;
                        if ($inventory) {
                            $this->inventoryService->registerMovement(
                                $inventory->id,
                                'out',
                                $item->quantity,
                                'sale',
                                "Venta - Pedido #{$order->order_number}"
                            );
                        }
                    }
                }
            }

            // Al cancelar: devolver stock si estaba confirmado
            if ($newStatus === 'cancelled' && $oldStatus === 'confirmed') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $inventory = $item->product->inventory;
                        if ($inventory) {
                            $this->inventoryService->registerMovement(
                                $inventory->id,
                                'in',
                                $item->quantity,
                                'return',
                                "Devolución - Pedido #{$order->order_number} cancelado"
                            );
                        }
                    }
                }
            }

            return $order->fresh(['items.product', 'customer']);
        });
    }

    /**
     * Genera un número de pedido único por tenant: KW-{tenantId}-{timestamp}-{random}
     */
    private function generateOrderNumber(int $tenantId): string
    {
        do {
            $number = 'KW-' . str_pad($tenantId, 2, '0', STR_PAD_LEFT)
                    . '-' . date('ymd')
                    . '-' . strtoupper(Str::random(4));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
