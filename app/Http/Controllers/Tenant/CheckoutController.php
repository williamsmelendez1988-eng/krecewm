<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Services\OrderService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly WhatsAppService $whatsAppService
    ) {}

    /**
     * Muestra la vista de checkout.
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('catalog.index')->with('error', 'Tu carrito está vacío. Agrega algunos productos antes de continuar.');
        }

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['qty']);

        $tenant       = \App\Core\Tenant\TenantManager::getTenant();
        $exchangeRate = (float) $tenant->getSetting('exchange_rate_usd_bs', 0);

        return view('tenant.checkout.index', compact('cart', 'total', 'tenant', 'exchangeRate'));
    }

    /**
     * Procesa la orden de compra.
     */
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('catalog.index')->with('error', 'Tu carrito está vacío.');
        }

        $tenant       = \App\Core\Tenant\TenantManager::getTenant();
        $exchangeRate = (float) $tenant->getSetting('exchange_rate_usd_bs', 0);

        $request->validate([
            'name'             => 'required|string|max:100',
            'phone'            => 'required|string|max:20',
            'email'            => 'nullable|email|max:100',
            'address'          => 'required|string|max:255',
            'city'             => 'required|string|max:100',
            'payment_method'   => 'required|in:whatsapp,transfer,pago_movil',
            'currency'         => 'nullable|in:USD,VES',
            'notes'            => 'nullable|string|max:500',
        ]);

        $currency = $request->input('currency', 'USD');
        if ($exchangeRate <= 0) {
            $currency = 'USD';
        }

        $shippingData = $request->only([
            'name', 'phone', 'email', 'address', 'city', 'notes'
        ]);

        // Crear la orden usando el OrderService
        $order = $this->orderService->createFromCart(
            $cart, 
            $shippingData, 
            $request->payment_method,
            $currency,
            $exchangeRate > 0 ? $exchangeRate : 1.0000
        );

        // ── Notificar a todos los admins de la tienda sobre el nuevo pedido ──
        try {
            $adminUsers = User::whereIn('role', ['tenant_admin', 'tenant_staff'])
                ->where('status', 'active')
                ->get();
            Notification::send($adminUsers, new NewOrderNotification($order));
        } catch (\Throwable $e) {
            // Si falla la notificación, no interrumpimos el flujo de compra
            logger()->warning('NewOrderNotification failed: ' . $e->getMessage());
        }

        // Vaciar el carrito
        session()->forget('cart');

        return redirect()->route('tenant.checkout.success', $order->id)->with('success', '¡Pedido recibido con éxito!');
    }

    /**
     * Vista de confirmación final.
     */
    public function success(Order $order)
    {
        // Cargar relaciones
        $order->load(['items', 'customer']);

        // Generar link de WhatsApp
        $whatsappUrl = $this->whatsAppService->generateOrderLink($order);

        return view('tenant.checkout.success', compact('order', 'whatsappUrl'));
    }
}
