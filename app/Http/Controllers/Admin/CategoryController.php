<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Core\Tenant\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Listado de categorías.
     */
    public function index()
    {
        $categories = $this->categoryRepository->getCategoryTree();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        $parentCategories = $this->categoryRepository->getParentCategories();
        return view('admin.categories.form', [
            'category' => null,
            'parentCategories' => $parentCategories
        ]);
    }

    /**
     * Guardar nueva categoría.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $tenantId = TenantManager::getTenantId();

        // Generar slug único del tenant activo
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;

        while (\App\Models\Category::where('tenant_id', $tenantId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $this->categoryRepository->create([
            'tenant_id' => $tenantId,
            'parent_id' => $request->parent_id,
            'name' => trim($request->name),
            'slug' => $slug,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return redirect()->route('tenant.admin.categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(int $id)
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            abort(404);
        }

        // Obtener categorías padres elegibles (excluyendo la categoría actual)
        $parentCategories = $this->categoryRepository->getParentCategories()
            ->where('id', '!=', $id);

        return view('admin.categories.form', compact('category', 'parentCategories'));
    }

    /**
     * Actualizar categoría.
     */
    public function update(Request $request, int $id)
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|integer|exists:categories,id|different:id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $tenantId = TenantManager::getTenantId();
        $slug = $category->slug;

        // Si cambia el nombre, regeneramos slug único
        if (trim($request->name) !== $category->name) {
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;

            while (\App\Models\Category::where('tenant_id', $tenantId)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        $this->categoryRepository->update($id, [
            'parent_id' => $request->parent_id,
            'name' => trim($request->name),
            'slug' => $slug,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return redirect()->route('tenant.admin.categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Eliminar categoría.
     */
    public function destroy(int $id)
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            abort(404);
        }

        // Poner parent_id en null para categorías hijas si existieran
        \App\Models\Category::where('parent_id', $id)->update(['parent_id' => null]);

        $this->categoryRepository->delete($id);

        return redirect()->route('tenant.admin.categories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
