<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\BrandRepositoryInterface;
use App\Services\ProductService;
use App\Core\Tenant\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected ProductRepositoryInterface $productRepository;
    protected CategoryRepositoryInterface $categoryRepository;
    protected BrandRepositoryInterface $brandRepository;
    protected ProductService $productService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        BrandRepositoryInterface $brandRepository,
        ProductService $productService
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->brandRepository = $brandRepository;
        $this->productService = $productService;
    }

    /**
     * Listado de productos con filtros.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'brand_id', 'status', 'stock_status']);
        $products = $this->productRepository->searchAndFilterAdmin($filters);
        
        $categories = $this->categoryRepository->getActiveCategories();
        $brands = $this->brandRepository->getActiveBrands();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        $categories = $this->categoryRepository->getActiveCategories();
        $brands = $this->brandRepository->getActiveBrands();

        return view('admin.products.form', [
            'product' => null,
            'categories' => $categories,
            'brands' => $brands
        ]);
    }

    /**
     * Guardar producto.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'sku' => 'nullable|string|max:100',
            'internal_code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'cost_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,active,inactive',
            'is_featured' => 'nullable|boolean',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:150',
            'variations' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048' // máx 2MB por imagen
        ], [
            'sale_price.lt' => 'El precio de oferta debe ser menor al precio normal.'
        ]);

        $data = $request->except(['images']);
        $data['tenant_id'] = TenantManager::getTenantId();
        
        $images = $request->file('images') ?? [];
        $userId = Auth::id();

        $this->productService->createProduct($data, $images, $userId);

        return redirect()->route('tenant.admin.products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(int $id)
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            abort(404);
        }

        $categories = $this->categoryRepository->getActiveCategories();
        $brands = $this->brandRepository->getActiveBrands();

        return view('admin.products.form', compact('product', 'categories', 'brands'));
    }

    /**
     * Actualizar producto.
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'sku' => 'nullable|string|max:100',
            'internal_code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'cost_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,active,inactive',
            'is_featured' => 'nullable|boolean',
            'min_stock' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:150',
            'variations' => 'nullable|array',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|max:2048',
            'keep_images' => 'nullable|array'
        ], [
            'sale_price.lt' => 'El precio de oferta debe ser menor al precio normal.'
        ]);

        $data = $request->except(['new_images', 'keep_images']);
        $newImages = $request->file('new_images') ?? [];
        $keepImages = $request->keep_images ?? [];

        $updated = $this->productService->updateProduct($id, $data, $newImages, $keepImages);

        if ($updated) {
            return redirect()->route('tenant.admin.products.index')
                ->with('success', 'Producto actualizado correctamente.');
        }

        return redirect()->back()->withErrors(['error' => 'Ocurrió un error al actualizar el producto.']);
    }

    /**
     * Eliminar producto.
     */
    public function destroy(int $id)
    {
        $deleted = $this->productService->deleteProduct($id);

        if ($deleted) {
            return redirect()->route('tenant.admin.products.index')
                ->with('success', 'Producto eliminado correctamente.');
        }

        return redirect()->back()->withErrors(['error' => 'No se pudo eliminar el producto.']);
    }
}
