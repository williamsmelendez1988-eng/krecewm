<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Core\Tenant\Traits\BelongsToTenant;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['tenant_id', 'name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, BelongsToTenant, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Verifica si el usuario es Super Admin de KreceWM.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin' && is_null($this->tenant_id);
    }

    /**
     * Verifica si el usuario es Administrador del Negocio (Tenant Owner).
     *
     * @return bool
     */
    public function isTenantAdmin(): bool
    {
        return $this->role === 'tenant_admin' && !is_null($this->tenant_id);
    }

    /**
     * Verifica si el usuario es Personal del Negocio (Staff).
     *
     * @return bool
     */
    public function isTenantStaff(): bool
    {
        return $this->role === 'tenant_staff' && !is_null($this->tenant_id);
    }

    /**
     * Verifica si el usuario tiene alguno de los roles indicados.
     *
     * @param  array|string  $roles
     * @return bool
     */
    public function hasRole(array|string $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        // Si el rol es superadmin y el usuario es superadmin, retornar true
        if (in_array('superadmin', $roles) && $this->isSuperAdmin()) {
            return true;
        }

        // Si el rol es tenant_admin (equivalente a owner) y es admin de tienda
        if (in_array('tenant_admin', $roles) && $this->isTenantAdmin()) {
            return true;
        }

        // Si el rol es tenant_staff (empleados/gestores)
        if (in_array('tenant_staff', $roles) && $this->isTenantStaff()) {
            return true;
        }

        // Mapeos heredados solicitados:
        // 'manager' y 'employee' se mapean sobre 'tenant_admin' y 'tenant_staff'
        if (in_array('manager', $roles) && ($this->isTenantAdmin() || $this->role === 'tenant_staff')) {
            return true;
        }
        if (in_array('employee', $roles) && $this->role === 'tenant_staff') {
            return true;
        }

        return false;
    }
}
