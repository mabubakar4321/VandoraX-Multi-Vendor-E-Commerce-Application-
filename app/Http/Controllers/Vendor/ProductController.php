<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Repositories\ProductVariantRepository;
use App\Repositories\ProductRepository;
use App\Services\ProductImageService;
use App\Services\ProductInventoryService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Auth;
use Exception;

class ProductController extends Controller
{
    protected $productRepository;
    protected $productImageService;
    protected $productInventoryService;
    protected $productService;
    protected $variantRepository;

    public function __construct(
        ProductRepository $productRepository,
        ProductImageService $productImageService,
        ProductService $productService,
        ProductVariantRepository $variantRepository,
        ProductInventoryService $productInventoryService
    )
    {
        $this->productRepository = $productRepository;
        $this->productImageService = $productImageService;
        $this->productInventoryService = $productInventoryService;
        $this->productService = $productService;
        $this->variantRepository = $variantRepository;
    }

    public function index()
    {
        $vendor = Auth::user()->vendor;
        $products = $this->productRepository->getVendorProducts($vendor->id);
        $stats = $this->productRepository->getProductStats($vendor->id);
        $categories = Category::main()->active()->get();

        return view('vendor.products.index', compact('products', 'stats', 'categories'));
    }

    public function create()
    {
        $categories = Category::main()->active()->get();
        return view('vendor.products.create', compact('categories'));
    }

    public function show(Product $product)
    {
        $this->authorize('update', $product);
        $product->load([
            'category.parent',
            'subcategory',
            'images',
            'variants.inventory',
            'specifications',
            'inventory',
        ]);

        return view('vendor.products.show', compact('product'));
    }

    public function store(CreateProductRequest $request)
    {
        try {
            $vendor = Auth::user()->vendor;
            $data = $request->validated();
            $images = $request->file('images', []);
            $variants = $data['variants'] ?? [];
            $specifications = $data['specifications'] ?? [];
            unset($data['images']);
            unset($data['variants']);
            unset($data['specifications']);

            $details = [
                'dimensions' => $data['dimensions'] ?? null,
                'weight' => $data['weight'] ?? null,
                'material' => $data['material'] ?? null,
                'brand' => $data['brand'] ?? null,
                'color_options' => $data['color_options'] ?? null,
            ];
            foreach (array_keys($details) as $key) {
                unset($data[$key]);
            }

            $product = $this->productService->createProduct($data, $vendor->id);

            foreach ($images as $image) {
                $this->productImageService->uploadImage($image, $product->id);
            }

            foreach ($variants as $variantData) {
                $stock = $variantData['stock'];
                $variantData['sku'] = $variantData['sku'] ?? $this->variantRepository->generateUniqueSku(
                    $product->id,
                    $variantData['variant_value']
                );

                $variant = ProductVariant::create($variantData + ['product_id' => $product->id]);
                $this->productInventoryService->createInventory($product->id, $variant->id, $stock);
            }

            foreach ($details as $key => $value) {
                if (filled($value)) {
                    ProductSpecification::create([
                        'product_id' => $product->id,
                        'spec_key' => $key === 'color_options' ? 'Color options' : ucfirst($key),
                        'spec_value' => $value,
                    ]);
                }
            }

            foreach ($specifications as $specification) {
                ProductSpecification::create([
                    'product_id' => $product->id,
                    'spec_key' => $specification['key'],
                    'spec_value' => $specification['value'],
                ]);
            }

            return redirect()->route('vendor.products.index')
                ->with('success', 'Product created successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        
        $categories = Category::main()->active()->get();
        $subcategories = $product->category_id 
            ? Category::where('parent_id', $product->category_id)->get()
            : collect();

        return view('vendor.products.edit', compact('product', 'categories', 'subcategories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        try {
            $this->productService->updateProduct($product->id, $request->validated());

            return redirect()->route('vendor.products.index')
                ->with('success', 'Product updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        try {
            $this->productService->deleteProduct($product->id);

            return redirect()->route('vendor.products.index')
                ->with('success', 'Product deleted successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function publish(Product $product)
    {
        $this->authorize('update', $product);

        try {
            $this->productService->publishProduct($product->id);

            return redirect()->back()
                ->with('success', 'Product published successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function unpublish(Product $product)
    {
        $this->authorize('update', $product);

        try {
            $this->productService->unpublishProduct($product->id);

            return redirect()->back()
                ->with('success', 'Product unpublished successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}