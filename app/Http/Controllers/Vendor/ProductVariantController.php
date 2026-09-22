<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\ProductVariantRepository;
use App\Services\ProductInventoryService;
use Illuminate\Support\Facades\Auth;
use Exception;

class ProductVariantController extends Controller
{
    protected $variantRepository;
    protected $inventoryService;

    public function __construct(
        ProductVariantRepository $variantRepository,
        ProductInventoryService $inventoryService
    ) {
        $this->variantRepository = $variantRepository;
        $this->inventoryService = $inventoryService;
    }

    public function store(CreateVariantRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        try {
            $data = $request->validated();
            $data['sku'] = $data['sku'] ?? $this->variantRepository->generateUniqueSku(
                $product->id,
                $data['variant_value']
            );

            $variant = ProductVariant::create($data + ['product_id' => $product->id]);

            // Create inventory record
            $this->inventoryService->createInventory(
                $product->id,
                $variant->id,
                $data['stock'] ?? 0
            );

            return redirect()->back()
                ->with('success', 'Variant added successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(CreateVariantRequest $request, Product $product, ProductVariant $variant)
    {
        $this->authorize('update', $product);

        try {
            $variant->update($request->validated());

            return redirect()->back()
                ->with('success', 'Variant updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        $this->authorize('update', $product);

        try {
            $variant->delete();

            return redirect()->back()
                ->with('success', 'Variant deleted successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}