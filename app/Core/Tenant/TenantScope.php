<?php

namespace App\Core\Tenant;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Aplica el scope a la consulta del builder de Eloquent.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (TenantManager::hasTenant()) {
            $builder->where($model->getTable() . '.tenant_id', TenantManager::getTenantId());
        }
    }
}
