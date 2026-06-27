<?php

namespace App\Services;

use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class ProductService
{
    protected ProductRepositoryInterface $productRepository;
    protected InventoryService $inventoryService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        InventoryService $inventoryService
    ) {
        $this->productRepository = $productRepository;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Crear un nuevo producto junto con su inventario inicial.
     */
    public function createProduct(array $data, ?array $images = [], ?int $userId = null): Product
    {
        return DB::transaction(function () use ($data, $images, $userId) {
            // 1. Generar SKU si no se especifica
            if (empty($data['sku'])) {
                $data['sku'] = $this->generateUniqueSku($data['name']);
            }

            // 2. Generar slug único del producto
            $data['slug'] = $this->generateUniqueSlug($data['name']);

            // 3. Procesar variaciones
            $data['variations'] = $this->formatVariations($data['variations'] ?? null);

            // 4. Subir imágenes
            $uploadedImages = [];
            if (!empty($images)) {
                foreach ($images as $image) {
                    if ($image instanceof UploadedFile) {
                        $uploadedImages[] = $this->uploadProductImage($image);
                    }
                }
            }
            $data['images'] = $uploadedImages;

            // Por defecto el estado es activo si no se indica lo contrario
            $data['status'] = $data['status'] ?? 'active';
            $data['is_featured'] = filter_var($data['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN);

            // 5. Crear el Producto
            $product = $this->productRepository->create($data);

            // 6. Crear Registro de Inventario
            $stock = isset($data['stock']) ? (int) $data['stock'] : 0;
            $minStock = isset($data['min_stock']) ? (int) $data['min_stock'] : 5;
            $location = $data['location'] ?? null;

            $inventory = Inventory::create([
                'tenant_id' => $product->tenant_id,
                'product_id' => $product->id,
                'quantity' => 0, // Se inicializará a través del movimiento de Kárdex
                'min_stock' => $minStock,
                'location' => $location
            ]);

            // 7. Generar movimiento de entrada inicial en el Kárdex (si hay stock > 0)
            if ($stock > 0) {
                $this->inventoryService->registerMovement(
                    inventoryId: $inventory->id,
                    type: 'in',
                    quantity: $stock,
                    reason: 'purchase',
                    description: 'Stock inicial al registrar producto',
                    userId: $userId,
                    tenantId: $product->tenant_id
                );
            }

            return $product;
        });
    }

    /**
     * Actualizar un producto existente.
     */
    public function updateProduct(int $id, array $data, ?array $newImages = [], ?array $keepImages = []): bool
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return false;
        }

        return DB::transaction(function () use ($product, $data, $newImages, $keepImages) {
            // 1. Generar slug nuevo si cambia el nombre
            if (!empty($data['name']) && $data['name'] !== $product->name) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $product->id);
            }

            // 2. Formatear variaciones
            if (isset($data['variations'])) {
                $data['variations'] = $this->formatVariations($data['variations']);
            }

            // 3. Gestionar imágenes antiguas y nuevas
            $currentImages = $product->images ?? [];
            $finalImages = [];

            // Conservar las imágenes antiguas seleccionadas
            if (!empty($keepImages)) {
                foreach ($currentImages as $img) {
                    if (in_array($img, $keepImages)) {
                        $finalImages[] = $img;
                    } else {
                        // Eliminar física de storage si no se conserva
                        Storage::disk('public')->delete($img);
                    }
                }
            } else {
                // Si no se indica conservar nada, se eliminan todas las imágenes anteriores
                foreach ($currentImages as $img) {
                    Storage::disk('public')->delete($img);
                }
            }

            // Subir imágenes nuevas
            if (!empty($newImages)) {
                foreach ($newImages as $image) {
                    if ($image instanceof UploadedFile) {
                        $finalImages[] = $this->uploadProductImage($image);
                    }
                }
            }

            $data['images'] = $finalImages;
            $data['is_featured'] = filter_var($data['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN);

            // Actualizar producto
            $updated = $this->productRepository->update($product->id, $data);

            // Actualizar detalles de inventario asociados (ubicación y stock mínimo)
            if ($updated && $product->inventory) {
                $inventoryData = [];
                if (isset($data['min_stock'])) {
                    $inventoryData['min_stock'] = (int) $data['min_stock'];
                }
                if (isset($data['location'])) {
                    $inventoryData['location'] = $data['location'];
                }
                if (!empty($inventoryData)) {
                    $product->inventory->update($inventoryData);
                }
            }

            return $updated;
        });
    }

    /**
     * Eliminar físicamente un producto junto a sus imágenes e inventario.
     */
    public function deleteProduct(int $id): bool
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return false;
        }

        return DB::transaction(function () use ($product) {
            // Eliminar imágenes de storage
            if (!empty($product->images)) {
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            // Eliminar producto (esto gatilla borrado en cascada del inventario en la BD)
            return $this->productRepository->delete($product->id);
        });
    }

    /**
     * Generar un SKU único para el inquilino activo.
     */
    protected function generateUniqueSku(string $productName): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $productName), 0, 3));
        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }

        do {
            $sku = $prefix . '-' . strtoupper(Str::random(6));
            $exists = Product::where('sku', $sku)->exists(); // El scope global aísla la búsqueda por tenant automáticamente
        } while ($exists);

        return $sku;
    }

    /**
     * Generar un slug único para el inquilino activo.
     */
    protected function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = Product::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Subir imagen a storage/app/public/uploads/products.
     */
    protected function uploadProductImage(UploadedFile $file): string
    {
        // Guardará en public/uploads/products/archivo_unico.ext
        return $file->store('uploads/products', 'public');
    }

    /**
     * Formatear el JSON de variaciones.
     * Convierte el formato recibido de vista o API a la estructura estándar.
     */
    protected function formatVariations($variations): ?array
    {
        if (empty($variations)) {
            return null;
        }

        // Si ya es un array asociativo/objeto, lo validamos y retornamos
        if (is_array($variations)) {
            // Estructura esperada: [['name' => 'Color', 'options' => ['Rojo', 'Azul']]]
            $clean = [];
            foreach ($variations as $var) {
                if (isset($var['name']) && !empty($var['name'])) {
                    $options = $var['options'] ?? [];
                    if (is_string($options)) {
                        // Si viene como string separado por comas, lo parseamos
                        $options = array_filter(array_map('trim', explode(',', $options)));
                    }
                    if (!empty($options)) {
                        $clean[] = [
                            'name' => trim($var['name']),
                            'options' => array_values($options)
                        ];
                    }
                }
            }
            return !empty($clean) ? $clean : null;
        }

        return null;
    }
}
