<?php

namespace App\Repositories;

use App\Models\ProductInventory;

class ProductInventoryRepository extends BaseRepository
{
    public function __construct(ProductInventory $model)
    {
        parent::__construct($model);
    }

    public function getProductInventory($productId)
    {
        return $this->model
            ->where('product_id', $productId)
            ->with(['variant'])
            ->get();
    }

    public function getInventoryForVariant($variantId)
    {
        return $this->model
            ->where('variant_id', $variantId)
            ->first();
    }

    public function updateStock($productId, $variantId, $newQuantity, $reason, $userId = null)
    {
        $query = $this->model->where('product_id', $productId);

        if ($variantId === null) {
            $query->whereNull('variant_id');
        } else {
            $query->where('variant_id', $variantId);
        }

        $inventory = $query->first();

        if ($inventory) {
            $oldQuantity = $inventory->quantity;
            $inventory->update(['quantity' => $newQuantity]);

            // Log the change
            \App\Models\ProductInventoryLog::create([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'change_reason' => $reason,
                'updated_by' => $userId,
            ]);

            return true;
        }

        return false;
    }

    public function getLowStockItems($vendorId, $threshold = 10)
    {
        return $this->model
            ->whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->where('quantity', '<=', $threshold)
            ->with(['product', 'variant'])
            ->get();
    }
}