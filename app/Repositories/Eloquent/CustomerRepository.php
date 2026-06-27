<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Core\Tenant\TenantManager;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function allForTenant(): Collection
    {
        return Customer::orderBy('name')->get();
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return Customer::withCount('orders')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function find(int $id): ?Customer
    {
        return Customer::find($id);
    }

    public function findByPhone(string $phone): ?Customer
    {
        return Customer::where('phone', $phone)->first();
    }

    public function findByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    public function create(array $data): Customer
    {
        $data['tenant_id'] = TenantManager::getTenantId();
        return Customer::create($data);
    }

    public function update(int $id, array $data): Customer
    {
        $customer = Customer::findOrFail($id);
        $customer->update($data);
        return $customer->fresh();
    }

    public function delete(int $id): bool
    {
        return Customer::findOrFail($id)->delete();
    }
}
