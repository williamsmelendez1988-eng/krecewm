<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - {{ $currentTenant->name }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Branding Dinámico de KreceWM -->
    <style>
        :root {
            --color-primary: {{ $currentTenant->primary_color ?? '#1e293b' }};
            --color-secondary: {{ $currentTenant->secondary_color ?? '#f59e0b' }};
            --color-primary-hover: {{ ($currentTenant->primary_color ?? '#1e293b') }}dd; /* Opacidad para hover */
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .bg-tenant-primary { background-color: var(--color-primary) !important; }
        .bg-tenant-primary-hover:hover { background-color: var(--color-primary-hover) !important; }
        .bg-tenant-secondary { background-color: var(--color-secondary) !important; }
        .text-tenant-primary { color: var(--color-primary) !important; }
        .text-tenant-secondary { color: var(--color-secondary) !important; }
        .border-tenant-primary { border-color: var(--color-primary) !important; }
        .ring-tenant-primary:focus { --tw-ring-color: var(--color-primary) !important; }
    </style>

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e293b">
    <link rel="apple-touch-icon" href="https://cdn-icons-png.flaticon.com/512/9752/9752284.png">
    
    <!-- Script para registrar Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registrado (Admin):', reg.scope))
                    .catch(err => console.error('Error al registrar Service Worker:', err));
            });
        }
    </script>
</head>
<body class="h-full">
    <div x-data="{ sidebarOpen: false }" class="min-h-full">
        <!-- Sidebar móvil -->
        <div x-show="sidebarOpen" class="relative z-40 lg:hidden" role="dialog" aria-modal="true" style="display: none;">
            <div class="fixed inset-0 bg-slate-600 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-40 flex">
                <div class="relative flex w-full max-w-xs flex-1 flex-col bg-slate-900 pt-5 pb-4">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button type="button" @click="sidebarOpen = false" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <span class="sr-only">Cerrar lateral</span>
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex flex-shrink-0 items-center px-4">
                        @if($currentTenant->logo)
                            <img class="h-8 w-auto" src="{{ asset('storage/' . $currentTenant->logo) }}" alt="{{ $currentTenant->name }}">
                        @else
                            <span class="text-xl font-bold text-white">{{ $currentTenant->getSetting('logo_text', $currentTenant->name) }}</span>
                        @endif
                    </div>
                    <div class="mt-5 h-0 flex-1 overflow-y-auto">
                        <nav class="space-y-1 px-2">
                            <a href="{{ route('tenant.admin.dashboard') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Dashboard
                            </a>
                            <a href="{{ route('tenant.admin.orders.index') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Pedidos
                            </a>
                            <a href="{{ route('tenant.admin.customers.index') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Clientes
                            </a>
                            <a href="{{ route('tenant.admin.products.index') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Productos
                            </a>
                            <a href="{{ route('tenant.admin.categories.index') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Categorías
                            </a>
                            <a href="{{ route('tenant.admin.brands.index') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Marcas
                            </a>
                            <a href="{{ route('tenant.admin.inventory.index') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Stock e Inventario
                            </a>
                            <a href="{{ route('tenant.admin.import.index') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Carga Masiva (Excel)
                            </a>
                            <a href="{{ route('tenant.admin.reports.index') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Reportes
                            </a>
                            <a href="{{ route('tenant.admin.staff.index') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Equipo de Trabajo
                            </a>
                            <a href="{{ route('tenant.admin.settings.index') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Configuración de Tienda
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col lg:bg-slate-900 lg:border-r lg:border-slate-800">
            <div class="flex h-16 flex-shrink-0 items-center px-6 bg-slate-950 border-b border-slate-850">
                @if($currentTenant->logo)
                    <img class="max-h-10 max-w-full" src="{{ asset('storage/' . $currentTenant->logo) }}" alt="{{ $currentTenant->name }}">
                @else
                    <span class="text-lg font-bold text-white tracking-tight truncate">{{ $currentTenant->getSetting('logo_text', $currentTenant->name) }}</span>
                @endif
                <span class="ml-auto px-1.5 py-0.5 text-[9px] font-semibold text-slate-300 bg-slate-800 rounded border border-slate-700">Tienda</span>
            </div>
            <div class="flex flex-1 flex-col overflow-y-auto px-4 py-4">
                <nav class="flex-1 space-y-1.5">
                    <a href="{{ route('tenant.admin.dashboard') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tenant.admin.dashboard') ? 'bg-tenant-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('tenant.admin.orders.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tenant.admin.orders.*') ? 'bg-tenant-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Pedidos
                    </a>

                    <a href="{{ route('tenant.admin.customers.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tenant.admin.customers.*') ? 'bg-tenant-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Clientes
                    </a>
                    
                    <a href="{{ route('tenant.admin.products.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tenant.admin.products.*') ? 'bg-tenant-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Productos
                    </a>

                    <a href="{{ route('tenant.admin.categories.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tenant.admin.categories.*') ? 'bg-tenant-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Categorías
                    </a>

                    <a href="{{ route('tenant.admin.brands.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tenant.admin.brands.*') ? 'bg-tenant-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 12h10m-8 5h8" />
                        </svg>
                        Marcas
                    </a>

                    <a href="{{ route('tenant.admin.inventory.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tenant.admin.inventory.*') ? 'bg-tenant-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Stock e Inventario
                    </a>

                    <a href="{{ route('tenant.admin.import.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tenant.admin.import.*') ? 'bg-tenant-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Carga Masiva (Excel)
                    </a>

                    <a href="{{ route('tenant.admin.reports.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tenant.admin.reports.*') ? 'bg-tenant-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Reportes
                    </a>

                    <a href="{{ route('tenant.admin.staff.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tenant.admin.staff.*') ? 'bg-tenant-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Equipo
                    </a>

                    <a href="{{ route('tenant.admin.settings.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tenant.admin.settings.*') ? 'bg-tenant-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Branding e Identidad
                    </a>

                    <a href="{{ route('tenant.admin.notifications.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tenant.admin.notifications.*') ? 'bg-tenant-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Notificaciones
                        @php $nb = auth()->user()->unreadNotifications->count(); @endphp
                        @if($nb > 0)
                        <span class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-bold text-white">{{ $nb > 9 ? '9+' : $nb }}</span>
                        @endif
                    </a>
                </nav>
                <div class="border-t border-slate-800 pt-4 mt-auto">
                    <a href="/" target="_blank" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-slate-100 transition-all duration-200">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Ver Tienda Pública
                    </a>
                </div>
            </div>
        </div>

        <!-- Cuerpo principal -->
        <div class="flex flex-col lg:pl-64">
            <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white border-b border-slate-200">
                <button type="button" @click="sidebarOpen = true" class="border-r border-slate-200 px-4 text-slate-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 lg:hidden">
                    <span class="sr-only">Abrir lateral</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div class="flex flex-1 justify-between px-6">
                    <div class="flex flex-1 items-center">
                        <h1 class="text-lg font-semibold text-slate-800">Panel Administrativo</h1>
                    </div>
                    <div class="ml-4 flex items-center gap-2 md:ml-6">

                        {{-- 🔔 Campana de Notificaciones con Polling cada 30s --}}
                        <div x-data="{
                                open: false,
                                unreadCount: {{ auth()->user()->unreadNotifications->count() }},
                                fetchUnreadCount() {
                                    fetch('/admin/api/notifications/unread-count', {
                                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                                    })
                                    .then(r => r.ok ? r.json() : null)
                                    .then(data => { if (data) this.unreadCount = data.count; })
                                    .catch(() => {});
                                },
                                init() {
                                    setInterval(() => this.fetchUnreadCount(), 30000);
                                }
                            }" class="relative">
                            <button @click="open = !open"
                                class="relative flex h-9 w-9 items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors focus:outline-none">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span x-show="unreadCount > 0"
                                      x-text="unreadCount > 9 ? '9+' : unreadCount"
                                      class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[9px] font-bold text-white"
                                      x-transition
                                      style="display: none;"></span>
                            </button>

                            {{-- Dropdown de últimas notificaciones --}}
                            <div x-show="open" @click.away="open = false"
                                class="absolute right-0 z-20 mt-2 w-80 origin-top-right rounded-xl bg-white shadow-xl ring-1 ring-black/5 focus:outline-none overflow-hidden"
                                style="display: none;">
                                <div class="flex items-center justify-between px-4 py-3 bg-slate-900">
                                    <span class="text-sm font-semibold text-white">Notificaciones</span>
                                    <span x-show="unreadCount > 0"
                                          x-text="unreadCount + ' nuevas'"
                                          class="text-xs font-bold text-white bg-rose-500 rounded-full px-1.5 py-0.5"
                                          style="display: none;"></span>
                                </div>
                                <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                                    @forelse(auth()->user()->notifications()->latest()->limit(5)->get() as $notif)
                                        @php $isRead = !is_null($notif->read_at); @endphp
                                        <a href="{{ route('tenant.admin.notifications.markRead', $notif->id) }}"
                                           class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition-colors {{ $isRead ? '' : 'bg-indigo-50/50' }}">
                                            <span class="text-lg mt-0.5">{{ $notif->data['icon'] ?? '🔔' }}</span>
                                            <div class="min-w-0">
                                                <p class="text-xs font-semibold {{ $isRead ? 'text-slate-600' : 'text-slate-900' }} truncate">{{ $notif->data['title'] ?? '' }}</p>
                                                <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ $notif->data['message'] ?? '' }}</p>
                                                <p class="text-[10px] text-slate-300 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                            </div>
                                            @if(!$isRead)
                                            <span class="h-2 w-2 rounded-full bg-indigo-500 flex-shrink-0 mt-1.5"></span>
                                            @endif
                                        </a>
                                    @empty
                                        <div class="px-4 py-6 text-center text-xs text-slate-400">Sin notificaciones aún</div>
                                    @endforelse
                                </div>
                                <div class="border-t border-slate-100 px-4 py-2.5 bg-slate-50">
                                    <a href="{{ route('tenant.admin.notifications.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                                        Ver todas las notificaciones →
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Perfil -->
                        <div x-data="{ open: false }" class="relative ml-1">
                            <div>
                                <button type="button" @click="open = !open" class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" id="user-menu-button">
                                    <div class="h-8 w-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold">
                                        {{ substr(auth()->user()->name, 0, 2) }}
                                    </div>
                                </button>
                            </div>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                                    <span class="inline-block mt-1 px-1.5 py-0.5 text-[10px] font-medium rounded capitalize bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ auth()->user()->role === 'tenant_admin' ? 'Owner' : 'Staff' }}
                                    </span>
                                </div>
                                <form method="POST" action="{{ route('tenant.admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-slate-50">Cerrar Sesión</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido -->
            <main class="flex-1 py-8 px-6 lg:px-8">
                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 shadow-sm flex items-center">
                        <svg class="h-5 w-5 text-emerald-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 rounded-lg bg-rose-50 border border-rose-200 p-4 text-rose-800 shadow-sm flex items-center">
                        <svg class="h-5 w-5 text-rose-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
@stack('scripts')
</html>
