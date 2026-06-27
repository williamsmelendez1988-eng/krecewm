<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Import\ExcelImportService;
use App\Core\Tenant\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkImportController extends Controller
{
    protected ExcelImportService $importService;

    public function __construct(ExcelImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Mostrar la vista del formulario de importación.
     */
    public function showForm()
    {
        return view('admin.import.index', ['results' => null]);
    }

    /**
     * Procesar la importación masiva.
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,csv,txt|max:4096' // máx 4MB
        ]);

        $file = $request->file('import_file');
        $filePath = $file->getRealPath();

        $tenantId = TenantManager::getTenantId();
        $userId = Auth::id();

        // Ejecutar importación
        $results = $this->importService->import($filePath, $tenantId, $userId);

        return view('admin.import.index', compact('results'));
    }

    /**
     * Descargar la plantilla Excel formateada.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Productos Plantilla');

        // Definir columnas
        $headers = [
            'Nombre',
            'SKU',
            'Descripcion',
            'Categoria',
            'Marca',
            'Precio',
            'Precio_Oferta',
            'Precio_Costo',
            'Stock_Inicial',
            'Stock_Minimo',
            'Ubicacion'
        ];

        // Rellenar cabeceras en fila 1
        foreach ($headers as $key => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($key + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        // Agregar una fila de ejemplo (Fila 2)
        $exampleRow = [
            'Filtro de Aceite Hilux',
            'FLT-TOY-899',
            'Filtro de aceite original para Toyota Hilux motores 2.5 y 3.0.',
            'Filtros',
            'Toyota',
            '25.50',
            '22.00',
            '15.00',
            '50',
            '10',
            'Pasillo B - Estante 4'
        ];

        foreach ($exampleRow as $key => $val) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($key + 1);
            $sheet->setCellValue($colLetter . '2', $val);
        }

        // Estilos del encabezado (Fila 1)
        $headerRange = 'A1:K1';
        $styleArray = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'], // Slate-800 corporativo de KreceWM
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        
        $sheet->getStyle($headerRange)->applyFromArray($styleArray);

        // Autoajustar ancho de las columnas
        foreach (range(1, count($headers)) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Generar respuesta de descarga
        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="plantilla_carga_productos.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
