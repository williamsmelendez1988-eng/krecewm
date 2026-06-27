<?php

namespace App\Http\Controllers\Tenant;

use App\Core\Tenant\TenantManager;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class CatalogController extends Controller
{
    /**
     * Muestra la página de inicio pública.
     * Si no hay tenant, muestra la landing de KreceWM.
     * Si hay tenant, muestra su catálogo.
     */
    public function index()
    {
        // 1. Mostrar la Landing Page principal del SaaS
        if (!TenantManager::hasTenant()) {
            return view('landing.index');
        }

        // 2. Mostrar la tienda virtual del Tenant activo
        $tenant = TenantManager::getTenant();

        // Obtener categorías activas
        $categories = Category::where('status', 'active')
            ->whereNull('parent_id')
            ->get();

        // Query base para productos
        $query = Product::with(['category', 'brand', 'inventory'])
            ->where('status', 'active');

        // Filtro por Categoría
        if (request()->filled('categoria')) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', request('categoria'));
            });
        }

        // Filtro por Búsqueda (Artisan / SQL level fallback)
        if (request()->filled('buscar')) {
            $searchTerm = request('buscar');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Paginar productos
        $products = $query->orderBy('is_featured', 'desc')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        // Productos destacados (para carrusel o banner si no hay búsqueda activa)
        $featuredProducts = Product::with(['category', 'brand', 'inventory'])
            ->where('is_featured', true)
            ->where('status', 'active')
            ->limit(4)
            ->get();

        $whatsappNumber = $tenant->getSetting('whatsapp_number', $tenant->contact_phone);
        $logoText       = $tenant->getSetting('logo_text', $tenant->name);
        $favicon        = $tenant->getSetting('favicon', null);
        $exchangeRate   = (float) $tenant->getSetting('exchange_rate_usd_bs', 0); // 0 = desactivado

        return view('tenant.catalog.index', compact(
            'tenant',
            'products',
            'featuredProducts',
            'categories',
            'whatsappNumber',
            'logoText',
            'favicon',
            'exchangeRate'
        ));
    }
}
