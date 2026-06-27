<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * CategoryRepository constructor.
     *
     * @param Category $model
     */
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    /**
     * Obtener categorías principales (padres, parent_id = null).
     */
    public function getParentCategories(): Collection
    {
        return $this->model->whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Obtener el árbol jerárquico completo de categorías.
     */
    public function getCategoryTree(): Collection
    {
        return $this->model->whereNull('parent_id')
            ->with('children') // Relación definida en Category model
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Buscar una categoría por su slug.
     */
    public function findBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Obtener categorías activas.
     */
    public function getActiveCategories(): Collection
    {
        return $this->model->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();
    }
}
