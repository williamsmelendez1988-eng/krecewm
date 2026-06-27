<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\SuperAdmin\TenantController as SuperAdminTenant;
use App\Http\Controllers\Admin\DashboardController as TenantDashboard;
use App\Http\Controllers\Admin\SettingController as TenantSetting;
use App\Http\Controllers\Tenant\CatalogController;
use App\Core\Tenant\TenantManager;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Dinámicas Multi-Tenant de KreceWM
|--------------------------------------------------------------------------
| El middleware global IdentifyTenant resuelve el inquilino activo en memoria.
| En base a eso, renderizamos el catálogo público o el portal corporativo.
*/

// =========================================================================
// 0. RUTAS DE DESARROLLO — Solo disponibles en APP_ENV=local
//    http://127.0.0.1:8000/dev/switch-tenant/demo  → entrar como demo
//    http://127.0.0.1:8000/dev/switch-tenant/repuestos → entrar como repuestos
//    http://127.0.0.1:8000/dev/exit-tenant          → volver al portal central
// =========================================================================
if (app()->environment('local')) {
    Route::get('/dev/exit-tenant', function () {
        session()->forget('_dev_tenant');
        return redirect('/');
    })->name('dev.exit-tenant');

    Route::get('/dev/switch-tenant/{subdomain}', function (string $subdomain) {
        session(['_dev_tenant' => $subdomain]);
        return redirect('/admin/login');
    })->name('dev.switch-tenant');
}

// =========================================================================
// 1. RUTAS DE TIENDA VIRTUAL PÚBLICA (Solo si existe Tenant resuelto)
// =========================================================================
Route::group([], function () {
    
    // Catálogo público de la tienda (o Landing Page central si no hay tenant)
    Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');

    // Rutas específicas del Tenant resuelto
    Route::group(['middleware' => 'require.tenant'], function () {
        
        // Autenticación Administrativa Local de la Tienda
        Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('tenant.admin.login');
        Route::post('/admin/login', [LoginController::class, 'login'])->name('tenant.admin.login.post');
        Route::post('/admin/logout', [LoginController::class, 'logout'])->name('tenant.admin.logout');

        // Carrito de compras público
        Route::get('/cart', [\App\Http\Controllers\Tenant\CartController::class, 'index'])->name('tenant.cart.index');
        Route::post('/cart/add', [\App\Http\Controllers\Tenant\CartController::class, 'add'])->name('tenant.cart.add');
        Route::post('/cart/update', [\App\Http\Controllers\Tenant\CartController::class, 'update'])->name('tenant.cart.update');
        Route::post('/cart/remove', [\App\Http\Controllers\Tenant\CartController::class, 'remove'])->name('tenant.cart.remove');
        Route::post('/cart/clear', [\App\Http\Controllers\Tenant\CartController::class, 'clear'])->name('tenant.cart.clear');

        // Checkout público
        Route::get('/checkout', [\App\Http\Controllers\Tenant\CheckoutController::class, 'index'])->name('tenant.checkout.index');
        Route::post('/checkout', [\App\Http\Controllers\Tenant\CheckoutController::class, 'store'])->name('tenant.checkout.store');
        Route::get('/checkout/success/{order}', [\App\Http\Controllers\Tenant\CheckoutController::class, 'success'])->name('tenant.checkout.success');
        Route::get('/checkout/success/{order}/pdf', [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])->name('tenant.checkout.pdf');

        // Detalle de producto público
        Route::get('/producto/{slug}', [\App\Http\Controllers\Tenant\ProductDetailController::class, 'show'])->name('tenant.product.show');

        // Panel Administrativo Local de la Tienda (Protegido)
        Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'tenant.admin']], function () {
            
            // Dashboard de la tienda
            Route::get('/dashboard', [TenantDashboard::class, 'index'])->name('tenant.admin.dashboard');

            // Configuraciones de Branding Dinámico e Identidad
            Route::get('/settings', [TenantSetting::class, 'index'])->name('tenant.admin.settings.index');
            Route::post('/settings', [TenantSetting::class, 'update'])->name('tenant.admin.settings.update');
            
            // Categorías CRUD
            Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->names('tenant.admin.categories');
            
            // Marcas CRUD
            Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class)->names('tenant.admin.brands');
            
            // Productos CRUD
            Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->names('tenant.admin.products');
            
            // Clientes CRUD
            Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'])->names('tenant.admin.customers');

            // Pedidos CRUD & Estado & WhatsApp
            Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update', 'destroy'])->names('tenant.admin.orders');
            Route::post('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('tenant.admin.orders.status');
            Route::get('/orders/{order}/whatsapp', [\App\Http\Controllers\Admin\OrderController::class, 'sendWhatsApp'])->name('tenant.admin.orders.whatsapp');

            // Inventario
            Route::get('/inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('tenant.admin.inventory.index');
            Route::get('/inventory/{inventory}/adjust', [\App\Http\Controllers\Admin\InventoryController::class, 'showAdjustForm'])->name('tenant.admin.inventory.adjust');
            Route::post('/inventory/{inventory}/adjust', [\App\Http\Controllers\Admin\InventoryController::class, 'adjust'])->name('tenant.admin.inventory.adjust.post');
            Route::get('/inventory/movements', [\App\Http\Controllers\Admin\InventoryController::class, 'movements'])->name('tenant.admin.inventory.movements');
            
            // Importación Masiva
            Route::get('/import', [\App\Http\Controllers\Admin\BulkImportController::class, 'showForm'])->name('tenant.admin.import.index');
            Route::post('/import', [\App\Http\Controllers\Admin\BulkImportController::class, 'import'])->name('tenant.admin.import.post');
            Route::get('/import/download-template', [\App\Http\Controllers\Admin\BulkImportController::class, 'downloadTemplate'])->name('tenant.admin.import.template');

            // Reportes & Analytics
            Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('tenant.admin.reports.index');
            Route::get('/reports/export-csv', [\App\Http\Controllers\Admin\ReportController::class, 'exportCsv'])->name('tenant.admin.reports.export');

            // Gestión de Personal (Staff)
            Route::get('/staff', [\App\Http\Controllers\Admin\StaffController::class, 'index'])->name('tenant.admin.staff.index');
            Route::get('/staff/create', [\App\Http\Controllers\Admin\StaffController::class, 'create'])->name('tenant.admin.staff.create');
            Route::post('/staff', [\App\Http\Controllers\Admin\StaffController::class, 'store'])->name('tenant.admin.staff.store');
            Route::get('/staff/{user}/edit', [\App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('tenant.admin.staff.edit');
            Route::put('/staff/{user}', [\App\Http\Controllers\Admin\StaffController::class, 'update'])->name('tenant.admin.staff.update');
            Route::patch('/staff/{user}/toggle', [\App\Http\Controllers\Admin\StaffController::class, 'toggleStatus'])->name('tenant.admin.staff.toggle');

            // Facturas / Cotizaciones PDF
            Route::get('/orders/{order}/invoice', [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])->name('tenant.admin.orders.invoice');

            // Sistema de Notificaciones In-App
            Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('tenant.admin.notifications.index');
            Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('tenant.admin.notifications.markAllRead');
            Route::get('/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markRead'])->name('tenant.admin.notifications.markRead');

            // API ligera para polling de notificaciones (Alpine.js)
            Route::get('/api/notifications/unread-count', function () {
                return response()->json([
                    'count' => auth()->user()->unreadNotifications()->count(),
                ]);
            })->name('tenant.admin.api.notifications.unreadCount');
        });
    });
});

// =========================================================================
// 2. RUTAS DEL DOMINIO CENTRAL (Solo si NO hay Tenant resuelto)
// =========================================================================
Route::group(['middleware' => 'require.central'], function () {

    // Autenticación Central (Super Administrador KreceWM)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Panel Maestro Super Admin KreceWM (Protegido)
    Route::group(['prefix' => 'superadmin', 'middleware' => ['auth', 'superadmin']], function () {
        
        // Dashboard global
        Route::get('/dashboard', [SuperAdminDashboard::class, 'index'])->name('superadmin.dashboard');

        // Gestión de Negocios (Tenants)
        Route::get('/tenants', [SuperAdminTenant::class, 'index'])->name('superadmin.tenants.index');
        Route::get('/tenants/create', [SuperAdminTenant::class, 'create'])->name('superadmin.tenants.create');
        Route::post('/tenants', [SuperAdminTenant::class, 'store'])->name('superadmin.tenants.store');
        Route::get('/tenants/{tenant}/edit', [SuperAdminTenant::class, 'edit'])->name('superadmin.tenants.edit');
        Route::put('/tenants/{tenant}', [SuperAdminTenant::class, 'update'])->name('superadmin.tenants.update');
        Route::patch('/tenants/{tenant}/toggle', [SuperAdminTenant::class, 'toggleStatus'])->name('superadmin.tenants.toggle');
        
    });
});
