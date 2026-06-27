@extends('layouts.admin')

@section('content')
<div class="max-w-4xl space-y-6" x-data="{ 
    activeTab: 'general',
    variations: {{ old('variations') ? json_encode(old('variations')) : ($product && $product->variations ? json_encode($product->variations) : '[]') }},
    newAttrName: '',
    newAttrValues: '',
    addVariation() {
        if (this.newAttrName.trim() === '' || this.newAttrValues.trim() === '') return;
        this.variations.push({
            name: this.newAttrName.trim(),
            options: this.newAttrValues.split(',').map(s => s.trim()).filter(s => s !== '')
        });
        this.newAttrName = '';
        this.newAttrValues = '';
    },
    removeVariation(index) {
        this.variations.splice(index, 1);
    }
}">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 font-sans">
                {{ $product ? 'Editar Producto' : 'Nuevo Producto' }}
            </h2>
            <p class="text-sm text-slate-500">
                {{ $product ? 'Modifica los campos del producto seleccionado y guarda los cambios.' : 'Registra un nuevo producto completando la información requerida.' }}
            </p>
        </div>
        <a href="{{ route('tenant.admin.products.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
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

    <form method="POST" action="{{ $product ? route('tenant.admin.products.update', $product->id) : route('tenant.admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if($product)
            @method('PUT')
        @endif

        {{-- Barra de Navegación de Pestañas (Tabs) --}}
        <div class="border-b border-slate-200">
            <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                <button type="button" @click="activeTab = 'general'" :class="activeTab === 'general' ? 'border-tenant-primary text-tenant-primary' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'" class="whitespace-nowrap py-3 px-1 border-b-2 font-semibold text-sm transition-all duration-200">
                    Información General
                </button>
                <button type="button" @click="activeTab = 'pricing'" :class="activeTab === 'pricing' ? 'border-tenant-primary text-tenant-primary' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'" class="whitespace-nowrap py-3 px-1 border-b-2 font-semibold text-sm transition-all duration-200">
                    Precios e Inventario
                </button>
                <button type="button" @click="activeTab = 'variations'" :class="activeTab === 'variations' ? 'border-tenant-primary text-tenant-primary' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'" class="whitespace-nowrap py-3 px-1 border-b-2 font-semibold text-sm transition-all duration-200">
                    Variaciones Técnicas
                </button>
                <button type="button" @click="activeTab = 'images'" :class="activeTab === 'images' ? 'border-tenant-primary text-tenant-primary' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'" class="whitespace-nowrap py-3 px-1 border-b-2 font-semibold text-sm transition-all duration-200">
                    Imágenes
                </button>
            </nav>
        </div>

        {{-- Contenedor de Formularios --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
            
            {{-- TABA 1: GENERAL --}}
            <div x-show="activeTab === 'general'" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-slate-700">Nombre del Producto *</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $product ? $product->name : '') }}"
                            class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-slate-700">Categoría *</label>
                        <select name="category_id" id="category_id" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                            <option value="">Selecciona categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product ? $product->category_id : '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-slate-700">Marca (Opcional)</label>
                        <select name="brand_id" id="brand_id"
                            class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                            <option value="">Ninguna</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product ? $product->brand_id : '') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="sku" class="block text-sm font-medium text-slate-700">SKU (Opcional)</label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku', $product ? $product->sku : '') }}" placeholder="Auto-generado si se deja vacío"
                            class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                    </div>

                    <div>
                        <label for="internal_code" class="block text-sm font-medium text-slate-700">Código de Ubicación Interno (Opcional)</label>
                        <input type="text" name="internal_code" id="internal_code" value="{{ old('internal_code', $product ? $product->internal_code : '') }}"
                            class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700">Descripción</label>
                    <textarea name="description" id="description" rows="4"
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">{{ old('description', $product ? $product->description : '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700">Estado de Publicación</label>
                        <select name="status" id="status" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                            <option value="active" {{ old('status', $product ? $product->status : 'active') === 'active' ? 'selected' : '' }}>Activo (Visible en tienda)</option>
                            <option value="draft" {{ old('status', $product ? $product->status : '') === 'draft' ? 'selected' : '' }}>Borrador</option>
                            <option value="inactive" {{ old('status', $product ? $product->status : '') === 'inactive' ? 'selected' : '' }}>Inactivo (Oculto)</option>
                        </select>
                    </div>

                    <div class="flex items-center pt-6">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product ? $product->is_featured : false) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-slate-300 text-tenant-primary focus:ring-tenant-primary">
                        <label for="is_featured" class="ml-2 block text-sm font-semibold text-slate-800">Marcar como Producto Destacado</label>
                    </div>
                </div>
            </div>

            {{-- TABA 2: PRICING & INVENTORY --}}
            <div x-show="activeTab === 'pricing'" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="price" class="block text-sm font-medium text-slate-700">Precio Normal *</label>
                        <input type="number" name="price" id="price" step="0.01" min="0" required value="{{ old('price', $product ? $product->price : '') }}"
                            class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                    </div>

                    <div>
                        <label for="sale_price" class="block text-sm font-medium text-slate-700">Precio Oferta (Opcional)</label>
                        <input type="number" name="sale_price" id="sale_price" step="0.01" min="0" value="{{ old('sale_price', $product ? $product->sale_price : '') }}"
                            class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                    </div>

                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-slate-700">Precio de Costo (Opcional)</label>
                        <input type="number" name="cost_price" id="cost_price" step="0.01" min="0" value="{{ old('cost_price', $product ? $product->cost_price : '') }}"
                            class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                        <p class="mt-1 text-[11px] text-slate-400">Usado para cálculo interno de utilidades.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                    {{-- Stock Físico Inicial --}}
                    @if(!$product)
                        <div>
                            <label for="stock" class="block text-sm font-medium text-slate-700">Inventario Inicial (Stock) *</label>
                            <input type="number" name="stock" id="stock" min="0" required value="{{ old('stock', '0') }}"
                                class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Inventario Actual (Lectura)</label>
                            <div class="mt-1 py-2.5 px-3 rounded-lg border border-slate-200 bg-slate-100 text-sm font-bold text-slate-800 flex items-center justify-between">
                                <span>{{ $product->inventory ? $product->inventory->quantity : 0 }} unidades</span>
                                <a href="{{ route('tenant.admin.inventory.adjust', $product->inventory ? $product->inventory->id : 0) }}" class="text-xs text-blue-600 hover:text-blue-900 font-semibold underline">Realizar Ajuste &rarr;</a>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-400">Por auditoría, el stock se ajusta mediante el Kárdex.</p>
                        </div>
                    @endif

                    <div>
                        <label for="min_stock" class="block text-sm font-medium text-slate-700">Stock Mínimo (Alerta)</label>
                        <input type="number" name="min_stock" id="min_stock" min="0" value="{{ old('min_stock', $product && $product->inventory ? $product->inventory->min_stock : '5') }}"
                            class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-slate-700">Ubicación Física (Pasillo/Estante)</label>
                        <input type="text" name="location" id="location" value="{{ old('location', $product && $product->inventory ? $product->inventory->location : '') }}" placeholder="Ej: Pasillo A - Estante 2"
                            class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                    </div>
                </div>
            </div>

            {{-- TABA 3: VARIATIONS --}}
            <div x-show="activeTab === 'variations'" class="space-y-6">
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <h4 class="text-sm font-bold text-slate-800">Generar Variaciones del Producto</h4>
                    <p class="text-xs text-slate-500 mt-1">Permite especificar detalles como talla, color o modelos de motores aplicables.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                        <div>
                            <input type="text" x-model="newAttrName" placeholder="Nombre (ej: Talla)"
                                class="block w-full rounded-lg border border-slate-300 bg-white py-2 px-3 text-xs focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
                        </div>
                        <div class="md:col-span-2 flex space-x-2">
                            <input type="text" x-model="newAttrValues" placeholder="Opciones separadas por coma (ej: S, M, L)"
                                class="block w-full min-w-0 flex-1 rounded-lg border border-slate-300 bg-white py-2 px-3 text-xs focus:ring-1 focus:ring-tenant-primary focus:border-tenant-primary">
                            <button type="button" @click="addVariation()"
                                class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-slate-850 hover:bg-slate-900 rounded-lg">
                                Añadir
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Listado de variaciones agregadas --}}
                <div class="space-y-3">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Variaciones Creadas</span>
                    
                    <template x-for="(v, index) in variations" :key="index">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-slate-200">
                            <div>
                                <span class="text-sm font-bold text-slate-850" x-text="v.name"></span>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <template x-for="(opt, oIdx) in v.options" :key="oIdx">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-xs font-medium text-slate-700" x-text="opt"></span>
                                    </template>
                                </div>
                            </div>
                            
                            {{-- Input oculto para enviar al backend estructurado --}}
                            <input type="hidden" :name="`variations[${index}][name]`" :value="v.name">
                            <template x-for="(opt, oIdx) in v.options" :key="'h-'+oIdx">
                                <input type="hidden" :name="`variations[${index}][options][${oIdx}]`" :value="opt">
                            </template>

                            <button type="button" @click="removeVariation(index)" class="text-xs text-rose-500 hover:text-rose-700 font-semibold">
                                Eliminar
                            </button>
                        </div>
                    </template>

                    <div x-show="variations.length === 0" class="text-center py-6 text-slate-400 text-xs italic">
                        Sin variaciones técnicas asignadas.
                    </div>
                </div>
            </div>

            {{-- TABA 4: IMAGES --}}
            <div x-show="activeTab === 'images'" class="space-y-6">
                {{-- Si es edición, listamos imágenes actuales --}}
                @if($product && $product->images && count($product->images) > 0)
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-3">Imágenes Actuales (Marca las que deseas conservar)</span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach($product->images as $img)
                                <div class="relative rounded-lg border border-slate-200 overflow-hidden bg-slate-50 aspect-square group">
                                    <img src="{{ asset('storage/' . $img) }}" alt="Foto" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 inset-x-0 bg-slate-900/70 p-2 flex items-center justify-between">
                                        <span class="text-[10px] text-white font-mono truncate">Conserar</span>
                                        <input type="checkbox" name="keep_images[]" value="{{ $img }}" checked
                                            class="h-3.5 w-3.5 rounded border-slate-300 text-tenant-primary focus:ring-tenant-primary">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Cargar nuevas fotos --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700">Subir Fotografías del Producto</label>
                    <input type="file" name="{{ $product ? 'new_images[]' : 'images[]' }}" multiple accept="image/*"
                        class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                    <p class="mt-1 text-xs text-slate-400">Puedes seleccionar múltiples imágenes. PNG, JPG, JPEG. Máx. 2MB por foto.</p>
                </div>
            </div>

        </div>

        {{-- Botones de envío --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('tenant.admin.products.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-tenant-primary bg-tenant-primary-hover shadow-md rounded-lg transition-all duration-200">
                {{ $product ? 'Guardar Cambios' : 'Crear Producto' }}
            </button>
        </div>
    </form>
</div>
@endsection
