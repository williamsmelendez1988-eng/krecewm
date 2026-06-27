<?php

namespace App\Repositories\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator;
    public function find(int $id): ?\App\Models\Order;
    public function findWithItems(int $id): ?\App\Models\Order;
    public function create(array $data): \App\Models\Order;
    public function updateStatus(int $id, string $status): \App\Models\Order;
    public function countByStatus(string $status): int;
    public function recentOrders(int $limit = 5): Collection;
}
