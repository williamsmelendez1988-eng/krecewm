<?php

namespace App\Services\Import;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Inventory;
use App\Services\InventoryService;
use App\Core\Tenant\TenantManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;

class ExcelImportService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Importar catálogo desde un archivo Excel o CSV.
     */
    public function import(string $filePath, int $tenantId, ?int $userId = null): array
    {
        $results = [
            'success_count' => 0,
            'errors' => []
        ];

        try {
            // Cargar el archivo con PhpSpreadsheet
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
        } catch (Exception $e) {
            $results['errors'][] = [
                'row' => 0,
                'message' => 'No se pudo leer el archivo. Asegúrate de que sea un archivo Excel (.xlsx) o CSV válido.'
            ];
            return $results;
        }

        if (count($rows) <= 1) {
            $results['errors'][] = [
                'row' => 0,
                'message' => 'El archivo está vacío o no tiene filas de datos (cabecera no cuenta).'
            ];
            return $results;
        }

        // Leer cabeceras y mapear columnas
        $headers = array_map(function ($h) {
            return strtolower(trim($h));
        }, $rows[0]);

        // Mapear posiciones de las columnas propuestas
        $colMap = [
            'nombre' => array_search('nombre', $headers),
            'sku' => array_search('sku', $headers),
            'descripcion' => array_search('descripcion', $headers),
            'categoria' => array_search('categoria', $headers),
            'marca' => array_search('marca', $headers),
            'precio' => array_search('precio', $headers),
            'precio_oferta' => array_search('precio_oferta', $headers),
            'precio_costo' => array_search('precio_costo', $headers),
            'stock_inicial' => array_search('stock_inicial', $headers),
            'stock_minimo' => array_search('stock_minimo', $headers),
            'ubicacion' => array_search('ubicacion', $headers),
        ];

        // Validamos que al menos existan las columnas obligatorias en la cabecera
        if ($colMap['nombre'] === false || $colMap['sku'] === false || $colMap['precio'] === false || $colMap['stock_inicial'] === false) {
            $results['errors'][] = [
                'row' => 1,
                'message' => 'El archivo no contiene todas las columnas requeridas: "nombre", "sku", "precio", "stock_inicial".'
            ];
            return $results;
        }

        // Cache de categorías y marcas para evitar consultas redundantes en ciclos
        $categoriesCache = [];
        $brandsCache = [];

        // Registro de SKUs que ya están en el archivo para evitar duplicados internos
        $processedSkus = [];

        // Procesar fila por fila (empezando en el índice 1, después de las cabeceras)
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            // Si la fila completa está vacía, la ignoramos
            if (empty(array_filter($row))) {
                continue;
            }

            $rowNumber = $i + 1;

            // Extraer datos de la fila
            $nombre = trim($row[$colMap['nombre']] ?? '');
            $sku = strtoupper(trim($row[$colMap['sku']] ?? ''));
            $descripcion = trim($row[$colMap['descripcion']] ?? '');
            $categoriaNombre = trim($row[$colMap['categoria']] ?? '');
            $marcaNombre = trim($row[$colMap['marca']] ?? '');
            $precio = $row[$colMap['precio']];
            $precioOferta = $row[$colMap['precio_oferta']] ?? null;
            $precioCosto = $row[$colMap['precio_costo']] ?? null;
            $stockInicial = $row[$colMap['stock_inicial']];
            $stockMinimo = $row[$colMap['stock_minimo']] ?? 5;
            $ubicacion = trim($row[$colMap['ubicacion']] ?? '');

            // 1. Validaciones
            $rowErrors = [];

            if (empty($nombre)) {
                $rowErrors[] = 'El nombre es obligatorio.';
            }

            if (empty($sku)) {
                $rowErrors[] = 'El SKU es obligatorio.';
            } else {
                // Verificar duplicados dentro del mismo archivo
                if (in_array($sku, $processedSkus)) {
                    $rowErrors[] = "SKU '{$sku}' duplicado en el mismo archivo.";
                }
                // Verificar duplicados en la base de datos (con tenant_id aislado)
                elseif (Product::where('sku', $sku)->exists()) {
                    $rowErrors[] = "El SKU '{$sku}' ya está registrado en tu tienda.";
                }
            }

            if (!is_numeric($precio) || $precio < 0) {
                $rowErrors[] = 'El precio debe ser un número mayor o igual a 0.';
            }

            if ($precioOferta !== null && $precioOferta !== '' && (!is_numeric($precioOferta) || $precioOferta < 0)) {
                $rowErrors[] = 'El precio de oferta debe ser numérico mayor o igual a 0.';
            }

            if ($precioCosto !== null && $precioCosto !== '' && (!is_numeric($precioCosto) || $precioCosto < 0)) {
                $rowErrors[] = 'El precio de costo debe ser numérico mayor o igual a 0.';
            }

            if (!is_numeric($stockInicial) || $stockInicial < 0) {
                $rowErrors[] = 'El stock inicial debe ser un número entero mayor o igual a 0.';
            }

            if ($stockMinimo !== null && $stockMinimo !== '' && (!is_numeric($stockMinimo) || $stockMinimo < 0)) {
                $rowErrors[] = 'El stock mínimo debe ser un número mayor o igual a 0.';
            }

            // Si hay errores en esta fila, los registramos y saltamos al siguiente producto
            if (!empty($rowErrors)) {
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'sku' => $sku ?: 'N/A',
                    'message' => implode(' | ', $rowErrors)
                ];
                continue;
            }

            // 2. Inserción en base de datos con Transacción
            try {
                DB::transaction(function () use (
                    $tenantId, $userId, $nombre, $sku, $descripcion, $categoriaNombre,
                    $marcaNombre, $precio, $precioOferta, $precioCosto, $stockInicial,
                    $stockMinimo, $ubicacion, &$categoriesCache, &$brandsCache
                ) {
                    // Resolver Categoría dinámica
                    $categoryId = null;
                    if (!empty($categoriaNombre)) {
                        $catKey = strtolower($categoriaNombre);
                        if (!isset($categoriesCache[$catKey])) {
                            // Buscar en base de datos por nombre
                            $category = Category::where('name', $categoriaNombre)->first();
                            if (!$category) {
                                // Crear categoría al vuelo
                                $category = Category::create([
                                    'tenant_id' => $tenantId,
                                    'name' => $categoriaNombre,
                                    'slug' => $this->generateUniqueCategorySlug($categoriaNombre, $tenantId),
                                    'status' => 'active'
                                ]);
                            }
                            $categoriesCache[$catKey] = $category->id;
                        }
                        $categoryId = $categoriesCache[$catKey];
                    } else {
                        // Opcional: Podríamos tener una categoría por defecto "Sin categoría"
                        $categoryId = $this->getDefaultCategoryId($tenantId, $categoriesCache);
                    }

                    // Resolver Marca dinámica
                    $brandId = null;
                    if (!empty($marcaNombre)) {
                        $brandKey = strtolower($marcaNombre);
                        if (!isset($brandsCache[$brandKey])) {
                            $brand = Brand::where('name', $marcaNombre)->first();
                            if (!$brand) {
                                $brand = Brand::create([
                                    'tenant_id' => $tenantId,
                                    'name' => $marcaNombre,
                                    'slug' => $this->generateUniqueBrandSlug($marcaNombre, $tenantId),
                                    'status' => 'active'
                                ]);
                            }
                            $brandsCache[$brandKey] = $brand->id;
                        }
                        $brandId = $brandsCache[$brandKey];
                    }

                    // Crear el producto
                    $product = Product::create([
                        'tenant_id' => $tenantId,
                        'category_id' => $categoryId,
                        'brand_id' => $brandId,
                        'name' => $nombre,
                        'slug' => $this->generateUniqueProductSlug($nombre, $tenantId),
                        'sku' => $sku,
                        'description' => $descripcion,
                        'price' => (float) $precio,
                        'sale_price' => ($precioOferta !== null && $precioOferta !== '') ? (float) $precioOferta : null,
                        'cost_price' => ($precioCosto !== null && $precioCosto !== '') ? (float) $precioCosto : null,
                        'status' => 'active',
                        'is_featured' => false
                    ]);

                    // Crear inventario
                    $inventory = Inventory::create([
                        'tenant_id' => $tenantId,
                        'product_id' => $product->id,
                        'quantity' => 0, // Aumentará por el Kárdex
                        'min_stock' => (int) ($stockMinimo ?? 5),
                        'location' => $ubicacion
                    ]);

                    // Registrar stock inicial en Kárdex
                    if ($stockInicial > 0) {
                        $this->inventoryService->registerMovement(
                            inventoryId: $inventory->id,
                            type: 'in',
                            quantity: (int) $stockInicial,
                            reason: 'purchase',
                            description: 'Carga inicial desde importación masiva',
                            userId: $userId,
                            tenantId: $tenantId
                        );
                    }
                });

                $processedSkus[] = $sku;
                $results['success_count']++;

            } catch (Exception $ex) {
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'sku' => $sku,
                    'message' => 'Error al guardar en base de datos: ' . $ex->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Resolver o crear una categoría general por defecto.
     */
    protected function getDefaultCategoryId(int $tenantId, array &$cache): int
    {
        $defaultName = 'General';
        $key = strtolower($defaultName);

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $category = Category::where('name', $defaultName)->first();
        if (!$category) {
            $category = Category::create([
                'tenant_id' => $tenantId,
                'name' => $defaultName,
                'slug' => 'general',
                'status' => 'active'
            ]);
        }

        $cache[$key] = $category->id;
        return $category->id;
    }

    /**
     * Slug único de categoría para el inquilino.
     */
    protected function generateUniqueCategorySlug(string $name, int $tenantId): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;
        while (Category::where('tenant_id', $tenantId)->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    /**
     * Slug único de marca para el inquilino.
     */
    protected function generateUniqueBrandSlug(string $name, int $tenantId): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;
        while (Brand::where('tenant_id', $tenantId)->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    /**
     * Slug único de producto para el inquilino.
     */
    protected function generateUniqueProductSlug(string $name, int $tenantId): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;
        while (Product::where('tenant_id', $tenantId)->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }
        return $slug;
    }
}
