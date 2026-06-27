<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Obtener todos los registros.
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Obtener registros paginados.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Buscar un registro por su ID.
     */
    public function find(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Crear un nuevo registro.
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * Actualizar un registro existente.
     */
    public function update(int|string $id, array $attributes): bool
    {
        $record = $this->find($id);

        if (!$record) {
            return false;
        }

        return $record->update($attributes);
    }

    /**
     * Eliminar un registro.
     */
    public function delete(int|string $id): bool
    {
        $record = $this->find($id);

        if (!$record) {
            return false;
        }

        return $record->delete();
    }
}
