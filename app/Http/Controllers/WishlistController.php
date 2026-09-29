<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::where('customer_id', auth()->id())
            ->with(['product.images', 'product.vendor.user', 'product.category'])
            ->latest()
            ->get();

        return view('wishlist.index', compact('wishlists'));
    }

    public function add(Request $request)
    {
        // Must be logged in
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to save items',
            ], 401);
        }

        // Validate product_id
        if (!$request->has('product_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Product ID is required',
            ], 400);
        }

        $productId = $request->input('product_id');

        // Check product exists
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        // Check product is published and approved
        if ($product->status !== 'published' || $product->moderation_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'This product is not available',
            ], 400);
        }

        // Add or update wishlist item
        $wishlist = Wishlist::firstOrCreate([
            'customer_id' => auth()->id(),
            'product_id' => $productId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to wishlist!',
            'is_new' => $wishlist->wasRecentlyCreated,
        ], 200);
    }

    public function remove(Wishlist $wishlist)
    {
        if ($wishlist->customer_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from wishlist',
        ]);
    }

    public function clear()
    {
        Wishlist::where('customer_id', auth()->id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared',
        ]);
    }

    public function count()
    {
        $count = Wishlist::where('customer_id', auth()->id())->count();

        return response()->json([
            'count' => $count,
        ]);
    }
}



