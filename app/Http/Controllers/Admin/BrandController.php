<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\BrandRepositoryInterface;
use App\Core\Tenant\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    protected BrandRepositoryInterface $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * Listado de marcas.
     */
    public function index()
    {
        $brands = $this->brandRepository->all();
        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Guardar marca.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'logo' => 'nullable|image|max:1024', // máx 1MB
            'status' => 'required|in:active,inactive'
        ]);

        $tenantId = TenantManager::getTenantId();
        
        // Generar slug único
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;
        while (\App\Models\Brand::where('tenant_id', $tenantId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // Subir logo
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('uploads/brands', 'public');
        }

        $this->brandRepository->create([
            'tenant_id' => $tenantId,
            'name' => trim($request->name),
            'slug' => $slug,
            'logo' => $logoPath,
            'status' => $request->status
        ]);

        return redirect()->route('tenant.admin.brands.index')
            ->with('success', 'Marca creada correctamente.');
    }

    /**
     * Actualizar marca.
     */
    public function update(Request $request, int $id)
    {
        $brand = $this->brandRepository->find($id);
        if (!$brand) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'logo' => 'nullable|image|max:1024',
            'status' => 'required|in:active,inactive'
        ]);

        $tenantId = TenantManager::getTenantId();
        $slug = $brand->slug;

        // Generar slug nuevo si cambia nombre
        if (trim($request->name) !== $brand->name) {
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;
            while (\App\Models\Brand::where('tenant_id', $tenantId)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        $logoPath = $brand->logo;
        if ($request->hasFile('logo')) {
            // Eliminar anterior si existe
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $logoPath = $request->file('logo')->store('uploads/brands', 'public');
        }

        $this->brandRepository->update($id, [
            'name' => trim($request->name),
            'slug' => $slug,
            'logo' => $logoPath,
            'status' => $request->status
        ]);

        return redirect()->route('tenant.admin.brands.index')
            ->with('success', 'Marca actualizada correctamente.');
    }

    /**
     * Eliminar marca.
     */
    public function destroy(int $id)
    {
        $brand = $this->brandRepository->find($id);
        if (!$brand) {
            abort(404);
        }

        // Desasociar marca de los productos
        \App\Models\Product::where('brand_id', $id)->update(['brand_id' => null]);

        // Eliminar logo físico
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $this->brandRepository->delete($id);

        return redirect()->route('tenant.admin.brands.index')
            ->with('success', 'Marca eliminada correctamente.');
    }
}
