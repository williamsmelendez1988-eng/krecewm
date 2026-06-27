<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    /**
     * Muestra la vista principal del Super Admin Dashboard.
     */
    public function index()
    {
        $metrics = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'suspended_tenants' => Tenant::where('status', 'suspended')->count(),
            'trial_tenants' => Tenant::where('status', 'trial')->count(),
            'total_plans' => Plan::count(),
            'total_users' => User::whereNotNull('tenant_id')->count(),
            
            // Métricas globales
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_revenue' => Order::whereIn('status', ['confirmed', 'shipped', 'delivered'])->sum('total'),
        ];

        // Obtener los últimos 5 negocios creados
        $latestTenants = Tenant::with('plan')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Top 5 negocios por ventas completadas
        $topTenants = Tenant::withCount(['orders' => function ($q) {
                $q->whereIn('status', ['confirmed', 'shipped', 'delivered']);
            }])
            ->withSum(['orders' => function ($q) {
                $q->whereIn('status', ['confirmed', 'shipped', 'delivered']);
            }], 'total')
            ->orderByRaw('COALESCE(orders_sum_total, 0) DESC')
            ->limit(5)
            ->get();

        return view('superadmin.dashboard', compact('metrics', 'latestTenants', 'topTenants'));
    }
}
