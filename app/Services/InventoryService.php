<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Registrar un movimiento de stock y actualizar la cantidad actual del inventario.
     */
    public function registerMovement(
        int $inventoryId,
        string $type,
        int $quantity,
        string $reason,
        ?string $description = null,
        ?int $userId = null,
        ?int $tenantId = null
    ): InventoryMovement {
        if ($quantity <= 0) {
            throw new Exception("La cantidad del movimiento debe ser mayor a cero.");
        }

        if (!in_array($type, ['in', 'out'])) {
            throw new Exception("El tipo de movimiento debe ser 'in' (entrada) o 'out' (salida).");
        }

        $validReasons = ['purchase', 'sale', 'adjustment', 'damage', 'return'];
        if (!in_array($reason, $validReasons)) {
            throw new Exception("El motivo del movimiento no es válido.");
        }

        return DB::transaction(function () use ($inventoryId, $type, $quantity, $reason, $description, $userId, $tenantId) {
            // 1. Obtener y bloquear el inventario para evitar condiciones de carrera (race conditions)
            $inventory = Inventory::where('id', $inventoryId)->lockForUpdate()->first();
            if (!$inventory) {
                throw new Exception("El registro de inventario especificado no existe.");
            }

            // Aislamos el tenant si no viene definido en la petición actual
            $resolvedTenantId = $tenantId ?? $inventory->tenant_id;

            // 2. Calcular nuevo stock
            $currentStock = $inventory->quantity;
            $newStock = $currentStock;

            if ($type === 'in') {
                $newStock += $quantity;
            } else {
                $newStock -= $quantity;
                // Si es una venta y no hay stock suficiente, lanzamos excepción
                if ($reason === 'sale' && $newStock < 0) {
                    throw new Exception("Stock insuficiente para realizar esta venta. Stock disponible: {$currentStock}.");
                }
            }

            // 3. Actualizar la cantidad en la tabla inventories
            $inventory->update([
                'quantity' => $newStock
            ]);

            // Disparar alerta si el stock baja del mínimo y es una salida de inventario
            if ($type === 'out' && $newStock <= $inventory->min_stock) {
                try {
                    $inventory->load('product');
                    $adminUsers = \App\Models\User::whereIn('role', ['admin', 'superadmin'])->get();
                    foreach ($adminUsers as $admin) {
                        $admin->notify(new \App\Notifications\LowStockNotification($inventory));
                    }
                } catch (\Exception $e) {
                    // Silently ignore notification failure
                }
            }

            // 4. Crear el registro histórico del movimiento (Kárdex)
            $movement = InventoryMovement::create([
                'tenant_id' => $resolvedTenantId,
                'inventory_id' => $inventoryId,
                'user_id' => $userId,
                'type' => $type,
                'quantity' => $quantity,
                'reason' => $reason,
                'description' => $description
            ]);

            return $movement;
        });
    }

    /**
     * Obtener productos que están bajo el stock mínimo (alertas críticas).
     */
    public function getLowStockProducts(?int $limit = null)
    {
        $query = Inventory::whereRaw('quantity <= min_stock')
            ->with(['product'])
            ->orderBy('quantity', 'asc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }
}
