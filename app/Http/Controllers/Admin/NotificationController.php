<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Listado de todas las notificaciones del usuario autenticado.
     */
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        auth()->user()->unreadNotifications->markAsRead();

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Marca una notificación específica como leída y redirige a su acción.
     */
    public function markRead(string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        $url = $notification->data['action_url'] ?? route('tenant.admin.dashboard');

        return redirect($url);
    }

    /**
     * Marca todas las notificaciones del usuario como leídas.
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }
}
