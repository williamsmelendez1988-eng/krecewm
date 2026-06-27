@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-xl">
    <div class="flex items-center gap-4">
        <a href="{{ route('tenant.admin.customers.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Editar Cliente</h2>
            <p class="text-sm text-slate-500">Modifica los detalles del cliente {{ $customer->name }}.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
        <form action="{{ route('tenant.admin.customers.update', $customer->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nombre Completo *</label>
                <input type="text" id="name" name="name" required value="{{ old('name', $customer->name) }}" placeholder="Ej. Juan Pérez"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                @error('name')
                    <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Teléfono Móvil *</label>
                <input type="text" id="phone" name="phone" required value="{{ old('phone', $customer->phone) }}" placeholder="Ej. 3001234567"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                @error('phone')
                    <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Correo Electrónico (Opcional)</label>
                <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" placeholder="juan@correo.com"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                @error('email')
                    <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Dirección de Envío (Opcional)</label>
                <input type="text" id="address" name="address" value="{{ old('address', $customer->address) }}" placeholder="Calle 123 #45-67, Apto 201"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                @error('address')
                    <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Ciudad (Opcional)</label>
                <input type="text" id="city" name="city" value="{{ old('city', $customer->city) }}" placeholder="Medellín"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                @error('city')
                    <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Estado *</label>
                <select id="status" name="status" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-tenant-primary text-sm text-slate-800 bg-slate-50 focus:bg-white transition-all duration-200">
                    <option value="active" {{ old('status', $customer->status) === 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ old('status', $customer->status) === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('status')
                    <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('tenant.admin.customers.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-tenant-primary bg-tenant-primary-hover px-5 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-200">
                    Actualizar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
