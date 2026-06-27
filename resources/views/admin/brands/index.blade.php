@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
    editing: false, 
    brandId: '', 
    brandName: '', 
    brandStatus: 'active',
    actionRoute: '{{ route('tenant.admin.brands.store') }}',
    startEdit(brand) {
        this.editing = true;
        this.brandId = brand.id;
        this.brandName = brand.name;
        this.brandStatus = brand.status;
        this.actionRoute = `/admin/brands/${brand.id}`;
    },
    cancelEdit() {
        this.editing = false;
        this.brandId = '';
        this.brandName = '';
        this.brandStatus = 'active';
        this.actionRoute = '{{ route('tenant.admin.brands.store') }}';
    }
}">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900 font-sans">Marcas de Productos</h2>
        <p class="text-sm text-slate-500">Agrega y administra fabricantes o marcas para enriquecer la búsqueda del catálogo.</p>
    </div>

    @if($errors->any())
        <div class="rounded-lg bg-rose-50 border border-rose-200 p-4 text-rose-800 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Listado de Marcas (Izquierda) --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden h-fit">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Logo</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Slug</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="relative px-6 py-3.5">
                            <span class="sr-only">Acciones</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($brands as $brand)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($brand->logo)
                                    <div class="h-10 w-20 rounded border border-slate-100 bg-slate-50 flex items-center justify-center overflow-hidden">
                                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="Logo" class="max-h-8 max-w-full object-contain">
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 font-mono italic">Sin Logo</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="text-sm font-bold text-slate-800">{{ $brand->name }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-mono text-slate-500">
                                {{ $brand->slug }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $brand->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                    {{ $brand->status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium space-x-2">
                                <button type="button" @click="startEdit({{ json_encode($brand) }})" class="text-blue-600 hover:text-blue-900 transition-colors">Editar</button>
                                <form method="POST" action="{{ route('tenant.admin.brands.destroy', $brand->id) }}" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar esta marca? Los productos asociados quedarán como sin marca.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-900 transition-colors">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h10M7 12h10m-8 5h8" />
                                </svg>
                                <h3 class="mt-4 text-sm font-medium text-slate-900">No hay marcas</h3>
                                <p class="mt-1 text-sm text-slate-500">Comienza agregando fabricantes en el panel lateral derecho.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Formulario Dinámico (Derecha) --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 h-fit">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3" x-text="editing ? 'Editar Marca' : 'Nueva Marca'"></h3>
            
            <form method="POST" :action="actionRoute" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <template x-if="editing">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                {{-- Nombre --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Nombre de la Marca</label>
                    <input type="text" name="name" id="name" required x-model="brandName"
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                </div>

                {{-- Logo --}}
                <div>
                    <label for="logo" class="block text-sm font-medium text-slate-700">Logotipo (Imagen)</label>
                    <input type="file" name="logo" id="logo" accept="image/*"
                        class="mt-1 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                    <p class="mt-1 text-[11px] text-slate-400">PNG, JPG, JPEG. Máx. 1MB.</p>
                </div>

                {{-- Estado --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700">Estado</label>
                    <select name="status" id="status" required x-model="brandStatus"
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                </div>

                {{-- Botones --}}
                <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100">
                    <button type="button" x-show="editing" @click="cancelEdit"
                        class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow rounded-lg transition-all"
                        x-text="editing ? 'Guardar Cambios' : 'Registrar Marca'">
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
