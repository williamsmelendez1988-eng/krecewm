<?php

namespace App\Http\Controllers\Admin;

use App\Core\Tenant\TenantManager;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    /**
     * Lista los miembros del equipo del tenant.
     */
    public function index()
    {
        $tenantId = TenantManager::getTenantId();
        $staffMembers = User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->paginate(20);

        return view('admin.staff.index', compact('staffMembers'));
    }

    /**
     * Formulario para crear un nuevo miembro del staff.
     */
    public function create()
    {
        return view('admin.staff.create');
    }

    /**
     * Almacena un nuevo miembro del staff.
     */
    public function store(Request $request)
    {
        $tenantId = TenantManager::getTenantId();

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email',
            'role'     => 'required|in:admin,staff',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role === 'admin' ? 'tenant_admin' : 'tenant_staff',
            'tenant_id' => $tenantId,
            'password'  => Hash::make($request->password),
            'status'    => 'active',
        ]);

        return redirect()->route('tenant.admin.staff.index')
            ->with('success', "Usuario {$request->name} creado exitosamente.");
    }

    /**
     * Muestra el formulario de edición de un miembro del staff.
     */
    public function edit(User $user)
    {
        $this->authorizeStaffMember($user);

        return view('admin.staff.edit', compact('user'));
    }

    /**
     * Actualiza los datos de un miembro del staff.
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeStaffMember($user);

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,staff',
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role === 'admin' ? 'tenant_admin' : 'tenant_staff',
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('tenant.admin.staff.index')
            ->with('success', "Usuario {$user->name} actualizado correctamente.");
    }

    /**
     * Activa o desactiva un miembro del staff.
     */
    public function toggleStatus(User $user)
    {
        $this->authorizeStaffMember($user);

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $estado = $newStatus === 'active' ? 'activado' : 'desactivado';
        return redirect()->route('tenant.admin.staff.index')
            ->with('success', "Usuario {$user->name} {$estado} correctamente.");
    }

    /**
     * Verifica que el usuario pertenece al tenant activo.
     */
    private function authorizeStaffMember(User $user): void
    {
        if ($user->tenant_id !== TenantManager::getTenantId()) {
            abort(403, 'No tienes permisos para editar este usuario.');
        }
    }
}
