<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductInventoryLog;
use App\Services\StockManagementService;
use Illuminate\Http\Request;

class StockManagementController extends Controller
{
    protected $stockService;

    public function __construct(StockManagementService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Stock dashboard - shows all stock information
     */
    public function dashboard()
    {
        $vendor = auth()->user()->vendor;
        $stats = $this->stockService->getVendorStockStats($vendor->id);

        // Get products grouped by stock status
        $lowStockProducts = $this->stockService->getLowStockProducts($vendor->id)
            ->sortBy('name')
            ->take(10);

        $outOfStockProducts = $this->stockService->getOutOfStockProducts($vendor->id)
            ->sortBy('name')
            ->take(10);

        // Get recent inventory logs
        $recentLogs = ProductInventoryLog::where('product_id', '!=', null)
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        return view('vendor.stock.dashboard', compact(
            'stats',
            'lowStockProducts',
            'outOfStockProducts',
            'recentLogs'
        ));
    }

    /**
     * Show inventory for a specific product
     */
    public function show(Product $product)
    {
        $this->authorize('update', $product);

        $vendor = auth()->user()->vendor;
        $inventory = $this->stockService->getInventory($product);
        $logs = $this->stockService->getInventoryHistory($product);
        $variants = $product->variants;

        return view('vendor.stock.show', compact(
            'product',
            'inventory',
            'logs',
            'variants'
        ));
    }

    /**
     * Adjust stock (manual adjustment)
     */
    public function adjust(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'quantity_change' => 'required|integer',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $variant = null;
        if ($validated['variant_id'] ?? null) {
            $variant = $product->variants()->findOrFail($validated['variant_id']);
        }

        $log = $this->stockService->adjustStock(
            $product,
            $validated['quantity_change'],
            'manual_adjustment',
            $validated['reason'] ?? '',
            $variant,
            $validated['notes'] ?? ''
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'log' => $log,
            ]);
        }

        return back()->with('success', 'Stock adjusted successfully');
    }

    /**
     * Set exact stock quantity
     */
    public function setQuantity(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $variant = null;
        if ($validated['variant_id'] ?? null) {
            $variant = $product->variants()->findOrFail($validated['variant_id']);
        }

        $log = $this->stockService->setStock(
            $product,
            $validated['quantity'],
            $validated['reason'] ?? '',
            $variant,
            $validated['notes'] ?? ''
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'log' => $log,
            ]);
        }

        return back()->with('success', 'Stock updated successfully');
    }

    /**
     * Set low stock threshold
     */
    public function setThreshold(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'threshold' => 'required|integer|min:0|max:1000',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $variant = null;
        if ($validated['variant_id'] ?? null) {
            $variant = $product->variants()->findOrFail($validated['variant_id']);
        }

        $this->stockService->setLowStockThreshold(
            $product,
            $validated['threshold'],
            $variant
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Low stock threshold updated',
            ]);
        }

        return back()->with('success', 'Threshold updated successfully');
    }

    /**
     * Get inventory history for product
     */
    public function history(Request $request, Product $product)
    {
        $this->authorize('view', $product);

        $logs = $this->stockService->getInventoryHistory($product, null, 100);

        if ($request->wantsJson()) {
            return response()->json($logs);
        }

        return view('vendor.stock.history', compact('product', 'logs'));
    }

    /**
     * Get stock status API (for dashboard widgets)
     */
    public function getStatus(Product $product)
    {
        $this->authorize('view', $product);

        $status = $this->stockService->getStockStatus($product);
        $inventory = $this->stockService->getInventory($product);

        return response()->json([
            'product_id' => $product->id,
            'status' => $status,
            'quantity' => $inventory->quantity,
            'available' => $inventory->getAvailableStock(),
            'reserved' => $inventory->reserved_quantity,
            'threshold' => $inventory->low_stock_threshold,
            'is_low_stock' => $this->stockService->isLowStock($product),
            'is_out_of_stock' => $this->stockService->isOutOfStock($product),
        ]);
    }

    /**
     * Bulk adjust stock
     */
    public function bulkAdjust(Request $request)
    {
        $vendor = auth()->user()->vendor;

        $validated = $request->validate([
            'adjustments' => 'required|array',
            'adjustments.*.product_id' => 'required|exists:products,id',
            'adjustments.*.quantity_change' => 'required|integer',
            'adjustments.*.reason' => 'nullable|string',
        ]);

        $results = [];

        foreach ($validated['adjustments'] as $adjustment) {
            $product = Product::where('vendor_id', $vendor->id)
                ->findOrFail($adjustment['product_id']);

            $log = $this->stockService->adjustStock(
                $product,
                $adjustment['quantity_change'],
                'manual_adjustment',
                $adjustment['reason'] ?? ''
            );

            $results[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'log_id' => $log->id,
            ];
        }

        return back()->with('success', count($results) . ' products updated successfully');
    }
}
