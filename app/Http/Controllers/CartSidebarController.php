<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartSidebarController extends Controller
{
    /**
     * Get cart items as JSON for sidebar display
     */
    public function items()
    {
        $cart = Cart::getOrCreateForUser(auth()->id(), session()->getId());

        $itemsByVendor = $cart->items()
            ->with(['product.vendor.user', 'product.images', 'variant'])
            ->get()
            ->groupBy('product.vendor_id');

        $subtotal = $cart->getSubtotal();
        $totalItems = $cart->getTotalItems();

        return response()->json([
            'success' => true,
            'items_by_vendor' => $itemsByVendor->map(function($items) {
                return $items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'product_image' => $item->product->mainImage() ? asset('storage/' . $item->product->mainImage()->image_path) : null,
                        'variant_name' => $item->variant ? $item->variant->variant_value : null,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->getTotal(),
                        'vendor_name' => $item->product->vendor->user->name,
                    ];
                })->values();
            })->values(),
            'subtotal' => $subtotal,
            'total_items' => $totalItems,
        ]);
    }

    /**
     * Get minimal cart count and total for header
     */
    public function summary()
    {
        $cart = Cart::getOrCreateForUser(auth()->id(), session()->getId());

        return response()->json([
            'count' => $cart->getTotalItems(),
            'subtotal' => $cart->getSubtotal(),
        ]);
    }
}
