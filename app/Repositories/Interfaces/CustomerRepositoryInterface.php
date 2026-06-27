<?php

namespace App\Repositories\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CustomerRepositoryInterface
{
    public function allForTenant(): Collection;
    public function paginate(int $perPage = 20): LengthAwarePaginator;
    public function find(int $id): ?\App\Models\Customer;
    public function findByPhone(string $phone): ?\App\Models\Customer;
    public function findByEmail(string $email): ?\App\Models\Customer;
    public function create(array $data): \App\Models\Customer;
    public function update(int $id, array $data): \App\Models\Customer;
    public function delete(int $id): bool;
}
