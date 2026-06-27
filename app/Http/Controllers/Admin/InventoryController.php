<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Services\InventoryService;
use App\Core\Tenant\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Listado de existencias actuales.
     */
    public function index(Request $request)
    {
        $query = Inventory::with(['product.category', 'product.brand']);

        // Buscador
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('internal_code', 'like', "%{$search}%");
            });
        }

        // Filtro por ubicación
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        // Filtros rápidos de stock
        if ($request->filled('stock_status')) {
            $status = $request->stock_status;
            if ($status === 'out') {
                $query->where('quantity', '<=', 0);
            } elseif ($status === 'low') {
                $query->whereRaw('quantity <= min_stock AND quantity > 0');
            } elseif ($status === 'normal') {
                $query->whereRaw('quantity > min_stock');
            }
        }

        $inventories = $query->orderBy('quantity', 'asc')->paginate(15);

        return view('admin.inventory.index', compact('inventories'));
    }

    /**
     * Mostrar formulario para ajustar inventario.
     */
    public function showAdjustForm(int $id)
    {
        $inventory = Inventory::with('product')->find($id);
        if (!$inventory) {
            abort(404);
        }

        return view('admin.inventory.adjust', compact('inventory'));
    }

    /**
     * Procesar ajuste de inventario.
     */
    public function adjust(Request $request, int $id)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|in:purchase,sale,adjustment,damage,return',
            'description' => 'nullable|string|max:255'
        ]);

        try {
            $userId = Auth::id();
            
            $this->inventoryService->registerMovement(
                inventoryId: $id,
                type: $request->type,
                quantity: (int) $request->quantity,
                reason: $request->reason,
                description: trim($request->description),
                userId: $userId
            );

            return redirect()->route('tenant.admin.inventory.index')
                ->with('success', 'Ajuste de inventario registrado correctamente.');

        } catch (\Exception $ex) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $ex->getMessage()]);
        }
    }

    /**
     * Historial de movimientos (Kárdex).
     */
    public function movements(Request $request)
    {
        $query = InventoryMovement::with(['inventory.product', 'user']);

        // Filtro de búsqueda por producto/sku
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('inventory.product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtro por motivo
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.inventory.movements', compact('movements'));
    }
}
