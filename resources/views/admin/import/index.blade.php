@extends('layouts.admin')

@section('content')
<div class="max-w-4xl space-y-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900 font-sans">Carga Masiva de Productos</h2>
        <p class="text-sm text-slate-500 font-sans">Importa tu catálogo de productos y existencias de stock de forma rápida desde un archivo Excel o CSV.</p>
    </div>

    {{-- Panel de Acciones Primarias --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Descargar Plantilla --}}
        <div class="bg-slate-50 p-6 rounded-xl border border-slate-200 flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Paso 1</span>
                <h3 class="text-base font-bold text-slate-800 mt-1">Descarga la Plantilla</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Usa nuestro archivo base Excel pre-configurado para rellenar tus productos con las columnas y formatos correctos.</p>
            </div>
            <div class="mt-6">
                <a href="{{ route('tenant.admin.import.template') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 shadow-sm rounded-lg hover:bg-slate-100 transition-colors">
                    Descargar Plantilla (.xlsx)
                </a>
            </div>
        </div>

        {{-- Subir Catálogo --}}
        <div class="md:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Paso 2</span>
            <h3 class="text-base font-bold text-slate-800 mt-1">Sube tu Archivo</h3>
            
            <form method="POST" action="{{ route('tenant.admin.import.post') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="import_file" class="block text-sm font-medium text-slate-700 sr-only">Archivo de catálogo</label>
                    <input type="file" name="import_file" id="import_file" required accept=".xlsx,.csv"
                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                    <p class="mt-2 text-[11px] text-slate-400">Formatos soportados: Excel (.xlsx) o CSV. Peso máximo: 4MB.</p>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 text-xs font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow rounded-lg transition-all">
                        Procesar Catálogo &rarr;
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- Reporte de Resultados (si existen) --}}
    @if(isset($results))
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3">Resumen del Proceso</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Éxitos --}}
                <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                        ✓
                    </div>
                    <div>
                        <span class="text-2xl font-extrabold text-emerald-800 block">{{ $results['success_count'] }}</span>
                        <span class="text-xs text-emerald-600 font-semibold uppercase tracking-wider">Productos Importados con Éxito</span>
                    </div>
                </div>

                {{-- Errores --}}
                <div class="p-4 rounded-lg {{ empty($results['errors']) ? 'bg-slate-50 border border-slate-200 text-slate-400' : 'bg-rose-50 border border-rose-200 text-rose-800' }} flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-full {{ empty($results['errors']) ? 'bg-slate-100 text-slate-400' : 'bg-rose-100 text-rose-700' }} flex items-center justify-center flex-shrink-0 font-bold">
                        !
                    </div>
                    <div>
                        <span class="text-2xl font-extrabold block {{ empty($results['errors']) ? 'text-slate-500' : 'text-rose-800' }}">{{ count($results['errors']) }}</span>
                        <span class="text-xs font-semibold uppercase tracking-wider {{ empty($results['errors']) ? 'text-slate-400' : 'text-rose-600' }}">Errores de Validación Encontrados</span>
                    </div>
                </div>
            </div>

            {{-- Detalle de errores por fila --}}
            @if(!empty($results['errors']))
                <div class="space-y-3 pt-4">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Desglose de Errores por Fila</span>
                    <div class="max-h-80 overflow-y-auto border border-slate-200 rounded-lg divide-y divide-slate-150">
                        @foreach($results['errors'] as $error)
                            <div class="p-3 text-xs flex items-start space-x-3 hover:bg-slate-50 transition-colors">
                                <span class="px-2 py-0.5 rounded bg-rose-100 border border-rose-200 text-rose-700 font-bold flex-shrink-0">
                                    Fila {{ $error['row'] }}
                                </span>
                                @if(isset($error['sku']) && $error['sku'] !== 'N/A')
                                    <span class="font-mono text-slate-400 flex-shrink-0">[SKU: {{ $error['sku'] }}]</span>
                                @endif
                                <p class="text-slate-600 flex-1 leading-normal">{{ $error['message'] }}</p>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-[11px] text-slate-400">Corrige estos errores en tu plantilla original de Excel y vuelve a subir el archivo para cargar los productos restantes.</p>
                </div>
            @else
                <div class="p-3 text-xs bg-emerald-50/50 rounded-lg border border-emerald-100 text-emerald-700 flex items-center">
                    <svg class="h-4 w-4 text-emerald-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>¡El archivo completo se procesó de forma perfecta sin ningún error! Catálogo actualizado al 100%.</span>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
