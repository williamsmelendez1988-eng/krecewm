@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 font-Outfit">Clientes</h2>
            <p class="text-sm text-slate-500">Administra los clientes registrados y consulta su historial de compras.</p>
        </div>
        <a href="{{ route('tenant.admin.customers.create') }}" class="inline-flex items-center justify-center rounded-lg bg-tenant-primary bg-tenant-primary-hover px-4 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-200">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Cliente
        </a>
    </div>

    {{-- Tabla de Clientes --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-500">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Nombre</th>
                        <th class="px-6 py-4">Teléfono</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Ciudad</th>
                        <th class="px-6 py-4 text-center">Pedidos</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-55/30 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('tenant.admin.customers.show', $customer->id) }}" class="font-semibold text-slate-900 hover:text-tenant-primary transition-colors block">
                                    {{ $customer->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $customer->phone }}</td>
                            <td class="px-6 py-4 text-xs font-mono">{{ $customer->email ?: '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $customer->city ?: '-' }}</td>
                            <td class="px-6 py-4 text-center font-bold text-slate-800">{{ $customer->orders_count }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $customer->status === 'active' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-slate-50 text-slate-600 ring-slate-500/20' }}">
                                    {{ $customer->status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-xs font-semibold space-x-2">
                                <a href="{{ route('tenant.admin.customers.show', $customer->id) }}" class="text-tenant-primary hover:underline">Ver ficha</a>
                                <a href="{{ route('tenant.admin.customers.edit', $customer->id) }}" class="text-blue-600 hover:underline">Editar</a>
                                <form action="{{ route('tenant.admin.customers.destroy', $customer->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro de eliminar este cliente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline bg-transparent border-none cursor-pointer">Borrar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium">No se han registrado clientes aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
