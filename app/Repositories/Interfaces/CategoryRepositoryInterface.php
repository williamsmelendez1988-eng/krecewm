<?php

namespace App\Repositories\Interfaces;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Category;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Obtener categorías principales (padres, parent_id = null).
     */
    public function getParentCategories(): Collection;

    /**
     * Obtener el árbol jerárquico completo de categorías.
     */
    public function getCategoryTree(): Collection;

    /**
     * Buscar una categoría por su slug.
     */
    public function findBySlug(string $slug): ?Category;

    /**
     * Obtener categorías activas.
     */
    public function getActiveCategories(): Collection;
}
