<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected function getCart()
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        return Cart::getOrCreateForUser($userId, $sessionId);
    }

    public function index()
    {
        $cart = $this->getCart();
        $itemsByVendor = $cart->getItemsByVendor();
        $subtotal = $cart->getSubtotal();
        $totalItems = $cart->getTotalItems();

        return view('cart.index', compact('cart', 'itemsByVendor', 'subtotal', 'totalItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check if product is available
        if ($product->status !== 'published' || $product->moderation_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'This product is not available.',
            ], 400);
        }

        // Get variant if specified
        $variant = $request->variant_id
            ? ProductVariant::findOrFail($request->variant_id)
            : null;

        // Check stock availability
        $stockCheck = $this->checkStock($product, $variant, $request->quantity);
        if (!$stockCheck['available']) {
            return response()->json([
                'success' => false,
                'message' => $stockCheck['message'],
                'available_stock' => $stockCheck['available_stock'],
            ], 400);
        }

        // Get price
        $price = $variant ? $variant->price : $product->base_price;

        $cart = $this->getCart();

        // Check if item already exists
        $cartItem = $cart->items()
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;

            // Validate total quantity doesn't exceed stock
            $stockCheck = $this->checkStock($product, $variant, $newQuantity);
            if (!$stockCheck['available']) {
                return response()->json([
                    'success' => false,
                    'message' => $stockCheck['message'],
                    'available_stock' => $stockCheck['available_stock'],
                    'current_in_cart' => $cartItem->quantity,
                ], 400);
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
                'price' => $price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cart->getTotalItems(),
        ]);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getCart();

        // Verify item belongs to current cart
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cart item.',
            ], 403);
        }

        // Check stock availability
        $product = $cartItem->product;
        $variant = $cartItem->variant;

        $stockCheck = $this->checkStock($product, $variant, $request->quantity);
        if (!$stockCheck['available']) {
            return response()->json([
                'success' => false,
                'message' => $stockCheck['message'],
                'available_stock' => $stockCheck['available_stock'],
            ], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'quantity' => $cartItem->quantity,
            'item_total' => $cartItem->getTotal(),
            'cart_subtotal' => $cart->getSubtotal(),
            'cart_count' => $cart->getTotalItems(),
        ]);
    }

    public function remove(CartItem $cartItem)
    {
        $cart = $this->getCart();

        // Verify item belongs to current cart
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cart item.',
            ], 403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
            'cart_subtotal' => $cart->getSubtotal(),
            'cart_count' => $cart->getTotalItems(),
        ]);
    }

    public function clear()
    {
        $cart = $this->getCart();
        $cart->clearCart();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully!',
        ]);
    }

    public function count()
    {
        $cart = $this->getCart();

        return response()->json([
            'count' => $cart->getTotalItems(),
        ]);
    }

    public function moveToWishlist(CartItem $cartItem)
    {
        $cart = $this->getCart();

        if ($cartItem->cart_id !== $cart->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cart item.',
            ], 403);
        }

        if (auth()->check()) {
            // Add to wishlist if user is authenticated
            \App\Models\Wishlist::firstOrCreate([
                'customer_id' => auth()->id(),
                'product_id' => $cartItem->product_id,
            ]);

            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item moved to wishlist!',
                'cart_count' => $cart->getTotalItems(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Please login to add items to wishlist.',
        ], 401);
    }

    /**
     * Validate cart before checkout
     * Checks all items for stock availability and returns issues
     */
    public function validateCart()
    {
        $cart = $this->getCart();
        $items = $cart->items()->with(['product', 'variant'])->get();

        $issues = [];
        $valid = true;

        foreach ($items as $item) {
            $stockCheck = $this->checkStock($item->product, $item->variant, $item->quantity);

            if (!$stockCheck['available']) {
                $valid = false;
                $issues[] = [
                    'item_id' => $item->id,
                    'product_name' => $item->product->name,
                    'variant_name' => $item->variant ? $item->variant->variant_value : null,
                    'requested_quantity' => $item->quantity,
                    'available_stock' => $stockCheck['available_stock'],
                    'message' => $stockCheck['message'],
                ];
            }
        }

        return response()->json([
            'valid' => $valid,
            'issues' => $issues,
            'cart_count' => $cart->getTotalItems(),
            'cart_subtotal' => $cart->getSubtotal(),
        ]);
    }

    /**
     * Check stock availability for a product/variant
     *
     * @param Product $product
     * @param ProductVariant|null $variant
     * @param int $quantity
     * @return array
     */
    protected function checkStock($product, $variant, $quantity)
    {
        // If variant exists, check variant stock
        if ($variant) {
            $availableStock = $variant->stock ?? 0;

            if ($availableStock <= 0) {
                return [
                    'available' => false,
                    'message' => 'This variant is currently out of stock.',
                    'available_stock' => 0,
                ];
            }

            if ($quantity > $availableStock) {
                return [
                    'available' => false,
                    'message' => "Only {$availableStock} " . \Illuminate\Support\Str::plural('unit', $availableStock) . " available in stock.",
                    'available_stock' => $availableStock,
                ];
            }

            return [
                'available' => true,
                'available_stock' => $availableStock,
            ];
        }

        // Check product stock via getTotalStock method
        $availableStock = $product->getTotalStock();

        if ($availableStock <= 0) {
            return [
                'available' => false,
                'message' => 'This product is currently out of stock.',
                'available_stock' => 0,
            ];
        }

        if ($quantity > $availableStock) {
            return [
                'available' => false,
                'message' => "Only {$availableStock} " . \Illuminate\Support\Str::plural('unit', $availableStock) . " available in stock.",
                'available_stock' => $availableStock,
            ];
        }

        return [
            'available' => true,
            'available_stock' => $availableStock,
        ];
    }
}
