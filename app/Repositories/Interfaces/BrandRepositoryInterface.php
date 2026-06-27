<?php

namespace App\Repositories\Interfaces;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Brand;

interface BrandRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Buscar una marca por su slug.
     */
    public function findBySlug(string $slug): ?Brand;

    /**
     * Obtener todas las marcas activas.
     */
    public function getActiveBrands(): Collection;
}
