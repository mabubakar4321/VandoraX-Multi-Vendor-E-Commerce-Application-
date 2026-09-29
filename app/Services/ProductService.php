<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\ProductInventoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductService
{
    protected $productRepository;
    protected $inventoryService;

    public function __construct(ProductRepository $productRepository, ProductInventoryService $inventoryService)
    {
        $this->productRepository = $productRepository;
        $this->inventoryService = $inventoryService;
    }

    public function createProduct($data, $vendorId)
    {
        return DB::transaction(function () use ($data, $vendorId) {
            $data['slug'] = Product::generateSlug($data['name']);
            $data['vendor_id'] = $vendorId;

            return $this->productRepository->create($data);
        });
    }

    public function updateProduct($productId, $data)
    {
        return DB::transaction(function () use ($productId, $data) {
            $product = $this->productRepository->findOrFail($productId);

            // Extract stock before updating product (stock lives in product_inventory, not products table)
            $stock = null;
            if (array_key_exists('stock', $data)) {
                $stock = (int) $data['stock'];
                unset($data['stock']);
            }

            if (isset($data['name']) && $data['name'] !== $product->name) {
                $data['slug'] = Product::generateSlug($data['name']);
            }

            $this->productRepository->update($productId, $data);

            // Update inventory stock if provided
            if ($stock !== null) {
                $inventory = $product->inventory()->first();

                if ($inventory) {
                    // Update existing inventory record
                    $this->inventoryService->updateStock(
                        $productId,
                        $inventory->variant_id,
                        $stock,
                        'Stock updated by vendor',
                        Auth::id()
                    );
                } else {
                    // Create inventory record if none exists
                    $this->inventoryService->createInventory($productId, null, $stock);
                }
            }

            return $product->fresh();
        });
    }

    public function deleteProduct($productId)
    {
        return DB::transaction(function () use ($productId) {
            $product = $this->productRepository->findOrFail($productId);
            return $product->delete();
        });
    }

    public function publishProduct($productId)
    {
        $product = $this->productRepository->findOrFail($productId);
        return $product->publish();
    }

    public function unpublishProduct($productId)
    {
        $product = $this->productRepository->findOrFail($productId);
        return $product->unpublish();
    }

    public function getProductStats($vendorId)
    {
        return $this->productRepository->getProductStats($vendorId);
    }
}