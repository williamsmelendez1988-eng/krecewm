<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\OrderService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderService $orderService,
        private readonly WhatsAppService $whatsAppService
    ) {}

    /**
     * Muestra el listado de pedidos.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'search']);
        $orders = $this->orderRepository->paginate(15, $filters);

        return view('admin.orders.index', compact('orders', 'filters'));
    }

    /**
     * Muestra el detalle de un pedido.
     */
    public function show($id)
    {
        $order = $this->orderRepository->findWithItems($id);

        if (!$order) {
            abort(404, 'Pedido no encontrado');
        }

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Actualiza el estado del pedido e impacta el inventario si cambia.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            'notes'  => 'nullable|string|max:500',
        ]);

        $order = $this->orderRepository->find($id);

        if (!$order) {
            abort(404, 'Pedido no encontrado');
        }

        $this->orderService->changeStatus($order, $request->status, $request->notes);

        return redirect()->route('tenant.admin.orders.show', $id)
            ->with('success', 'El estado del pedido ha sido actualizado.');
    }

    /**
     * Redirige al administrador a WhatsApp para comunicarse con el cliente.
     */
    public function sendWhatsApp($id)
    {
        $order = $this->orderRepository->findWithItems($id);

        if (!$order) {
            abort(404, 'Pedido no encontrado');
        }

        $whatsappUrl = $this->whatsAppService->generateOrderLink($order);

        return redirect()->away($whatsappUrl);
    }

    /**
     * Actualiza datos generales de un pedido (actualmente solo notas).
     */
    public function update(Request $request, $id)
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            abort(404, 'Pedido no encontrado');
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->update(['notes' => $request->notes]);

        return redirect()->route('tenant.admin.orders.show', $id)
            ->with('success', 'Pedido actualizado correctamente.');
    }

    /**
     * Elimina (soft-delete) un pedido.
     */
    public function destroy($id)
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            abort(404, 'Pedido no encontrado');
        }

        $order->delete();

        return redirect()->route('tenant.admin.orders.index')
            ->with('success', 'Pedido eliminado correctamente.');
    }
}
