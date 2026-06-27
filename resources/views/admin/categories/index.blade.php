@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 font-sans">Categorías de Productos</h2>
            <p class="text-sm text-slate-500">Organiza tu catálogo en niveles jerárquicos (categorías padres e hijas).</p>
        </div>
        <a href="{{ route('tenant.admin.categories.create') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow rounded-lg transition-all duration-200">
            Nueva Categoría
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nombre</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Slug</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Descripción</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th scope="col" class="relative px-6 py-3.5">
                        <span class="sr-only">Acciones</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($categories as $category)
                    {{-- Categoría Padre --}}
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="text-sm font-bold text-slate-800">{{ $category->name }}</span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-mono text-slate-500">
                            {{ $category->slug }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate">
                            {{ $category->description ?: 'Sin descripción' }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $category->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                {{ $category->status === 'active' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('tenant.admin.categories.edit', $category->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors">Editar</a>
                            <form method="POST" action="{{ route('tenant.admin.categories.destroy', $category->id) }}" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría? Las categorías hijas pasarán a no tener padre.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-900 transition-colors">Eliminar</button>
                            </form>
                        </td>
                    </tr>

                    {{-- Categorías Hijas --}}
                    @foreach($category->children as $child)
                        <tr class="hover:bg-slate-50 transition-colors bg-slate-50/50">
                            <td class="whitespace-nowrap px-6 py-3.5 pl-12 flex items-center">
                                <span class="text-slate-400 mr-2">↳</span>
                                <span class="text-sm text-slate-700 font-medium">{{ $child->name }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-3.5 text-sm font-mono text-slate-400">
                                {{ $child->slug }}
                            </td>
                            <td class="px-6 py-3.5 text-sm text-slate-400 max-w-xs truncate">
                                {{ $child->description ?: 'Sin descripción' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-3.5">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {{ $child->status === 'active' ? 'bg-emerald-50/60 text-emerald-600 border border-emerald-100' : 'bg-slate-100/60 text-slate-600 border border-slate-200' }}">
                                    {{ $child->status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-3.5 text-right text-sm font-medium space-x-2">
                                <a href="{{ route('tenant.admin.categories.edit', $child->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors">Editar</a>
                                <form method="POST" action="{{ route('tenant.admin.categories.destroy', $child->id) }}" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-900 transition-colors">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <h3 class="mt-4 text-sm font-medium text-slate-900">No hay categorías</h3>
                            <p class="mt-1 text-sm text-slate-500">Comienza creando una nueva categoría para organizar tus productos.</p>
                            <div class="mt-6">
                                <a href="{{ route('tenant.admin.categories.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow rounded-lg">
                                    Crear Categoría
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
