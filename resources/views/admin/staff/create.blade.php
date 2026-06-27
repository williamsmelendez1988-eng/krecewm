@extends('layouts.admin')

@section('content')
<div class="max-w-2xl space-y-6">
    <div>
        <a href="{{ route('tenant.admin.staff.index') }}" class="text-xs font-semibold text-slate-400 hover:text-tenant-primary transition-colors flex items-center gap-1 mb-3">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver al equipo
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ isset($user) ? 'Editar Miembro' : 'Agregar Miembro al Equipo' }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            {{ isset($user) ? 'Actualiza los datos del usuario.' : 'Crea un nuevo acceso al panel de administración.' }}
        </p>
    </div>

    @if($errors->any())
        <div class="rounded-lg bg-rose-50 border border-rose-200 p-4 text-rose-800 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
        action="{{ isset($user) ? route('tenant.admin.staff.update', $user) : route('tenant.admin.staff.store') }}"
        class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label for="name" class="block text-sm font-semibold text-slate-700">Nombre Completo</label>
                <input type="text" name="name" id="name" required
                    value="{{ old('name', $user->name ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">Correo Electrónico</label>
                <input type="email" name="email" id="email" required
                    value="{{ old('email', $user->email ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-slate-700">Rol</label>
                <select name="role" id="role" required
                    class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all">
                    <option value="staff" {{ in_array(old('role', $user->role ?? ''), ['staff', 'tenant_staff']) ? 'selected' : '' }}>Staff (solo lectura/operación)</option>
                    <option value="admin" {{ in_array(old('role', $user->role ?? ''), ['admin', 'tenant_admin']) ? 'selected' : '' }}>Admin (acceso completo)</option>
                </select>
                <p class="mt-1 text-xs text-slate-400">Los Admin pueden cambiar configuraciones. El Staff gestiona pedidos e inventario.</p>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700">
                    Contraseña {{ isset($user) ? '(dejar en blanco para no cambiar)' : '*' }}
                </label>
                <input type="password" name="password" id="password"
                    {{ isset($user) ? '' : 'required' }}
                    class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm focus:ring-2 focus:ring-tenant-primary focus:border-tenant-primary transition-all"
                    placeholder="{{ isset($user) ? '••••••••' : 'Mínimo 8 caracteres' }}">
                <p class="mt-1 text-xs text-slate-400">Mínimo 8 caracteres, letras mayúsculas, minúsculas y números.</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('tenant.admin.staff.index') }}"
                class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-2.5 text-sm font-bold text-white bg-tenant-primary bg-tenant-primary-hover rounded-lg shadow-md transition-all">
                {{ isset($user) ? 'Guardar Cambios' : 'Crear Usuario' }}
            </button>
        </div>
    </form>
</div>
@endsection
