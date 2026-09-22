<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getVendorProducts($vendorId, $perPage = 15)
    {
        return $this->model
            ->byVendor($vendorId)
            ->with(['category', 'images', 'variants'])
            ->latest()
            ->paginate($perPage);
    }

    public function getPublishedProducts($perPage = 20)
    {
        return $this->model
            ->published()
            ->with(['vendor', 'category', 'images', 'variants'])
            ->latest()
            ->paginate($perPage);
    }

    public function getProductsByCategory($categoryId, $perPage = 20)
    {
        return $this->model
            ->byCategory($categoryId)
            ->published()
            ->paginate($perPage);
    }

    public function searchProducts($query, $perPage = 20)
    {
        return $this->model
            ->published()
            ->search($query)
            ->with(['vendor', 'category', 'images'])
            ->paginate($perPage);
    }

    public function getLowStockProducts($vendorId)
    {
        return $this->model
            ->byVendor($vendorId)
            ->with(['variants', 'inventory'])
            ->get()
            ->filter(function ($product) {
                return $product->getTotalStock() < 10;
            });
    }

    public function getProductStats($vendorId)
    {
        $products = $this->model->byVendor($vendorId)->get();

        return [
            'total' => $products->count(),
            'published' => $products->where('status', 'published')->count(),
            'draft' => $products->where('status', 'draft')->count(),
            'low_stock' => $products->filter(fn($product) => $product->getTotalStock() < 10)->count(),
            'out_of_stock' => $products->filter(fn($p) => $p->isOutOfStock())->count(),
        ];
    }
}