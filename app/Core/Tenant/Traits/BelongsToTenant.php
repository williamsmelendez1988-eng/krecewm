<?php

namespace App\Core\Tenant\Traits;

use App\Core\Tenant\TenantManager;
use App\Core\Tenant\TenantScope;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    /**
     * Boot del trait para registrar el Global Scope y los hooks de guardado.
     *
     * @return void
     */
    protected static function bootBelongsToTenant(): void
    {
        // Aplicar el filtro automático de lectura
        static::addGlobalScope(new TenantScope);

        // Inyectar el tenant_id automáticamente al crear nuevos registros
        static::creating(function ($model) {
            if (TenantManager::hasTenant()) {
                if (empty($model->tenant_id)) {
                    $model->tenant_id = TenantManager::getTenantId();
                }
            }
        });
    }

    /**
     * Relación con el Tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
