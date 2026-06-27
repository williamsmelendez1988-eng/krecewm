@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Equipo de Trabajo</h2>
            <p class="text-sm text-slate-500 mt-1">Administra los usuarios con acceso al panel de tu tienda.</p>
        </div>
        <a href="{{ route('tenant.admin.staff.create') }}"
            class="inline-flex items-center gap-2 rounded-lg bg-tenant-primary bg-tenant-primary-hover px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Agregar Miembro
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Desde</th>
                    <th class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($staffMembers as $member)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-semibold text-slate-800">{{ $member->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $member->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($member->role === 'admin' || $member->role === 'owner' || $member->role === 'tenant_admin')
                                <span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-bold text-violet-700">Admin</span>
                            @elseif($member->isSuperAdmin())
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-700">Super Admin</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">Staff</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(($member->status ?? 'active') === 'active')
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700">Activo</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-600">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400">
                            {{ $member->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('tenant.admin.staff.edit', $member) }}"
                                    class="text-xs font-semibold text-slate-600 hover:text-tenant-primary transition-colors px-2 py-1 rounded-md hover:bg-slate-100">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('tenant.admin.staff.toggle', $member) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="text-xs font-semibold px-2 py-1 rounded-md transition-colors
                                        {{ (($member->status ?? 'active') === 'active') ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50' }}">
                                        {{ (($member->status ?? 'active') === 'active') ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-sm text-slate-400">
                            No hay miembros del equipo registrados aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($staffMembers->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $staffMembers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
