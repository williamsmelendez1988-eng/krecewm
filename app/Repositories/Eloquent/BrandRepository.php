<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\Interfaces\BrandRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    /**
     * BrandRepository constructor.
     *
     * @param Brand $model
     */
    public function __construct(Brand $model)
    {
        parent::__construct($model);
    }

    /**
     * Buscar una marca por su slug.
     */
    public function findBySlug(string $slug): ?Brand
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Obtener todas las marcas activas.
     */
    public function getActiveBrands(): Collection
    {
        return $this->model->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();
    }
}
