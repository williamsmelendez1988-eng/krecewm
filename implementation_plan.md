# Plan de Implementación — KreceWM Fase 5

## Objetivo
Elevar KreceWM al nivel de plataforma SaaS de producción con:
- **Dashboard Avanzado** del tenant con KPIs visuales y gráficas reales
- **Multi-Moneda** (USD + Bolívares venezolanos con tasa de cambio configurable)
- **Generación de Cotizaciones/Facturas en PDF** descargables
- **Sistema de Notificaciones In-App** para pedidos nuevos y stock bajo
- **Landing Page Pública de KreceWM** rediseñada y atractiva para conseguir nuevos negocios

---

## Módulo 1: Dashboard Avanzado del Tenant Admin

**Objetivo**: Reemplazar el dashboard básico actual con KPIs visuales y gráficas generadas con Chart.js CDN.

### Contenido del Dashboard
- **KPI Cards**: Ventas Hoy / Esta Semana / Este Mes (USD)
- **Gráfica de barras**: Ventas de los últimos 7 días (Chart.js)
- **Gráfica de dona**: Distribución de pedidos por estado
- **Tabla**: Top 5 productos más vendidos del mes
- **Tabla**: Últimos 5 pedidos con estado y acceso rápido
- **Alertas de Stock Bajo**: Productos con inventario crítico

### Archivos a Modificar/Crear
#### [MODIFY] `app/Http/Controllers/Admin/DashboardController.php`
#### [MODIFY] `resources/views/admin/dashboard.blade.php`

---

## Módulo 2: Multi-Moneda (USD + Bolívares)

**Objetivo**: Soportar precios duales para el mercado venezolano con tasa de cambio configurable por el administrador de la tienda.

### Lógica
- El admin configura la **tasa de cambio USD→Bs** en Configuración de Tienda
- El catálogo público muestra precio en **USD** y precio convertido en **Bs**
- El checkout acepta ambas monedas y el pedido guarda la moneda usada
- Pago Móvil opera en Bolívares, Efectivo/Transferencia en USD

### Archivos a Modificar/Crear
#### [NEW] `database/migrations/XXXX_add_currency_settings_to_orders_table.php`
#### [MODIFY] `resources/views/admin/settings/index.blade.php` — agregar campo de tasa de cambio
#### [MODIFY] `resources/views/tenant/catalog/index.blade.php` — mostrar precio en Bs
#### [MODIFY] `resources/views/tenant/catalog/show.blade.php` — precio dual
#### [MODIFY] `resources/views/tenant/checkout/index.blade.php` — selector de moneda

---

## Módulo 3: Cotizaciones & Facturas PDF

**Objetivo**: Generar documentos PDF profesionales de pedidos/cotizaciones con el logo y branding del negocio, usando la librería `barryvdh/laravel-dompdf`.

### Funcionalidades
- Botón "Descargar PDF" en el detalle del pedido (panel admin)
- Botón "Ver Cotización" en la página pública de éxito del checkout
- PDF incluye: logo del tenant, datos del negocio, tabla de productos, total, datos del cliente y método de pago
- Soporte para Pago Móvil y transferencias en el documento

### Archivos a Crear
#### [NEW] `app/Http/Controllers/Admin/InvoiceController.php`
#### [NEW] `resources/views/admin/invoices/pdf.blade.php`
#### [MODIFY] `resources/views/admin/orders/show.blade.php` — botón descargar PDF
#### [MODIFY] `routes/web.php` — ruta `/admin/orders/{order}/invoice`

---

## Módulo 4: Sistema de Notificaciones In-App

**Objetivo**: Alertar al admin de la tienda sobre eventos importantes sin necesidad de email externo.

### Notificaciones
- 🛒 **Pedido Nuevo**: Cuando llega un pedido por checkout público
- ⚠️ **Stock Bajo**: Cuando un producto cae por debajo del stock mínimo
- Badge de contador de notificaciones no leídas en el header del admin

### Implementación
- Tabla `notifications` de Laravel (ya incluida con el framework)
- Icono de campana en el header del layout admin con contador rojo
- Dropdown de últimas 5 notificaciones con marca "leída/no leída"

### Archivos a Crear/Modificar
#### [NEW] `app/Notifications/NewOrderNotification.php`
#### [NEW] `app/Notifications/LowStockNotification.php`
#### [MODIFY] `app/Http/Controllers/Tenant/CheckoutController.php` — disparar notificación al crear pedido
#### [MODIFY] `app/Services/InventoryService.php` — disparar notificación si stock < mínimo
#### [MODIFY] `resources/views/layouts/admin.blade.php` — campana de notificaciones
#### [NEW] `app/Http/Controllers/Admin/NotificationController.php`
#### [NEW] `resources/views/admin/notifications/index.blade.php`

---

## Módulo 5: Landing Page Pública de KreceWM Rediseñada

**Objetivo**: La URL `http://127.0.0.1:8000` (sin tenant activo) debe mostrar una landing page de marketing premium para atraer nuevos negocios a la plataforma.

### Secciones de la Landing
- **Hero**: Tagline + CTA de contacto con degradado premium
- **Para quién es**: Cards de sectores (Repuestos, Ferreterías, Agropecuarias, Tiendas)
- **Características**: 6 features clave con iconos (Multi-tienda, WhatsApp, Pago Móvil, Inventario, Analytics, PWA)
- **Planes**: Tabla de precios dinámica (Bronce, Plata, Oro)
- **Testimonios**: Sección de confianza con casos de uso
- **CTA Final**: Formulario de contacto / WhatsApp para registro

### Archivos a Modificar
#### [MODIFY] `resources/views/landing/index.blade.php`

---

## Verification Plan

### Automático
```bash
php artisan migrate --pretend
php artisan route:list | findstr invoice
php artisan route:list | findstr notification
```

### Manual
- Dashboard tenant → ver KPIs del mes actual, gráficas con datos reales
- Configuración → ingresar tasa de cambio → catálogo muestra precio dual
- Crear pedido → verificar que llega notificación en el header del admin
- Detalle del pedido → descargar PDF → verifica que incluye branding del negocio
- Ir a `http://127.0.0.1:8000` (sin tenant) → landing page premium de KreceWM
