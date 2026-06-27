<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso Administrativo - {{ $tenant->name }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-tenant-primary { background-color: var(--color-primary) !important; }
        .bg-tenant-primary-hover:hover { background-color: var(--color-primary-hover) !important; }
        .text-tenant-primary { color: var(--color-primary) !important; }
    </style>
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            @if($tenant->logo)
                <img class="mx-auto h-12 w-auto" src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}">
            @else
                <span class="text-3xl font-extrabold tracking-tight text-slate-900">{{ $tenant->getSetting('logo_text', $tenant->name) }}</span>
            @endif
            <p class="mt-2 text-sm text-slate-600">
                Panel Administrativo
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 border border-slate-200/80 shadow-xl sm:rounded-xl sm:px-10">
            @if($errors->any())
                <div class="mb-6 rounded-lg bg-rose-50 border border-rose-200 p-4 text-rose-800 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('tenant.admin.login.post') }}" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Correo Electrónico</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-lg border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border transition-all duration-200" placeholder="correo@tienda.com" value="{{ old('email') }}">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Contraseña</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-lg border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border transition-all duration-200" placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 bg-slate-50 text-slate-600 focus:ring-slate-500">
                        <label for="remember" class="ml-2 block text-sm text-slate-500">Recordarme</label>
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-lg bg-tenant-primary bg-tenant-primary-hover px-4 py-3 text-sm font-semibold text-white shadow-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 transition-all duration-200">
                        Ingresar al Panel
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
