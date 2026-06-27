<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Obtener todos los registros.
     */
    public function all(): Collection;

    /**
     * Obtener registros paginados.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Buscar un registro por su ID.
     */
    public function find(int|string $id): ?Model;

    /**
     * Crear un nuevo registro.
     */
    public function create(array $attributes): Model;

    /**
     * Actualizar un registro existente.
     */
    public function update(int|string $id, array $attributes): bool;

    /**
     * Eliminar un registro.
     */
    public function delete(int|string $id): bool;
}
