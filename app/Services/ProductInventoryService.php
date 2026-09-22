<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\ProductInventoryLog;
use App\Repositories\ProductInventoryRepository;
use Illuminate\Support\Facades\DB;

class ProductInventoryService
{
    protected $inventoryRepository;

    public function __construct(ProductInventoryRepository $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    public function createInventory($productId, $variantId = null, $quantity = 0, $threshold = 10)
    {
        return ProductInventory::create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $quantity,
            'low_stock_threshold' => $threshold,
        ]);
    }

    public function updateStock($productId, $variantId, $newQuantity, $reason, $userId = null)
    {
        return DB::transaction(function () use ($productId, $variantId, $newQuantity, $reason, $userId) {
            return $this->inventoryRepository->updateStock(
                $productId,
                $variantId,
                $newQuantity,
                $reason,
                $userId
            );
        });
    }

    public function adjustStock($productId, $variantId, $quantity, $reason, $userId = null)
    {
        $inventory = ProductInventory::where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if (!$inventory) {
            return false;
        }

        $newQuantity = max(0, $inventory->quantity + $quantity);
        return $this->updateStock($productId, $variantId, $newQuantity, $reason, $userId);
    }

    public function reserveStock($variantId, $quantity)
    {
        $inventory = ProductInventory::where('variant_id', $variantId)->first();

        if (!$inventory || $inventory->getAvailableStock() < $quantity) {
            return false;
        }

        return $inventory->update([
            'reserved_quantity' => $inventory->reserved_quantity + $quantity,
        ]);
    }

    public function releaseReservedStock($variantId, $quantity)
    {
        $inventory = ProductInventory::where('variant_id', $variantId)->first();

        if (!$inventory) {
            return false;
        }

        return $inventory->update([
            'reserved_quantity' => max(0, $inventory->reserved_quantity - $quantity),
        ]);
    }

    public function getLowStockAlerts($vendorId)
    {
        return $this->inventoryRepository->getLowStockItems($vendorId);
    }
}