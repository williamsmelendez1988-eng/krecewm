# KreceWM — Fase 5: Dashboard, Multi-Moneda, PDF, Notificaciones & Landing

## Módulo 1: Dashboard Avanzado del Tenant
- [x] Actualizar DashboardController con KPIs reales (hoy, semana, mes)
- [x] Crear la vista admin/dashboard.blade.php con Chart.js
- [x] KPI cards de ventas (total $ vendidos)
- [x] Gráfica de barras: ventas últimos 7 días
- [x] Gráfica de dona: pedidos por estado
- [x] Tabla: Top 5 productos más vendidos
- [x] Tabla: Últimos 5 pedidos con acceso rápido
- [x] Alertas de stock bajo en el dashboard

## Módulo 2: Multi-Moneda (USD + Bolívares)
- [x] Migración: agregar campo currency y exchange_rate a orders
- [x] Agregar campo de tasa de cambio en la Configuración de Tienda
- [x] Mostrar precio dual (USD + Bs) en catálogo público
- [x] Mostrar precio dual en página de detalle de producto
- [x] Selector de moneda en el checkout

## Módulo 3: Cotizaciones & Facturas PDF
- [x] Instalar y configurar barryvdh/laravel-dompdf
- [x] Crear InvoiceController con método generatePdf
- [x] Crear la vista PDF (resources/views/admin/invoices/pdf.blade.php)
- [x] Agregar botón "Descargar Factura PDF" en el detalle del pedido
- [x] Agregar ruta /admin/orders/{order}/invoice

## Módulo 4: Notificaciones In-App
- [x] Ejecutar migracion de la tabla notifications de Laravel
- [x] Crear NewOrderNotification.php
- [x] Crear LowStockNotification.php
- [x] Disparar NewOrderNotification desde CheckoutController
- [x] Disparar LowStockNotification desde InventoryService
- [x] Agregar campana de notificaciones en el header del admin
- [x] Crear NotificationController (index, markAsRead, markAllRead)
- [x] Crear la vista de notificaciones

## Módulo 5: Landing Page KreceWM Rediseñada
- [x] Rediseñar resources/views/landing/index.blade.php
- [x] Sección Hero premium con CTA
- [x] Cards de sectores de negocio target
- [x] 6 feature cards
- [x] Tabla de planes (Bronce, Plata, Oro) con precios
- [x] Testimonios
- [x] Sección CTA final con WhatsApp
