<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Muestra el dashboard de reportes.
     */
    public function index(Request $request)
    {
        $period = $request->input('period', 'month');

        $summary        = $this->reportService->salesSummary($period);
        $ordersByStatus = $this->reportService->ordersByStatus();
        $dailySales     = $this->reportService->dailySales($period);
        $topProducts    = $this->reportService->topProducts(10);
        $customers      = $this->reportService->customerMetrics($period);

        return view('admin.reports.index', compact(
            'summary', 'ordersByStatus', 'dailySales', 'topProducts', 'customers', 'period'
        ));
    }

    /**
     * Exporta los pedidos del período a un archivo CSV.
     */
    public function exportCsv(Request $request)
    {
        $period   = $request->input('period', 'month');
        $csv      = $this->reportService->exportOrdersCsv($period);
        $filename = 'pedidos-' . $period . '-' . now()->format('Ymd') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
