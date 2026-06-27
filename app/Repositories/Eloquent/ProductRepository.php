<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    /**
     * ProductRepository constructor.
     *
     * @param Product $model
     */
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Buscar un producto por su slug.
     */
    public function findBySlug(string $slug): ?Product
    {
        return $this->model->where('slug', $slug)
            ->with(['category', 'brand', 'inventory'])
            ->first();
    }

    /**
     * Buscar un producto por su SKU.
     */
    public function findBySku(string $sku): ?Product
    {
        return $this->model->where('sku', $sku)
            ->with(['inventory'])
            ->first();
    }

    /**
     * Obtener productos destacados.
     */
    public function getFeaturedProducts(int $limit = 8): Collection
    {
        return $this->model->where('status', 'active')
            ->where('is_featured', true)
            ->with(['category', 'brand', 'inventory'])
            ->limit($limit)
            ->get();
    }

    /**
     * Buscar y filtrar productos en el catálogo de administración.
     */
    public function searchAndFilterAdmin(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['category', 'brand', 'inventory']);

        // Filtro por término de búsqueda (nombre, sku, código interno)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('internal_code', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filtro por marca
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // Filtro por estado
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtro por stock
        if (!empty($filters['stock_status'])) {
            $status = $filters['stock_status'];
            $query->whereHas('inventory', function ($q) use ($status) {
                if ($status === 'out') {
                    $q->where('quantity', '<=', 0);
                } elseif ($status === 'low') {
                    $q->whereRaw('quantity <= min_stock AND quantity > 0');
                } elseif ($status === 'normal') {
                    $q->whereRaw('quantity > min_stock');
                }
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Buscar y filtrar productos en el catálogo público.
     */
    public function searchAndFilterPublic(array $filters, int $perPage = 16): LengthAwarePaginator
    {
        // Solo productos activos
        $query = $this->model->newQuery()
            ->where('status', 'active')
            ->with(['category', 'brand', 'inventory']);

        // Búsqueda por texto
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Búsqueda por slug de categoría
        if (!empty($filters['category_slug'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('slug', $filters['category_slug'])
                  ->where('status', 'active');
            });
        }

        // Búsqueda por slug de marca
        if (!empty($filters['brand_slug'])) {
            $query->whereHas('brand', function ($q) use ($filters) {
                $q->where('slug', $filters['brand_slug'])
                  ->where('status', 'active');
            });
        }

        // Rango de precio
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Ordenamiento
        $orderBy = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';

        if ($orderBy === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($orderBy === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($orderBy === 'name_asc') {
            $query->orderBy('name', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }
}
