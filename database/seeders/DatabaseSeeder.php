<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Seeder;
// Hash facade no necesaria: el cast 'hashed' del modelo User hashea automáticamente

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear Planes del SaaS
        $bronze = Plan::create([
            'name' => 'Plan Bronce',
            'slug' => 'bronze',
            'price' => 29.99,
            'billing_period' => 'monthly',
            'max_products' => 50,
            'max_users' => 1,
            'features' => [
                'soporte_email' => true,
                'catalogo_web' => true,
                'dominio_krecewm' => true,
                'dominio_personalizado' => false,
                'carga_masiva' => false,
            ]
        ]);

        $silver = Plan::create([
            'name' => 'Plan Plata',
            'slug' => 'silver',
            'price' => 59.99,
            'billing_period' => 'monthly',
            'max_products' => 200,
            'max_users' => 3,
            'features' => [
                'soporte_rapido' => true,
                'catalogo_web' => true,
                'dominio_krecewm' => true,
                'dominio_personalizado' => true,
                'carga_masiva' => true,
            ]
        ]);

        $gold = Plan::create([
            'name' => 'Plan Oro',
            'slug' => 'gold',
            'price' => 99.99,
            'billing_period' => 'monthly',
            'max_products' => 1000,
            'max_users' => 10,
            'features' => [
                'soporte_24_7' => true,
                'catalogo_web' => true,
                'dominio_krecewm' => true,
                'dominio_personalizado' => true,
                'carga_masiva' => true,
                'reportes_avanzados' => true,
            ]
        ]);

        // 2. Crear Super Administrador de KreceWM (tenant_id = null)
        User::create([
            'tenant_id' => null,
            'name' => 'Admin KreceWM',
            'email' => 'admin@krecewm.com',
            'password' => 'admin123',
            'role' => 'superadmin',
            'status' => 'active',
        ]);

        // 3. Crear Tenant 1: Demo Shop (demo.krecewm.local)
        $tenantDemo = Tenant::create([
            'plan_id' => $gold->id,
            'name' => 'Ferretería Central Demo',
            'subdomain' => 'demo',
            'custom_domain' => 'demo.krecewm.local',
            'logo' => null, // Cargará colores y textos dinámicos por defecto
            'primary_color' => '#2563eb', // Azul
            'secondary_color' => '#f59e0b', // Amarillo
            'contact_email' => 'contacto@ferreteriacentral.com',
            'contact_phone' => '+541188887777',
            'address' => 'Av. Rivadavia 1234, CABA',
            'city' => 'Buenos Aires',
            'status' => 'active',
            'trial_ends_at' => now()->addDays(30),
        ]);

        // Configuración dinámica inicial para Tenant Demo
        $demoSettings = [
            'logo_text' => 'Ferretería Central',
            'whatsapp_number' => '541188887777',
            'bank_transfer_info' => 'Banco de la Nación Argentina - CBU: 0110123456789012345678 - Alias: FERRE.CENTRAL',
            'facebook_url' => 'https://facebook.com/ferreteriacentral',
            'instagram_url' => 'https://instagram.com/ferreteriacentral',
        ];
        foreach ($demoSettings as $key => $value) {
            Setting::create([
                'tenant_id' => $tenantDemo->id,
                'key' => $key,
                'value' => $value,
            ]);
        }

        // Usuarios del Tenant Demo
        User::create([
            'tenant_id' => $tenantDemo->id,
            'name' => 'Juan Pérez (Demo Owner)',
            'email' => 'owner@demo.com',
            'password' => 'owner123',
            'role' => 'tenant_admin',
            'status' => 'active',
        ]);

        User::create([
            'tenant_id' => $tenantDemo->id,
            'name' => 'Sofía Empleada (Demo Staff)',
            'email' => 'staff@demo.com',
            'password' => 'staff123',
            'role' => 'tenant_staff',
            'status' => 'active',
        ]);

        // 4. Crear Tenant 2: Repuestos López (repuestos.krecewm.local)
        $tenantRepuestos = Tenant::create([
            'plan_id' => $silver->id,
            'name' => 'Repuestos López',
            'subdomain' => 'repuestos',
            'custom_domain' => 'repuestos.krecewm.local',
            'logo' => null,
            'primary_color' => '#dc2626', // Rojo
            'secondary_color' => '#1e293b', // Gris Oscuro
            'contact_email' => 'ventas@repuestoslopez.com',
            'contact_phone' => '+541199998888',
            'address' => 'Av. Mitre 5678, Avellaneda',
            'city' => 'Buenos Aires',
            'status' => 'active',
            'trial_ends_at' => now()->addDays(15),
        ]);

        // Configuración dinámica inicial para Tenant Repuestos
        $repuestosSettings = [
            'logo_text' => 'Repuestos López',
            'whatsapp_number' => '541199998888',
            'bank_transfer_info' => 'Banco Galicia - CBU: 0070123456789012345678 - Alias: REPUESTOS.LOPEZ',
            'facebook_url' => 'https://facebook.com/repuestoslopez',
            'instagram_url' => 'https://instagram.com/repuestoslopez',
        ];
        foreach ($repuestosSettings as $key => $value) {
            Setting::create([
                'tenant_id' => $tenantRepuestos->id,
                'key' => $key,
                'value' => $value,
            ]);
        }

        // Usuarios del Tenant Repuestos
        User::create([
            'tenant_id' => $tenantRepuestos->id,
            'name' => 'Carlos López',
            'email' => 'owner@repuestos.com',
            'password' => 'owner123',
            'role' => 'tenant_admin',
            'status' => 'active',
        ]);

        // 5. Sembrar Categorías, Marcas, Productos e Inventario para Tenant Demo (Ferretería)
        $catHerramientasElectricas = \App\Models\Category::create([
            'tenant_id' => $tenantDemo->id,
            'name' => 'Herramientas Eléctricas',
            'slug' => 'herramientas-electricas',
            'description' => 'Taladros, amoladoras, sierras y más.',
            'status' => 'active'
        ]);

        $catHerramientasManuales = \App\Models\Category::create([
            'tenant_id' => $tenantDemo->id,
            'name' => 'Herramientas Manuales',
            'slug' => 'herramientas-manuales',
            'description' => 'Martillos, pinzas, destornilladores y llaves.',
            'status' => 'active'
        ]);

        $brandBosch = \App\Models\Brand::create([
            'tenant_id' => $tenantDemo->id,
            'name' => 'Bosch',
            'slug' => 'bosch',
            'status' => 'active'
        ]);

        $brandStanley = \App\Models\Brand::create([
            'tenant_id' => $tenantDemo->id,
            'name' => 'Stanley',
            'slug' => 'stanley',
            'status' => 'active'
        ]);

        $prodTaladro = \App\Models\Product::create([
            'tenant_id' => $tenantDemo->id,
            'category_id' => $catHerramientasElectricas->id,
            'brand_id' => $brandBosch->id,
            'name' => 'Taladro Percutor Bosch GSB 13 RE 650W',
            'slug' => 'taladro-percutor-bosch-gsb-13-re-650w',
            'sku' => 'TAL-BOS-650',
            'internal_code' => 'COD001',
            'description' => 'Taladro de alto rendimiento con velocidad variable, ideal para concreto, madera y metal.',
            'price' => 79.99,
            'sale_price' => 69.99,
            'is_featured' => true,
            'status' => 'active'
        ]);

        \App\Models\Inventory::create([
            'tenant_id' => $tenantDemo->id,
            'product_id' => $prodTaladro->id,
            'quantity' => 15,
            'min_stock' => 3,
            'location' => 'Pasillo A - Estante 2'
        ]);

        $prodDestornillador = \App\Models\Product::create([
            'tenant_id' => $tenantDemo->id,
            'category_id' => $catHerramientasManuales->id,
            'brand_id' => $brandStanley->id,
            'name' => 'Juego de Destornilladores Stanley 10 Piezas',
            'slug' => 'juego-de-destornilladores-stanley-10-piezas',
            'sku' => 'DEST-STAN-10',
            'internal_code' => 'COD002',
            'description' => 'Destornilladores con puntas magnetizadas y mangos ergonómicos antideslizantes.',
            'price' => 19.99,
            'sale_price' => null,
            'is_featured' => false,
            'status' => 'active'
        ]);

        \App\Models\Inventory::create([
            'tenant_id' => $tenantDemo->id,
            'product_id' => $prodDestornillador->id,
            'quantity' => 45,
            'min_stock' => 5,
            'location' => 'Pasillo B - Estante 1'
        ]);

        // 6. Sembrar Categorías, Marcas, Productos e Inventario para Tenant Repuestos (Repuestos López)
        $catMotor = \App\Models\Category::create([
            'tenant_id' => $tenantRepuestos->id,
            'name' => 'Motor',
            'slug' => 'motor',
            'description' => 'Bujías, filtros, correas y componentes del motor.',
            'status' => 'active'
        ]);

        $brandFram = \App\Models\Brand::create([
            'tenant_id' => $tenantRepuestos->id,
            'name' => 'Fram',
            'slug' => 'fram',
            'status' => 'active'
        ]);

        $prodFiltro = \App\Models\Product::create([
            'tenant_id' => $tenantRepuestos->id,
            'category_id' => $catMotor->id,
            'brand_id' => $brandFram->id,
            'name' => 'Filtro de Aceite Fram Extra Guard PH3614',
            'slug' => 'filtro-de-aceite-fram-extra-guard-ph3614',
            'sku' => 'FIL-FRA-PH3614',
            'internal_code' => 'REP001',
            'description' => 'Filtro de aceite premium de larga duración, protege el motor contra suciedad y partículas.',
            'price' => 8.50,
            'sale_price' => 7.20,
            'is_featured' => true,
            'status' => 'active'
        ]);

        \App\Models\Inventory::create([
            'tenant_id' => $tenantRepuestos->id,
            'product_id' => $prodFiltro->id,
            'quantity' => 120,
            'min_stock' => 10,
            'location' => 'Estantería 4 - Caja B'
        ]);

        // 7. Sembrar Clientes y Pedidos para Tenant Demo (para métricas del Dashboard)
        $customerDemo = \App\Models\Customer::create([
            'tenant_id' => $tenantDemo->id,
            'name' => 'María Rodríguez',
            'email' => 'maria@example.com',
            'password' => bcrypt('password123'),
            'phone' => '+541155554444',
            'address' => 'Calle Falsa 123',
            'city' => 'Buenos Aires',
            'status' => 'active'
        ]);

        $orderDemo = \App\Models\Order::create([
            'tenant_id' => $tenantDemo->id,
            'customer_id' => $customerDemo->id,
            'order_number' => 'PED-2026-0001',
            'total' => 89.98,
            'currency' => 'USD',
            'exchange_rate' => 1.0000,
            'status' => 'delivered',
            'shipping_name' => 'María Rodríguez',
            'shipping_phone' => '+541155554444',
            'shipping_address' => 'Calle Falsa 123',
            'shipping_city' => 'Buenos Aires',
            'payment_method' => 'pago_movil',
            'payment_status' => 'paid',
            'notes' => 'Entregar por la tarde por favor.'
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $orderDemo->id,
            'product_id' => $prodTaladro->id,
            'product_name' => $prodTaladro->name,
            'sku' => $prodTaladro->sku,
            'quantity' => 1,
            'price' => 69.99,
            'total' => 69.99
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $orderDemo->id,
            'product_id' => $prodDestornillador->id,
            'product_name' => $prodDestornillador->name,
            'sku' => $prodDestornillador->sku,
            'quantity' => 1,
            'price' => 19.99,
            'total' => 19.99
        ]);
    }
}

