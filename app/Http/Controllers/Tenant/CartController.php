<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Muestra el contenido del carrito.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['qty']);

        return view('tenant.cart.index', compact('cart', 'total'));
    }

    /**
     * Agrega un producto al carrito.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $product = Product::with('inventory')->findOrFail($request->product_id);
        
        // Verificar stock si aplica
        $stockAvailable = $product->inventory ? $product->inventory->quantity : 0;
        
        $cart = session()->get('cart', []);
        
        $currentQty = isset($cart[$product->id]) ? $cart[$product->id]['qty'] : 0;
        $newQty = $currentQty + $request->qty;

        if ($product->inventory && !$product->inventory->allow_negative && $newQty > $stockAvailable) {
            return redirect()->back()->with('error', "No hay suficiente stock. Disponible: {$stockAvailable}. En tu carrito: {$currentQty}.");
        }

        $cart[$product->id] = [
            'product_id' => $product->id,
            'name'       => $product->name,
            'sku'        => $product->sku,
            'price'      => $product->price,
            'qty'        => $newQty,
            'image'      => $product->image_url ?? null,
        ];

        session()->put('cart', $cart);

        return redirect()->route('tenant.cart.index')->with('success', 'Producto agregado al carrito.');
    }

    /**
     * Actualiza la cantidad de un producto.
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $product = Product::with('inventory')->findOrFail($request->product_id);
        $stockAvailable = $product->inventory ? $product->inventory->quantity : 0;

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            if ($product->inventory && !$product->inventory->allow_negative && $request->qty > $stockAvailable) {
                return redirect()->back()->with('error', "No hay suficiente stock disponible. Stock actual: {$stockAvailable}.");
            }

            $cart[$request->product_id]['qty'] = $request->qty;
            session()->put('cart', $cart);
        }

        return redirect()->route('tenant.cart.index')->with('success', 'Carrito actualizado.');
    }

    /**
     * Quita un producto del carrito.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('tenant.cart.index')->with('success', 'Producto removido del carrito.');
    }

    /**
     * Vacía el carrito.
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('catalog.index')->with('success', 'El carrito ha sido vaciado.');
    }
}
