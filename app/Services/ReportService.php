<?php

namespace App\Services;

use App\Core\Tenant\TenantManager;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Resumen de ventas para un período dado.
     */
    public function salesSummary(string $period = 'month'): array
    {
        [$from, $to] = $this->getDateRange($period);

        $orders = Order::whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', ['cancelled']);

        $revenue    = (float) $orders->sum('total');
        $count      = $orders->count();
        $avgTicket  = $count > 0 ? round($revenue / $count, 2) : 0;

        // Comparación con período anterior
        $prevFrom = (clone $from)->sub($from->diff($to));
        $prevTo   = $from;

        $prevRevenue = (float) Order::whereBetween('created_at', [$prevFrom, $prevTo])
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');

        $revenueChange = $prevRevenue > 0
            ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : null;

        return compact('revenue', 'count', 'avgTicket', 'revenueChange', 'from', 'to');
    }

    /**
     * Pedidos agrupados por estado (para gráfica donut).
     */
    public function ordersByStatus(): Collection
    {
        return Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
    }

    /**
     * Pedidos agrupados por día para el período dado (para gráfica de línea).
     */
    public function dailySales(string $period = 'month'): Collection
    {
        [$from, $to] = $this->getDateRange($period);

        return Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as orders'),
                DB::raw('sum(total) as revenue')
            )
            ->whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', ['cancelled'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Productos más vendidos (por cantidad de unidades).
     */
    public function topProducts(int $limit = 10): Collection
    {
        return OrderItem::select('product_name', 'product_id',
                DB::raw('sum(quantity) as units_sold'),
                DB::raw('sum(total) as revenue')
            )
            ->whereHas('order', fn($q) => $q->whereNotIn('status', ['cancelled']))
            ->groupBy('product_name', 'product_id')
            ->orderByDesc('units_sold')
            ->limit($limit)
            ->get();
    }

    /**
     * Métricas de clientes: nuevos en el período y total.
     */
    public function customerMetrics(string $period = 'month'): array
    {
        [$from, $to] = $this->getDateRange($period);

        $newCustomers   = Customer::whereBetween('created_at', [$from, $to])->count();
        $totalCustomers = Customer::count();

        // Clientes recurrentes: los que tienen más de 1 pedido
        $recurrent = Customer::has('orders', '>', 1)->count();

        return compact('newCustomers', 'totalCustomers', 'recurrent');
    }

    /**
     * Genera un stream de CSV con los pedidos del período.
     */
    public function exportOrdersCsv(string $period = 'month'): string
    {
        [$from, $to] = $this->getDateRange($period);

        $orders = Order::with('customer')
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->get();

        $rows = [];
        $rows[] = implode(',', [
            'N° Pedido', 'Fecha', 'Cliente', 'Teléfono',
            'Ciudad', 'Método Pago', 'Estado', 'Total',
        ]);

        foreach ($orders as $o) {
            $rows[] = implode(',', [
                $o->order_number,
                $o->created_at->format('d/m/Y H:i'),
                '"' . ($o->shipping_name ?? '') . '"',
                $o->shipping_phone ?? '',
                '"' . ($o->shipping_city ?? '') . '"',
                $o->payment_method,
                $o->status,
                number_format($o->total, 2),
            ]);
        }

        return implode("\n", $rows);
    }

    /**
     * Retorna rango de fechas para el período solicitado.
     */
    private function getDateRange(string $period): array
    {
        return match ($period) {
            'today'  => [Carbon::today(), Carbon::now()],
            'week'   => [Carbon::now()->startOfWeek(), Carbon::now()],
            'month'  => [Carbon::now()->startOfMonth(), Carbon::now()],
            'year'   => [Carbon::now()->startOfYear(), Carbon::now()],
            default  => [Carbon::now()->startOfMonth(), Carbon::now()],
        };
    }
}
