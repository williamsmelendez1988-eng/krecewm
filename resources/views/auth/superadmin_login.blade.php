<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin Login - KreceWM</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-900 via-slate-950 to-black">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <span class="text-4xl font-extrabold tracking-tight text-white">Krece<span class="text-blue-500">WM</span></span>
            <span class="block mt-2 text-xs font-semibold text-blue-400 bg-blue-900/30 w-fit mx-auto px-2.5 py-0.5 rounded-full border border-blue-800/40 uppercase tracking-widest">SaaS Master Control</span>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-slate-900/60 backdrop-blur-md py-8 px-4 border border-slate-800/60 shadow-2xl sm:rounded-xl sm:px-10">
            @if($errors->any())
                <div class="mb-6 rounded-lg bg-rose-950/30 border border-rose-800/50 p-4 text-rose-300 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300">Correo Electrónico</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-lg border-slate-800 bg-slate-950/80 px-4 py-3 text-white placeholder-slate-500 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border transition-all duration-200" placeholder="admin@krecewm.com" value="{{ old('email') }}">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300">Contraseña</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-lg border-slate-800 bg-slate-950/80 px-4 py-3 text-white placeholder-slate-500 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border transition-all duration-200" placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-800 bg-slate-950 text-blue-600 focus:ring-blue-500">
                        <label for="remember" class="ml-2 block text-sm text-slate-400">Recordarme</label>
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all duration-200 hover:shadow-blue-500/20">
                        Iniciar Sesión
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
