<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Avanzado -->
    <title>@yield('title', $tenant->name)</title>
    <meta name="description" content="@yield('meta_description', 'Bienvenido a ' . $tenant->name . ' en KreceWM')">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ request()->url() }}">
    
    <!-- Open Graph (Facebook / WhatsApp) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', $tenant->name)">
    <meta property="og:description" content="@yield('meta_description', 'Bienvenido a ' . $tenant->name . ' en KreceWM')">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="{{ $tenant->name }}">
    @if($tenant->logo)
        <meta property="og:image" content="{{ asset('storage/' . $tenant->logo) }}">
    @endif

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', $tenant->name)">
    <meta name="twitter:description" content="@yield('meta_description', 'Bienvenido a ' . $tenant->name . ' en KreceWM')">
    @if($tenant->logo)
        <meta name="twitter:image" content="{{ asset('storage/' . $tenant->logo) }}">
    @endif

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e293b">
    <link rel="apple-touch-icon" href="https://cdn-icons-png.flaticon.com/512/9752/9752284.png">
    
    <!-- Script para registrar Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registrado:', reg.scope))
                    .catch(err => console.error('Error al registrar Service Worker:', err));
            });
        }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if($tenant->getSetting('favicon'))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $tenant->getSetting('favicon')) }}">
    @endif

    <!-- Branding Dinámico -->
    <style>
        :root {
            --color-primary: {{ $tenant->primary_color ?? '#1e293b' }};
            --color-secondary: {{ $tenant->secondary_color ?? '#f59e0b' }};
            --color-primary-hover: {{ ($tenant->primary_color ?? '#1e293b') }}dd;
        }
        body { font-family: 'Outfit', sans-serif; }
        
        .bg-tenant-primary { background-color: var(--color-primary) !important; }
        .bg-tenant-primary-hover:hover { background-color: var(--color-primary-hover) !important; }
        .bg-tenant-secondary { background-color: var(--color-secondary) !important; }
        .text-tenant-primary { color: var(--color-primary) !important; }
        .text-tenant-secondary { color: var(--color-secondary) !important; }
        .border-tenant-primary { border-color: var(--color-primary) !important; }
        .ring-tenant-primary:focus { --tw-ring-color: var(--color-primary) !important; }
    </style>
</head>
<body class="h-full flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-slate-100 sticky top-0 z-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logotipo / Branding -->
                <div class="flex items-center">
                    <a href="/" class="flex flex-shrink-0 items-center">
                        @if($tenant->logo)
                            <img class="h-9 w-auto" src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}">
                        @else
                            <span class="text-2xl font-bold tracking-tight text-slate-800">{{ $tenant->getSetting('logo_text', $tenant->name) }}</span>
                        @endif
                    </a>
                </div>

                <!-- Búsqueda y Navegación Básica -->
                <div class="flex items-center space-x-6">
                    <a href="{{ route('tenant.cart.index') }}" class="relative inline-flex items-center p-2 text-sm font-medium text-center text-slate-600 hover:text-tenant-primary transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                        @if(session('cart') && count(session('cart')) > 0)
                            <div class="absolute inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-red-500 rounded-full -top-1 -right-1">
                                {{ collect(session('cart'))->sum('qty') }}
                            </div>
                        @endif
                    </a>
                    <a href="/admin/login" class="text-sm font-medium text-slate-600 hover:text-tenant-primary transition-all duration-200">
                        Admin Login
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4">{{ $tenant->name }}</h3>
                    <p class="text-sm text-slate-400">{{ $tenant->address }}</p>
                    <p class="text-sm text-slate-400 mt-1">{{ $tenant->city }}</p>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4">Contacto</h3>
                    <p class="text-sm text-slate-400">Email: {{ $tenant->contact_email }}</p>
                    <p class="text-sm text-slate-400 mt-1">Teléfono: {{ $tenant->contact_phone }}</p>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4">Plataforma</h3>
                    <p class="text-sm text-slate-500">Tienda impulsada por <span class="font-semibold text-slate-300">KreceWM SaaS</span></p>
                </div>
            </div>
            <div class="mt-8 border-t border-slate-800 pt-8 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} {{ $tenant->name }}. Todos los derechos reservados.
            </div>
        </div>
    </footer>
</body>
</html>
