@extends('layouts.admin')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 font-sans">
                {{ $category ? 'Editar Categoría' : 'Nueva Categoría' }}
            </h2>
            <p class="text-sm text-slate-500">
                {{ $category ? 'Modifica los datos de la categoría seleccionada.' : 'Registra una nueva categoría en el sistema.' }}
            </p>
        </div>
        <a href="{{ route('tenant.admin.categories.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            &larr; Volver
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-lg bg-rose-50 border border-rose-200 p-4 text-rose-800 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $category ? route('tenant.admin.categories.update', $category->id) : route('tenant.admin.categories.store') }}" class="space-y-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        @csrf
        @if($category)
            @method('PUT')
        @endif

        <div class="space-y-4">
            {{-- Nombre --}}
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Nombre de la Categoría</label>
                <input type="text" name="name" id="name" required value="{{ old('name', $category ? $category->name : '') }}"
                    class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
            </div>

            {{-- Categoría Padre --}}
            <div>
                <label for="parent_id" class="block text-sm font-medium text-slate-700">Categoría Padre (Opcional)</label>
                <select name="parent_id" id="parent_id"
                    class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                    <option value="">Ninguna (Es una categoría principal)</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $category ? $category->parent_id : '') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-400">Si deseas agrupar esta categoría dentro de otra, selecciónala aquí.</p>
            </div>

            {{-- Descripción --}}
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700">Descripción (Opcional)</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">{{ old('description', $category ? $category->description : '') }}</textarea>
            </div>

            {{-- Estado --}}
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700">Estado de Visibilidad</label>
                <select name="status" id="status" required
                    class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                    <option value="active" {{ old('status', $category ? $category->status : 'active') === 'active' ? 'selected' : '' }}>Activo (Visible en catálogo)</option>
                    <option value="inactive" {{ old('status', $category ? $category->status : '') === 'inactive' ? 'selected' : '' }}>Inactivo (Oculto)</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
            <a href="{{ route('tenant.admin.categories.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow rounded-lg transition-all">
                {{ $category ? 'Guardar Cambios' : 'Crear Categoría' }}
            </button>
        </div>
    </form>
</div>
@endsection
