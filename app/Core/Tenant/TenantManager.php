<?php

namespace App\Core\Tenant;

use App\Models\Tenant;

class TenantManager
{
    /**
     * El tenant actualmente resuelto.
     *
     * @var \App\Models\Tenant|null
     */
    protected static ?Tenant $currentTenant = null;

    /**
     * Establece el tenant activo en memoria.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return void
     */
    public static function setTenant(Tenant $tenant): void
    {
        self::$currentTenant = $tenant;
    }

    /**
     * Obtiene el tenant activo.
     *
     * @return \App\Models\Tenant|null
     */
    public static function getTenant(): ?Tenant
    {
        return self::$currentTenant;
    }

    /**
     * Obtiene el ID del tenant activo.
     *
     * @return int|string|null
     */
    public static function getTenantId(): int|string|null
    {
        return self::$currentTenant ? self::$currentTenant->id : null;
    }

    /**
     * Verifica si hay un tenant resuelto en la sesión/petición.
     *
     * @return bool
     */
    public static function hasTenant(): bool
    {
        return !is_null(self::$currentTenant);
    }

    /**
     * Limpia el tenant activo en memoria.
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$currentTenant = null;
    }
}
