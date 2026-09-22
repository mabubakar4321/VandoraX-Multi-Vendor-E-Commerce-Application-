<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
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

            if (isset($data['name']) && $data['name'] !== $product->name) {
                $data['slug'] = Product::generateSlug($data['name']);
            }

            return $this->productRepository->update($productId, $data);
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