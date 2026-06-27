<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository
    ) {}

    /**
     * Muestra la lista de clientes.
     */
    public function index()
    {
        $customers = $this->customerRepository->paginate(15);
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Muestra el formulario para crear un nuevo cliente.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Almacena un cliente nuevo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'nullable|email|max:100|unique:customers,email',
            'phone'   => 'required|string|max:20|unique:customers,phone',
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:100',
            'status'  => 'required|in:active,inactive',
        ]);

        $this->customerRepository->create($request->all());

        return redirect()->route('tenant.admin.customers.index')->with('success', 'Cliente creado con éxito.');
    }

    /**
     * Muestra el detalle de un cliente específico e historial de pedidos.
     */
    public function show($id)
    {
        $customer = $this->customerRepository->find($id);

        if (!$customer) {
            abort(404, 'Cliente no encontrado');
        }

        // Cargar los pedidos de este cliente de forma paginada/ordenada
        $orders = $customer->orders()->orderByDesc('created_at')->paginate(10);

        return view('admin.customers.show', compact('customer', 'orders'));
    }

    /**
     * Muestra el formulario de edición de un cliente.
     */
    public function edit($id)
    {
        $customer = $this->customerRepository->find($id);

        if (!$customer) {
            abort(404, 'Cliente no encontrado');
        }

        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Actualiza la información del cliente.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'nullable|email|max:100|unique:customers,email,' . $id,
            'phone'   => 'required|string|max:20|unique:customers,phone,' . $id,
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:100',
            'status'  => 'required|in:active,inactive',
        ]);

        $this->customerRepository->update($id, $request->all());

        return redirect()->route('tenant.admin.customers.index')->with('success', 'Cliente actualizado con éxito.');
    }

    /**
     * Elimina (soft delete) un cliente.
     */
    public function destroy($id)
    {
        $this->customerRepository->delete($id);
        return redirect()->route('tenant.admin.customers.index')->with('success', 'Cliente eliminado con éxito.');
    }
}
