<?php

namespace App\Providers;

use App\Core\Tenant\TenantManager;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Interfaces\BrandRepositoryInterface;
use App\Repositories\Eloquent\BrandRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Eloquent\OrderRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 0. View Composer Global: comparte $tenant con todas las vistas del layout público de tienda
        View::composer(['layouts.tenant', 'tenant.*'], function ($view) {
            if (TenantManager::hasTenant()) {
                $view->with('tenant', TenantManager::getTenant());
            }
        });

        // 1. Gate para Super Administrador global
        Gate::define('access-superadmin', function (User $user) {
            return $user->isSuperAdmin() && is_null($user->tenant_id);
        });

        // 2. Gate para Administrador de Tienda (Owner / Manager)
        Gate::define('access-admin', function (User $user) {
            if (!TenantManager::hasTenant()) {
                return false;
            }
            // Debe pertenecer al tenant activo y tener rol de admin o staff
            return $user->tenant_id === TenantManager::getTenantId() && 
                   ($user->isTenantAdmin() || $user->isTenantStaff());
        });

        // 3. Gate específico para modificar configuraciones (Solo Tenant Owner / Admin)
        Gate::define('manage-settings', function (User $user) {
            if (!TenantManager::hasTenant()) {
                return false;
            }
            return $user->tenant_id === TenantManager::getTenantId() && $user->isTenantAdmin();
        });

        // 4. Gate específico para gestionar usuarios del personal
        Gate::define('manage-staff', function (User $user) {
            if (!TenantManager::hasTenant()) {
                return false;
            }
            return $user->tenant_id === TenantManager::getTenantId() && $user->isTenantAdmin();
        });
    }
}
