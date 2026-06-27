<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Services\InventoryService;
use App\Services\Import\ExcelImportService;
use App\Core\Tenant\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class InventoryAndImportTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected InventoryService $inventoryService;
    protected ExcelImportService $importService;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Crear Plan y Tenant de prueba
        $plan = Plan::create([
            'name' => 'Gold Plan',
            'slug' => 'gold',
            'price' => 99.00,
            'billing_period' => 'monthly',
            'max_products' => 500,
            'max_users' => 5
        ]);

        $this->tenant = Tenant::create([
            'plan_id' => $plan->id,
            'name' => 'Tienda Test',
            'subdomain' => 'test',
            'status' => 'active',
            'contact_email' => 'test@tienda.com',
            'contact_phone' => '123456789'
        ]);

        // 2. Establecer el tenant activo en el singleton
        TenantManager::setTenant($this->tenant);

        // 3. Crear usuario administrativo para el tenant
        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Tenant Owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant_admin'
        ]);

        // 4. Resolver servicios
        $this->inventoryService = $this->app->make(InventoryService::class);
        $this->importService = $this->app->make(ExcelImportService::class);
    }

    /**
     * Test de creación de producto con inventario inicial.
     */
    public function test_product_creation_initializes_inventory()
    {
        $productService = $this->app->make(\App\Services\ProductService::class);

        $category = Category::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Frenos',
            'slug' => 'frenos',
            'status' => 'active'
        ]);

        $data = [
            'tenant_id' => $this->tenant->id,
            'category_id' => $category->id,
            'name' => 'Pastillas de Freno delanteras',
            'sku' => 'PST-123',
            'price' => 1500.00,
            'stock' => 25,
            'min_stock' => 5,
            'location' => 'Estante A-1'
        ];

        $product = $productService->createProduct($data, [], $this->user->id);

        // Verificar producto en base de datos
        $this->assertDatabaseHas('products', [
            'tenant_id' => $this->tenant->id,
            'sku' => 'PST-123',
            'name' => 'Pastillas de Freno delanteras'
        ]);

        // Verificar inventario creado con la cantidad
        $this->assertDatabaseHas('inventories', [
            'tenant_id' => $this->tenant->id,
            'product_id' => $product->id,
            'quantity' => 25,
            'min_stock' => 5,
            'location' => 'Estante A-1'
        ]);

        // Verificar movimiento en Kárdex
        $this->assertDatabaseHas('inventory_movements', [
            'tenant_id' => $this->tenant->id,
            'type' => 'in',
            'quantity' => 25,
            'reason' => 'purchase',
            'user_id' => $this->user->id
        ]);
    }

    /**
     * Test de movimientos de inventario (Kárdex).
     */
    public function test_inventory_movements_in_and_out()
    {
        $category = Category::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Aceites',
            'slug' => 'aceites',
            'status' => 'active'
        ]);

        $product = Product::create([
            'tenant_id' => $this->tenant->id,
            'category_id' => $category->id,
            'name' => 'Aceite de Motor 10W40',
            'slug' => 'aceite-10w40',
            'sku' => 'ACE-10W40',
            'price' => 2200.00,
            'status' => 'active'
        ]);

        $inventory = Inventory::create([
            'tenant_id' => $this->tenant->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'min_stock' => 2
        ]);

        // 1. Test Entrada de stock
        $this->inventoryService->registerMovement(
            inventoryId: $inventory->id,
            type: 'in',
            quantity: 15,
            reason: 'purchase',
            description: 'Compra a distribuidor',
            userId: $this->user->id
        );

        $inventory->refresh();
        $this->assertEquals(25, $inventory->quantity); // 10 + 15

        // 2. Test Salida de stock (Venta)
        $this->inventoryService->registerMovement(
            inventoryId: $inventory->id,
            type: 'out',
            quantity: 5,
            reason: 'sale',
            description: 'Venta directa',
            userId: $this->user->id
        );

        $inventory->refresh();
        $this->assertEquals(20, $inventory->quantity); // 25 - 5

        // 3. Test Salida excediendo stock debe fallar
        $this->expectException(Exception::class);
        $this->inventoryService->registerMovement(
            inventoryId: $inventory->id,
            type: 'out',
            quantity: 100,
            reason: 'sale',
            userId: $this->user->id
        );
    }

    /**
     * Test de carga masiva de productos (Excel/CSV mock).
     */
    public function test_bulk_import_from_csv()
    {
        // Creamos un string CSV temporal para simular la importación
        $csvContent = "Nombre,SKU,Descripcion,Categoria,Marca,Precio,Precio_Oferta,Precio_Costo,Stock_Inicial,Stock_Minimo,Ubicacion\n"
                    . "Filtro de Aire Hilux,FLT-AIR-999,Filtro de aire para Hilux,Filtros,Toyota,800.00,,500.00,30,5,Pasillo A-3\n"
                    . "Batería Bosch 75Ah,BAT-BOS-75,Batería libre de mantenimiento,Eléctrico,Bosch,4500.00,4200.00,3000.00,12,3,Pasillo D\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'import_') . '.csv';
        file_put_contents($tempFile, $csvContent);

        // Ejecutar importación
        $results = $this->importService->import($tempFile, $this->tenant->id, $this->user->id);

        unlink($tempFile);

        // Verificar resultados del parseo
        $this->assertEquals(2, $results['success_count']);
        $this->assertEmpty($results['errors']);

        // Verificar que las categorías y marcas se crearon dinámicamente
        $this->assertDatabaseHas('categories', [
            'tenant_id' => $this->tenant->id,
            'name' => 'Filtros'
        ]);

        $this->assertDatabaseHas('brands', [
            'tenant_id' => $this->tenant->id,
            'name' => 'Toyota'
        ]);

        // Verificar que los productos se insertaron correctamente
        $this->assertDatabaseHas('products', [
            'tenant_id' => $this->tenant->id,
            'sku' => 'FLT-AIR-999',
            'price' => 800.00
        ]);

        $this->assertDatabaseHas('products', [
            'tenant_id' => $this->tenant->id,
            'sku' => 'BAT-BOS-75',
            'sale_price' => 4200.00
        ]);

        // Verificar existencias e inventarios
        $product1 = Product::where('sku', 'FLT-AIR-999')->first();
        $this->assertNotNull($product1->inventory);
        $this->assertEquals(30, $product1->inventory->quantity);
    }
}
