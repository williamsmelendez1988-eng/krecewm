@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">🔔 Notificaciones</h2>
            <p class="text-sm text-slate-500 mt-0.5">Alertas de pedidos nuevos y stock crítico.</p>
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
        <form method="POST" action="{{ route('tenant.admin.notifications.markAllRead') }}">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                ✓ Marcar todas como leídas
            </button>
        </form>
        @endif
    </div>

    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm overflow-hidden">
        @forelse($notifications as $notif)
            @php
                $data    = $notif->data;
                $isRead  = !is_null($notif->read_at);
                $bgClass = $isRead ? '' : 'bg-indigo-50/40 border-l-4 border-l-indigo-500';
            @endphp
            <div class="flex items-start gap-4 px-6 py-4 border-b border-slate-100 last:border-none {{ $bgClass }} hover:bg-slate-50/80 transition-colors">
                {{-- Ícono --}}
                <div class="mt-0.5 text-2xl flex-shrink-0">{{ $data['icon'] ?? '🔔' }}</div>

                {{-- Contenido --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold {{ $isRead ? 'text-slate-600' : 'text-slate-900' }}">
                            {{ $data['title'] ?? 'Notificación' }}
                        </p>
                        @if(!$isRead)
                            <span class="h-2 w-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                        @endif
                    </div>
                    <p class="text-sm {{ $isRead ? 'text-slate-400' : 'text-slate-600' }} mt-0.5">
                        {{ $data['message'] ?? '' }}
                    </p>
                    <p class="text-xs text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                </div>

                {{-- Acción --}}
                @if(isset($data['action_url']))
                <a href="{{ route('tenant.admin.notifications.markRead', $notif->id) }}"
                   class="flex-shrink-0 inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold
                          {{ $isRead ? 'bg-slate-100 text-slate-600 hover:bg-slate-200' : 'bg-indigo-600 text-white hover:bg-indigo-700' }} transition-colors">
                    {{ $data['action_label'] ?? 'Ver' }}
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endif
            </div>
        @empty
            <div class="py-16 text-center">
                <div class="text-5xl mb-4">🔕</div>
                <p class="text-base font-semibold text-slate-600">Sin notificaciones</p>
                <p class="text-sm text-slate-400 mt-1">Aquí aparecerán las alertas de pedidos y stock cuando ocurran.</p>
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if($notifications->hasPages())
    <div class="flex justify-center">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
