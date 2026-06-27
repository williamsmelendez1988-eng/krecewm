<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $q = Order::with(['customer', 'items'])
            ->orderByDesc('created_at');

        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $q->where(function ($sub) use ($filters) {
                $sub->where('order_number', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('shipping_name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('shipping_phone', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $q->paginate($perPage);
    }

    public function find(int $id): ?Order
    {
        return Order::find($id);
    }

    public function findWithItems(int $id): ?Order
    {
        return Order::with(['customer', 'items.product'])->find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function updateStatus(int $id, string $status): Order
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $status]);
        return $order->fresh();
    }

    public function countByStatus(string $status): int
    {
        return Order::where('status', $status)->count();
    }

    public function recentOrders(int $limit = 5): Collection
    {
        return Order::with('customer')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
