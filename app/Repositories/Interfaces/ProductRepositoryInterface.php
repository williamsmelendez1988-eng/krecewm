<?php

namespace App\Repositories\Interfaces;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Buscar un producto por su slug.
     */
    public function findBySlug(string $slug): ?Product;

    /**
     * Buscar un producto por su SKU.
     */
    public function findBySku(string $sku): ?Product;

    /**
     * Obtener productos destacados.
     */
    public function getFeaturedProducts(int $limit = 8): Collection;

    /**
     * Buscar y filtrar productos en el catálogo de administración.
     */
    public function searchAndFilterAdmin(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Buscar y filtrar productos en el catálogo público.
     */
    public function searchAndFilterPublic(array $filters, int $perPage = 16): LengthAwarePaginator;
}
