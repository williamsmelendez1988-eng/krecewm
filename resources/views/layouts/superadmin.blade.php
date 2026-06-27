<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin - KreceWM SaaS</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
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
                    .then(reg => console.log('Service Worker registrado (SuperAdmin):', reg.scope))
                    .catch(err => console.error('Error al registrar Service Worker:', err));
            });
        }
    </script>
</head>
<body class="h-full">
    <div x-data="{ sidebarOpen: false }" class="min-h-full">
        <!-- Sidebar para móviles -->
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
                        <span class="text-2xl font-bold tracking-tight text-white">Krece<span class="text-blue-500">WM</span></span>
                    </div>
                    <div class="mt-5 h-0 flex-1 overflow-y-auto">
                        <nav class="space-y-1 px-2">
                            <a href="{{ route('superadmin.dashboard') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Dashboard
                            </a>
                            <a href="{{ route('superadmin.tenants.index') }}" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                Negocios (Tenants)
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar estático para desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col lg:bg-slate-900 lg:border-r lg:border-slate-850">
            <div class="flex h-16 flex-shrink-0 items-center px-6 bg-slate-950">
                <span class="text-2xl font-bold tracking-tight text-white">Krece<span class="text-blue-500">WM</span></span>
                <span class="ml-2 px-2 py-0.5 text-[10px] font-semibold text-blue-300 bg-blue-900/30 rounded border border-blue-800/40">SaaS Admin</span>
            </div>
            <div class="flex flex-1 flex-col overflow-y-auto px-4 py-4">
                <nav class="flex-1 space-y-1.5">
                    <a href="{{ route('superadmin.dashboard') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('superadmin.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('superadmin.tenants.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('superadmin.tenants.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Negocios (Tenants)
                    </a>
                </nav>
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
                        <h1 class="text-lg font-semibold text-slate-800">Panel Super Admin</h1>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <!-- Perfil -->
                        <div x-data="{ open: false }" class="relative ml-3">
                            <div>
                                <button type="button" @click="open = !open" class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" id="user-menu-button">
                                    <div class="h-8 w-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold">SA</div>
                                </button>
                            </div>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
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
</html>
