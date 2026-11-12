<?php

namespace App\Http\Controllers\Storefront;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->where('moderation_status', 'approved')
            ->where('status', 'published')
            ->where('is_visible', true)
            ->with(['vendor', 'category', 'images'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
            ->when($request->filled('sort'), function ($query) use ($request) {
                match ($request->input('sort')) {
                    'price_asc' => $query->orderBy('base_price', 'asc'),
                    'price_desc' => $query->orderBy('base_price', 'desc'),
                    'name_asc' => $query->orderBy('name', 'asc'),
                    'oldest' => $query->oldest(),
                    default => $query->latest(),
                };
            }, fn ($query) => $query->latest())
            ->paginate(12)
            ->withQueryString();

        $categories = Category::active()->ordered()->get();

        return view('storefront.products.index', compact('products', 'categories'));
    }

    public function show(Request $request, Product $product)
    {
        abort_unless(
            $product->moderation_status === 'approved'
                && $product->status === 'published'
                && $product->is_visible,
            404
        );

        $product->load(['vendor.user', 'category.parent', 'subcategory', 'images', 'variants', 'specifications']);

        // Related products from the same category
        $relatedProducts = Product::query()
            ->where('moderation_status', 'approved')
            ->where('status', 'published')
            ->where('is_visible', true)
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->with(['images', 'category'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        // More products from the same vendor
        $vendorProducts = Product::query()
            ->where('moderation_status', 'approved')
            ->where('status', 'published')
            ->where('is_visible', true)
            ->where('id', '!=', $product->id)
            ->where('vendor_id', $product->vendor_id)
            ->with(['images', 'category'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        $reviews = Review::where('product_id', $product->id)
            ->where('status', 'approved')
            ->with(['customer', 'images', 'response.vendor'])
            ->when($request->input('sort'), function ($query) use ($request) {
                match ($request->input('sort')) {
                    'recent' => $query->latest(),
                    'highest' => $query->orderByDesc('product_rating'),
                    'lowest' => $query->orderBy('product_rating'),
                    default => $query->orderByDesc('is_helpful_count'),
                };
            }, fn ($q) => $q->orderByDesc('is_helpful_count'))
            ->paginate(10)
            ->withQueryString();

        $ratingBreakdown = Review::where('product_id', $product->id)
            ->where('status', 'approved')
            ->selectRaw('product_rating, COUNT(*) as count')
            ->groupBy('product_rating')
            ->pluck('count', 'product_rating');

        $canReview = false;
        if (auth()->check() && auth()->user()->hasRole('customer')) {
            $hasPurchased = \App\Models\OrderItem::whereHas('order', function ($q) {
                $q->where('customer_id', auth()->id())
                  ->where('status', 'delivered');
            })->where('product_id', $product->id)->exists();

            $hasReviewed = Review::where('product_id', $product->id)
                ->where('customer_id', auth()->id())
                ->exists();

            $canReview = $hasPurchased && !$hasReviewed;
        }

        return view('storefront.products.show', compact('product', 'relatedProducts', 'vendorProducts', 'reviews', 'ratingBreakdown', 'canReview'));
    }
}
