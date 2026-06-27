<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KreceWM — Digitaliza tu Negocio y Vende Más</title>
    <meta name="description" content="KreceWM es la plataforma SaaS que digitaliza tu negocio de repuestos, ferretería, agropecuaria o tienda especializada. Catálogo online, pedidos por WhatsApp, inventario y más.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .gradient-hero {
            background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(99,102,241,0.25) 0%, transparent 60%),
                        linear-gradient(180deg, #0f172a 0%, #1e1b4b 40%, #0f172a 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #818cf8, #a78bfa, #34d399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .card-glow:hover {
            box-shadow: 0 0 0 1px rgba(99,102,241,0.3), 0 20px 40px -10px rgba(99,102,241,0.15);
        }
        .feature-icon {
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(167,139,250,0.1));
            border: 1px solid rgba(99,102,241,0.2);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .floating { animation: float 4s ease-in-out infinite; }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(99,102,241,0.3); }
            50% { box-shadow: 0 0 40px rgba(99,102,241,0.6); }
        }
        .glow-btn { animation: pulse-glow 2.5s ease-in-out infinite; }
        .hero-grid {
            background-image: radial-gradient(circle, rgba(99,102,241,0.08) 1px, transparent 1px);
            background-size: 32px 32px;
        }
        .sector-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.04), rgba(255,255,255,0.01));
            border: 1px solid rgba(255,255,255,0.08);
            transition: all 0.3s ease;
        }
        .sector-card:hover {
            background: linear-gradient(135deg, rgba(99,102,241,0.12), rgba(167,139,250,0.06));
            border-color: rgba(99,102,241,0.3);
            transform: translateY(-4px);
        }
        .plan-card {
            transition: all 0.3s ease;
        }
        .plan-card:hover { transform: translateY(-6px); }
        .plan-popular {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none !important;
        }
    </style>
</head>
<body class="bg-slate-950 text-white overflow-x-hidden">

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- NAVBAR --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<nav x-data="{ open: false }" class="fixed top-0 z-50 w-full border-b border-white/5 bg-slate-950/80 backdrop-blur-xl">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-white tracking-tight">KreceWM</span>
            </div>

            <div class="hidden md:flex items-center gap-8">
                <a href="#sectores" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Sectores</a>
                <a href="#caracteristicas" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Características</a>
                <a href="#planes" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Planes</a>
                <a href="#contacto" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Contacto</a>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden md:inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-slate-300 hover:text-white transition-colors">
                    Iniciar Sesión
                </a>
                <a href="#contacto" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 transition-all glow-btn">
                    Empezar Gratis
                </a>
            </div>
        </div>
    </div>
</nav>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- HERO --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<section class="gradient-hero hero-grid relative min-h-screen flex items-center pt-16">
    {{-- Orbs decorativos --}}
    <div class="absolute top-24 left-1/4 h-72 w-72 rounded-full bg-indigo-600/20 blur-3xl"></div>
    <div class="absolute bottom-24 right-1/4 h-64 w-64 rounded-full bg-violet-600/15 blur-3xl"></div>

    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-24 text-center">
        {{-- Badge --}}
        <div class="mb-8 inline-flex items-center gap-2 rounded-full border border-indigo-500/30 bg-indigo-500/10 px-4 py-1.5 text-sm font-medium text-indigo-300">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            Plataforma SaaS · Hecha para Venezuela y LATAM
        </div>

        {{-- Headline --}}
        <h1 class="mx-auto max-w-4xl text-5xl font-black leading-tight tracking-tight sm:text-6xl lg:text-7xl">
            Tu negocio,<br>
            <span class="gradient-text">digitalizado y listo</span><br>
            para vender más
        </h1>

        <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-400 leading-relaxed">
            KreceWM convierte tu tienda de repuestos, ferretería, agropecuaria o negocio especializado
            en una plataforma de ventas online completa — con catálogo digital, pedidos por WhatsApp,
            inventario inteligente y pagos venezolanos integrados.
        </p>

        {{-- CTAs --}}
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="#contacto" id="cta-hero-principal"
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-8 py-4 text-base font-bold text-white shadow-xl hover:bg-indigo-500 transition-all duration-200 glow-btn">
                🚀 Quiero mi Tienda Digital
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            <a href="#caracteristicas"
               class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-8 py-4 text-base font-semibold text-slate-300 hover:bg-white/10 hover:text-white transition-all duration-200">
                Ver Características
            </a>
        </div>

        {{-- Stats --}}
        <div class="mt-16 grid grid-cols-3 gap-8 max-w-lg mx-auto">
            <div class="text-center">
                <p class="text-3xl font-black text-white">100%</p>
                <p class="text-xs text-slate-500 mt-1">Venezolano</p>
            </div>
            <div class="text-center border-x border-white/10">
                <p class="text-3xl font-black text-white">5min</p>
                <p class="text-xs text-slate-500 mt-1">Setup inicial</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-black text-white">∞</p>
                <p class="text-xs text-slate-500 mt-1">Productos</p>
            </div>
        </div>

        {{-- Mock UI flotante --}}
        <div class="mt-20 relative mx-auto max-w-3xl floating">
            <div class="rounded-2xl border border-white/10 bg-slate-900/80 backdrop-blur-sm shadow-2xl overflow-hidden">
                <div class="flex items-center gap-1.5 px-4 py-3 bg-slate-800/60 border-b border-white/5">
                    <span class="h-3 w-3 rounded-full bg-rose-500/60"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-500/60"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-500/60"></span>
                    <span class="ml-4 text-xs text-slate-500">Panel KreceWM — Repuestos El Maestro</span>
                </div>
                <div class="p-6 grid grid-cols-4 gap-4">
                    <div class="col-span-1 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 p-4 text-white">
                        <p class="text-xs text-indigo-200 font-semibold">Ventas Hoy</p>
                        <p class="text-2xl font-black mt-1">$420</p>
                        <p class="text-xs text-indigo-300 mt-1">↑ 12% vs ayer</p>
                    </div>
                    <div class="col-span-1 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 p-4 text-white">
                        <p class="text-xs text-emerald-200 font-semibold">Pedidos</p>
                        <p class="text-2xl font-black mt-1">18</p>
                        <p class="text-xs text-emerald-300 mt-1">5 pendientes</p>
                    </div>
                    <div class="col-span-1 rounded-xl bg-gradient-to-br from-violet-500 to-violet-700 p-4 text-white">
                        <p class="text-xs text-violet-200 font-semibold">Clientes</p>
                        <p class="text-2xl font-black mt-1">247</p>
                        <p class="text-xs text-violet-300 mt-1">+3 hoy</p>
                    </div>
                    <div class="col-span-1 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 p-4 text-white">
                        <p class="text-xs text-amber-200 font-semibold">Productos</p>
                        <p class="text-2xl font-black mt-1">1,842</p>
                        <p class="text-xs text-amber-300 mt-1">en catálogo</p>
                    </div>
                </div>
                <div class="px-6 pb-4">
                    <div class="h-24 rounded-xl bg-slate-800/60 flex items-end gap-2 px-4 pb-3 pt-2">
                        @foreach([40, 65, 45, 80, 55, 90, 70] as $h)
                        <div class="flex-1 rounded-t-md bg-indigo-500/70" style="height: {{ $h }}%;"></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- SECTORES --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<section id="sectores" class="py-24 bg-slate-950">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-sm font-bold text-indigo-400 uppercase tracking-widest mb-3">¿Para quién es KreceWM?</p>
            <h2 class="text-4xl font-black text-white">Hecho para tu sector</h2>
            <p class="mt-4 text-slate-400 max-w-xl mx-auto">Adaptado a los negocios venezolanos que necesitan vender online con catálogos grandes y precios duales.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['🔧', 'Repuestos Automotrices', 'Catálogos de miles de piezas con código OEM, búsqueda avanzada y precios en USD/Bs.'],
                ['⚙️', 'Motores y Cajas', 'Fichas técnicas, compatibilidad por modelo/año, consulta por WhatsApp directo.'],
                ['🔩', 'Ferreterías', 'Inventario masivo, unidades de medida flexibles, carga Excel de 1000+ productos.'],
                ['🌱', 'Agropecuarias', 'Gestión de productos con temporadas, presentaciones y control de stock mínimo.'],
                ['🛍️', 'Tiendas Especializadas', 'Catálogo visual con filtros por marca/categoría y proceso de compra rápido.'],
                ['💼', 'Distribuidoras', 'Multi-usuario, roles de staff, reportes de ventas y análisis por período.'],
            ] as [$emoji, $title, $desc])
            <div class="sector-card rounded-2xl p-6">
                <div class="text-4xl mb-4">{{ $emoji }}</div>
                <h3 class="text-lg font-bold text-white mb-2">{{ $title }}</h3>
                <p class="text-sm text-slate-400 leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- CARACTERÍSTICAS --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<section id="caracteristicas" class="py-24 bg-slate-900">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-sm font-bold text-indigo-400 uppercase tracking-widest mb-3">Todo incluido</p>
            <h2 class="text-4xl font-black text-white">Funcionalidades que importan</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['🛒', 'Catálogo + Carrito Online', 'Vitrina pública con búsqueda, filtros, imágenes y ficha de producto. Listo desde el día 1.'],
                ['💬', 'Pedidos por WhatsApp', 'Clientes hacen su pedido online y reciben confirmación vía WhatsApp automáticamente.'],
                ['📱', 'Pago Móvil Venezuela', 'Datos de Pago Móvil (banco, teléfono, RIF) mostrados en el checkout y en el PDF.'],
                ['💱', 'Precios USD + Bolívares', 'Tasa de cambio configurable. El catálogo muestra el equivalente en Bs en tiempo real.'],
                ['📦', 'Control de Inventario', 'Stock mínimo, alertas automáticas, movimientos de entrada/salida con historial.'],
                ['📊', 'Dashboard + Reportes', 'KPIs de ventas con gráficas, top productos del mes y exportación CSV.'],
                ['📄', 'Cotizaciones en PDF', 'Genera y descarga PDFs profesionales de cada pedido con los datos de pago incluidos.'],
                ['🔔', 'Notificaciones en Tiempo Real', 'Alertas en el panel cuando llega un pedido nuevo o el stock baja del mínimo.'],
                ['🏗️', 'Multi-tienda SaaS', 'Arquitectura multi-tenant: cada negocio tiene su propia URL, colores y datos aislados.'],
            ] as [$icon, $title, $desc])
            <div class="group rounded-2xl border border-white/5 bg-slate-800/40 p-6 card-glow transition-all duration-300 cursor-default">
                <div class="feature-icon inline-flex h-12 w-12 items-center justify-center rounded-xl text-2xl mb-4">
                    {{ $icon }}
                </div>
                <h3 class="text-base font-bold text-white mb-2">{{ $title }}</h3>
                <p class="text-sm text-slate-400 leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- PLANES --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<section id="planes" class="py-24 bg-slate-950">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-sm font-bold text-indigo-400 uppercase tracking-widest mb-3">Precios transparentes</p>
            <h2 class="text-4xl font-black text-white">Elige tu plan</h2>
            <p class="mt-4 text-slate-400">Sin cargos ocultos. Puedes migrar de plan en cualquier momento.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">

            {{-- Bronce --}}
            <div class="plan-card rounded-2xl border border-white/10 bg-slate-900 p-8">
                <div class="text-amber-500 text-3xl mb-4">🥉</div>
                <h3 class="text-xl font-black text-white">Bronce</h3>
                <p class="text-slate-400 text-sm mt-1 mb-6">Para empezar a digitalizarse</p>
                <div class="mb-6">
                    <span class="text-4xl font-black text-white">$25</span>
                    <span class="text-slate-400 text-sm">/mes</span>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach(['Hasta 200 productos', 'Catálogo online público', 'Pedidos WhatsApp', 'Panel admin básico', '1 usuario staff', 'Soporte por WhatsApp'] as $feature)
                    <li class="flex items-center gap-2.5 text-sm text-slate-300">
                        <svg class="h-4 w-4 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="#contacto" class="block w-full text-center rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-bold text-white hover:bg-white/10 transition-colors">
                    Comenzar
                </a>
            </div>

            {{-- Plata (Popular) --}}
            <div class="plan-card plan-popular rounded-2xl p-8 relative shadow-2xl shadow-indigo-500/20">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-400 px-4 py-1 text-xs font-black text-amber-900">⭐ MÁS POPULAR</span>
                </div>
                <div class="text-slate-300 text-3xl mb-4">🥈</div>
                <h3 class="text-xl font-black text-white">Plata</h3>
                <p class="text-indigo-200 text-sm mt-1 mb-6">El equilibrio perfecto</p>
                <div class="mb-6">
                    <span class="text-4xl font-black text-white">$55</span>
                    <span class="text-indigo-200 text-sm">/mes</span>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach(['Productos ilimitados', 'Carga masiva Excel', 'Inventario + Alertas', 'Pago Móvil + PDF', 'Precios USD/Bs', '3 usuarios staff', 'Reportes + Analytics', 'Branding personalizado'] as $feature)
                    <li class="flex items-center gap-2.5 text-sm text-white">
                        <svg class="h-4 w-4 text-white/80 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="#contacto" class="block w-full text-center rounded-xl bg-white py-3 text-sm font-black text-indigo-700 hover:bg-indigo-50 transition-colors">
                    Empezar Ahora
                </a>
            </div>

            {{-- Oro --}}
            <div class="plan-card rounded-2xl border border-amber-500/20 bg-slate-900 p-8">
                <div class="text-amber-400 text-3xl mb-4">🥇</div>
                <h3 class="text-xl font-black text-white">Oro</h3>
                <p class="text-slate-400 text-sm mt-1 mb-6">Para negocios en expansión</p>
                <div class="mb-6">
                    <span class="text-4xl font-black text-white">$99</span>
                    <span class="text-slate-400 text-sm">/mes</span>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach(['Todo lo de Plata', 'Dominio personalizado', 'Usuarios ilimitados', 'API REST acceso', 'Multi-sucursal', 'SLA prioritario 24/7', 'Onboarding dedicado', 'Facturación electrónica*'] as $feature)
                    <li class="flex items-center gap-2.5 text-sm text-slate-300">
                        <svg class="h-4 w-4 text-amber-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="#contacto" class="block w-full text-center rounded-xl border border-amber-500/30 bg-amber-500/10 py-3 text-sm font-bold text-amber-400 hover:bg-amber-500/20 transition-colors">
                    Contactar
                </a>
            </div>
        </div>

        <p class="text-center text-xs text-slate-600 mt-8">* Facturación electrónica en desarrollo. Precios en USD. Disponible pago en Bolívares a tasa del día.</p>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- TESTIMONIOS --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-slate-900">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-black text-white">Lo que dicen nuestros usuarios</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['Carlos M.', 'Repuestos del Centro', '⭐⭐⭐⭐⭐', 'Antes perdía ventas porque no tenía catálogo online. Con KreceWM mis clientes consultan el inventario directamente por WhatsApp y el pedido llega solo al sistema. Increíble.'],
                ['Marisol R.', 'Ferretería La Unión', '⭐⭐⭐⭐⭐', 'La carga masiva de Excel me ahorró semanas de trabajo. Subí 800 productos en una tarde. El sistema de tasa de cambio es perfecto para Venezuela.'],
                ['José A.', 'Agropecuaria El Campo', '⭐⭐⭐⭐⭐', 'El dashboard con las gráficas de ventas me da claridad para tomar decisiones. Ahora sé cuáles productos mueven más y cuáles tengo que reponer.'],
            ] as [$name, $biz, $stars, $quote])
            <div class="rounded-2xl border border-white/5 bg-slate-800/50 p-6">
                <div class="text-xl mb-3">{{ $stars }}</div>
                <p class="text-slate-300 text-sm leading-relaxed mb-5 italic">"{{ $quote }}"</p>
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ substr($name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white">{{ $name }}</p>
                        <p class="text-xs text-slate-400">{{ $biz }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- PROBAR DEMO — Solo visible en entorno local de desarrollo --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
@if(app()->environment('local'))
<section id="demo" class="py-24 bg-slate-950 border-t border-dashed border-indigo-500/30">
    <div class="mx-auto max-w-4xl px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-500/30 bg-amber-500/10 px-4 py-1.5 text-sm font-medium text-amber-300 mb-4">
                <span class="h-1.5 w-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                Entorno de Desarrollo
            </div>
            <h2 class="text-3xl font-black text-white">🧪 Probar Demo</h2>
            <p class="mt-3 text-slate-400 max-w-xl mx-auto">
                Accede directamente a las tiendas demo sin necesidad de configurar el archivo hosts.
                Haz clic en una tarjeta para entrar como inquilino.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-2xl mx-auto">
            {{-- Demo Tenant: Ferretería Central Demo --}}
            <a href="/dev/switch-tenant/demo"
               class="group block rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800/80 to-slate-900/80 p-6 hover:border-indigo-500/40 hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-2xl shadow-lg">
                        🔩
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white group-hover:text-indigo-300 transition-colors">Ferretería Central Demo</h3>
                        <p class="text-xs text-slate-500 font-mono">demo.krecewm.test</p>
                    </div>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed mb-4">
                    Tienda de ferretería con catálogo de ejemplo, pedidos de prueba y configuración de pago móvil.
                </p>
                <div class="flex items-center gap-2 text-xs font-semibold text-indigo-400 group-hover:text-indigo-300 transition-colors">
                    Entrar al panel
                    <svg class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </a>

            {{-- Demo Tenant: Repuestos López --}}
            <a href="/dev/switch-tenant/repuestos"
               class="group block rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800/80 to-slate-900/80 p-6 hover:border-emerald-500/40 hover:shadow-xl hover:shadow-emerald-500/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-2xl shadow-lg">
                        🔧
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white group-hover:text-emerald-300 transition-colors">Repuestos López</h3>
                        <p class="text-xs text-slate-500 font-mono">repuestos.krecewm.test</p>
                    </div>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed mb-4">
                    Tienda de repuestos automotrices con inventario extenso, búsqueda por código OEM y precios USD/Bs.
                </p>
                <div class="flex items-center gap-2 text-xs font-semibold text-emerald-400 group-hover:text-emerald-300 transition-colors">
                    Entrar al panel
                    <svg class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </a>
        </div>

        <div class="text-center mt-8">
            <p class="text-xs text-slate-600">
                ⚠️ Esta sección solo aparece cuando <code class="px-1.5 py-0.5 bg-slate-800 rounded text-amber-400 text-[10px]">APP_ENV=local</code>
            </p>
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- CTA FINAL + CONTACTO --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<section id="contacto" class="py-24 bg-slate-950">
    <div class="mx-auto max-w-3xl px-6 lg:px-8 text-center">
        <div class="rounded-3xl border border-indigo-500/20 bg-gradient-to-br from-indigo-900/40 to-violet-900/20 p-12">
            <div class="text-5xl mb-6">🚀</div>
            <h2 class="text-4xl font-black text-white mb-4">¿Listo para digitalizar tu negocio?</h2>
            <p class="text-slate-400 mb-8 text-lg">
                Escríbenos por WhatsApp y en menos de 24 horas tendrás tu tienda online funcionando.
                Sin contratos. Sin complicaciones.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="https://wa.me/584120000000?text=Hola!%20Me%20interesa%20KreceWM%20para%20mi%20negocio"
                   target="_blank"
                   id="cta-whatsapp-contacto"
                   class="inline-flex items-center justify-center gap-3 rounded-xl bg-emerald-500 px-8 py-4 text-base font-bold text-white shadow-xl hover:bg-emerald-400 transition-all duration-200">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.374 0 0 5.373 0 12c0 2.117.549 4.099 1.508 5.819L0 24l6.335-1.652A11.962 11.962 0 0012 24c6.626 0 12-5.373 12-12S18.626 0 12 0zm0 22c-1.885 0-3.653-.49-5.192-1.349L2.9 21.89l1.272-3.791A9.972 9.972 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" fill-rule="evenodd" clip-rule="evenodd"/>
                    </svg>
                    Escribir por WhatsApp
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-8 py-4 text-base font-semibold text-slate-300 hover:bg-white/10 hover:text-white transition-all duration-200">
                    Ya tengo cuenta → Iniciar Sesión
                </a>
            </div>

            <div class="mt-8 flex items-center justify-center gap-6 text-xs text-slate-600">
                <span>✓ Sin tarjeta de crédito</span>
                <span>✓ Configuración en 5 minutos</span>
                <span>✓ Soporte en español</span>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- FOOTER --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<footer class="border-t border-white/5 bg-slate-950 py-12">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-white">KreceWM</span>
                <span class="text-xs text-slate-600">— Plataforma SaaS de Digitalización Comercial</span>
            </div>
            <p class="text-xs text-slate-600">
                © {{ date('Y') }} KreceWM. Todos los derechos reservados. Hecho con ❤️ para Venezuela y LATAM.
            </p>
        </div>
    </div>
</footer>

</body>
</html>
