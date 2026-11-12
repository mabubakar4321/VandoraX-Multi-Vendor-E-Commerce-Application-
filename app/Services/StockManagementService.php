<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\ProductInventoryLog;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class StockManagementService
{
    /**
     * Adjust stock for a product or variant
     */
    public function adjustStock(
        Product $product,
        int $quantity,
        string $actionType = 'manual_adjustment',
        string $reason = '',
        ProductVariant $variant = null,
        string $notes = ''
    ): ProductInventoryLog
    {
        $inventory = $this->getInventory($product, $variant);

        // Record before state
        $quantityBefore = $inventory->quantity;

        // Calculate change
        $quantityChange = $quantity;
        $quantityAfter = max(0, $inventory->quantity + $quantityChange);

        // Update inventory
        $inventory->update(['quantity' => $quantityAfter]);

        // Log the change
        $log = ProductInventoryLog::create([
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'quantity_change' => $quantityChange,
            'action_type' => $actionType,
            'reason' => $reason,
            'notes' => $notes,
            'created_by' => Auth::id() ?? null,
        ]);

        return $log;
    }

    /**
     * Set stock to exact quantity
     */
    public function setStock(
        Product $product,
        int $quantity,
        string $reason = '',
        ProductVariant $variant = null,
        string $notes = ''
    ): ProductInventoryLog
    {
        $inventory = $this->getInventory($product, $variant);
        $change = $quantity - $inventory->quantity;

        return $this->adjustStock(
            $product,
            $change,
            'manual_adjustment',
            $reason,
            $variant,
            $notes
        );
    }

    /**
     * Get or create inventory for product/variant
     */
    public function getInventory(Product $product, ProductVariant $variant = null): ProductInventory
    {
        $query = ProductInventory::where('product_id', $product->id);

        if ($variant) {
            $query->where('variant_id', $variant->id);
        } else {
            $query->whereNull('variant_id');
        }

        return $query->firstOrCreate(
            [
                'product_id' => $product->id,
                'variant_id' => $variant?->id
            ],
            [
                'quantity' => 0,
                'reserved_quantity' => 0,
                'low_stock_threshold' => 5
            ]
        );
    }

    /**
     * Get available stock
     */
    public function getAvailableStock(Product $product, ProductVariant $variant = null): int
    {
        $inventory = $this->getInventory($product, $variant);
        return $inventory->getAvailableStock();
    }

    /**
     * Check if product is low stock
     */
    public function isLowStock(Product $product, ProductVariant $variant = null): bool
    {
        $inventory = $this->getInventory($product, $variant);
        return $inventory->isLowStock();
    }

    /**
     * Check if product is out of stock
     */
    public function isOutOfStock(Product $product, ProductVariant $variant = null): bool
    {
        return $this->getAvailableStock($product, $variant) <= 0;
    }

    /**
     * Get stock status
     */
    public function getStockStatus(Product $product, ProductVariant $variant = null): string
    {
        $available = $this->getAvailableStock($product, $variant);

        if ($available <= 0) {
            return 'out_of_stock';
        }

        if ($this->isLowStock($product, $variant)) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    /**
     * Get vendor stock statistics
     */
    public function getVendorStockStats($vendorId)
    {
        $products = Product::where('vendor_id', $vendorId)->with('inventory')->get();

        $stats = [
            'total_products' => $products->count(),
            'in_stock' => 0,
            'low_stock' => 0,
            'out_of_stock' => 0,
            'total_items' => 0,
            'reserved_items' => 0,
        ];

        foreach ($products as $product) {
            $inventory = $product->inventory;
            $quantity = (int) $inventory->sum('quantity');
            $reserved = (int) $inventory->sum('reserved_quantity');
            $available = $quantity - $reserved;
            $threshold = (int) $inventory->sum('low_stock_threshold');

            $stats['total_items'] += $quantity;
            $stats['reserved_items'] += $reserved;

            if ($available <= 0) {
                $stats['out_of_stock']++;
            } elseif ($available <= $threshold) {
                $stats['low_stock']++;
            } else {
                $stats['in_stock']++;
            }
        }

        return $stats;
    }

    /**
     * Get low stock products for vendor
     */
    public function getLowStockProducts($vendorId)
    {
        $products = Product::where('vendor_id', $vendorId)->with('inventory')->get();

        return $products->filter(function ($product) {
            $quantity = (int) $product->inventory->sum('quantity');
            $reserved = (int) $product->inventory->sum('reserved_quantity');
            $threshold = (int) $product->inventory->sum('low_stock_threshold');

            return $quantity - $reserved > 0 && $quantity - $reserved <= $threshold;
        });
    }

    /**
     * Get out of stock products for vendor
     */
    public function getOutOfStockProducts($vendorId)
    {
        $products = Product::where('vendor_id', $vendorId)->with('inventory')->get();

        return $products->filter(function ($product) {
            $quantity = (int) $product->inventory->sum('quantity');
            $reserved = (int) $product->inventory->sum('reserved_quantity');

            return $quantity - $reserved <= 0;
        });
    }

    /**
     * Set low stock threshold
     */
    public function setLowStockThreshold(
        Product $product,
        int $threshold,
        ?ProductVariant $variant = null
    ): void
    {
        $inventory = $this->getInventory($product, $variant);
        $inventory->update(['low_stock_threshold' => max(0, $threshold)]);
    }

    /**
     * Get inventory history
     */
    public function getInventoryHistory(Product $product, ProductVariant $variant = null, $limit = 50)
    {
        $query = ProductInventoryLog::forProduct($product->id)->recent();

        if ($variant) {
            $query->forVariant($variant->id);
        }

        return $query->paginate($limit);
    }
}
