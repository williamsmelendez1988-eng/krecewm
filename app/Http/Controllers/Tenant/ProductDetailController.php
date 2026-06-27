<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Core\Tenant\TenantManager;

class ProductDetailController extends Controller
{
    /**
     * Muestra el detalle público de un producto.
     */
    public function show(string $slug)
    {
        $tenant       = TenantManager::getTenant();
        $exchangeRate = (float) $tenant->getSetting('exchange_rate_usd_bs', 0);

        $product = Product::with(['category', 'brand', 'inventory'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // Productos relacionados de la misma categoría
        $related = Product::with(['inventory'])
            ->where('status', 'active')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('tenant.catalog.show', compact('product', 'related', 'tenant', 'exchangeRate'));
    }
}
