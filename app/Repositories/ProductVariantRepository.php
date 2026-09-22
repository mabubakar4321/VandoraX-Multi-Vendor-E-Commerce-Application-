<?php

namespace App\Repositories;

use App\Models\ProductVariant;

class ProductVariantRepository extends BaseRepository
{
    public function __construct(ProductVariant $model)
    {
        parent::__construct($model);
    }

    public function getProductVariants($productId)
    {
        return $this->model
            ->where('product_id', $productId)
            ->get();
    }

    public function skuExists($sku, $excludeId = null)
    {
        $query = $this->model->where('sku', $sku);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    public function generateUniqueSku($productId, $variantValue)
    {
        $baseSku = \Str::slug($variantValue, '-');
        $count = $this->model
            ->where('product_id', $productId)
            ->where('sku', 'like', $baseSku . '%')
            ->count();

        return $count > 0 ? $baseSku . '-' . ($count + 1) : $baseSku;
    }
}