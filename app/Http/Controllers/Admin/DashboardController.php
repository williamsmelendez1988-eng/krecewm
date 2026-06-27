<?php

namespace App\Http\Controllers\Admin;

use App\Core\Tenant\TenantManager;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el Panel Administrativo con KPIs avanzados y datos de gráficas.
     */
    public function index()
    {
        $tenant = TenantManager::getTenant();
        $now    = Carbon::now();

        // ─── KPI Cards: ventas por período ─────────────────────────────────
        $salesToday = Order::whereDate('created_at', $now->toDateString())
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->sum('total');

        $salesWeek = Order::whereBetween('created_at', [
                $now->copy()->startOfWeek(), $now->copy()->endOfWeek(),
            ])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->sum('total');

        $salesMonth = Order::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->sum('total');

        $salesLastMonth = Order::whereYear('created_at', $now->copy()->subMonth()->year)
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->sum('total');

        // Variación porcentual respecto al mes anterior
        $monthGrowth = $salesLastMonth > 0
            ? round((($salesMonth - $salesLastMonth) / $salesLastMonth) * 100, 1)
            : ($salesMonth > 0 ? 100 : 0);

        // ─── Contadores generales ────────────────────────────────────────────
        $metrics = [
            'total_products'   => Product::count(),
            'total_customers'  => Customer::count(),
            'total_orders'     => Order::count(),
            'pending_orders'   => Order::where('status', 'pending')->count(),
            'low_stock_count'  => Inventory::whereColumn('quantity', '<=', 'min_stock')->count(),
            'sales_today'      => $salesToday,
            'sales_week'       => $salesWeek,
            'sales_month'      => $salesMonth,
            'month_growth'     => $monthGrowth,
        ];

        // ─── Datos para gráfica de barras: ventas de los últimos 7 días ─────
        $chartLabels = [];
        $chartData   = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $chartLabels[] = $day->format('D d');
            $chartData[]   = (float) Order::whereDate('created_at', $day->toDateString())
                ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
                ->sum('total');
        }

        // ─── Datos para gráfica de dona: pedidos por estado ─────────────────
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ─── Top 5 productos más vendidos del mes ────────────────────────────
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(total) as revenue'))
            ->whereHas('order', function ($q) use ($now) {
                $q->whereYear('created_at', $now->year)
                  ->whereMonth('created_at', $now->month)
                  ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
            })
            ->with('product:id,name,sku')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // ─── Últimos 5 pedidos ───────────────────────────────────────────────
        $latestOrders = Order::with('customer')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ─── Productos en stock bajo ─────────────────────────────────────────
        $lowStockItems = Inventory::with('product:id,name,sku')
            ->whereColumn('quantity', '<=', 'min_stock')
            ->orderBy('quantity')
            ->limit(5)
            ->get();

        // ─── Tasa de cambio configurada por el tenant ────────────────────────
        $exchangeRate = (float) ($tenant->getSetting('exchange_rate_usd_bs', 0));

        return view('admin.dashboard', compact(
            'tenant',
            'metrics',
            'chartLabels',
            'chartData',
            'ordersByStatus',
            'topProducts',
            'latestOrders',
            'lowStockItems',
            'exchangeRate'
        ));
    }
}
