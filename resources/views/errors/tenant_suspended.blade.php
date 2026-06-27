<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Negocio Suspendido</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="min-h-screen bg-slate-50 flex flex-col items-center justify-center px-4">
    <div class="text-center max-w-md">
        <div class="mx-auto mb-6 h-20 w-20 rounded-full bg-rose-100 flex items-center justify-center">
            <svg class="h-10 w-10 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">Negocio Temporalmente Suspendido</h1>
        <p class="mt-3 text-slate-500">
            La tienda <strong class="text-slate-700">{{ $tenant->name }}</strong> no está disponible en este momento. 
            Por favor contacta al administrador del negocio.
        </p>
        <p class="mt-2 text-sm text-slate-400">Email: {{ $tenant->contact_email }}</p>
        <div class="mt-8 pt-6 border-t border-slate-200">
            <p class="text-xs text-slate-400">Plataforma impulsada por <strong class="text-slate-500">KreceWM SaaS</strong></p>
        </div>
    </div>
</body>
</html>
