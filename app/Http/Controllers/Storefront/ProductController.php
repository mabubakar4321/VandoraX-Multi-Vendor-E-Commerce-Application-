<?php

namespace App\Http\Controllers\Storefront;

use App\Models\Category;
use App\Models\Product;
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
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::active()->ordered()->get();

        return view('storefront.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_unless(
            $product->moderation_status === 'approved'
                && $product->status === 'published'
                && $product->is_visible,
            404
        );

        $product->load(['vendor', 'category.parent', 'subcategory', 'images', 'variants', 'specifications']);

        return view('storefront.products.show', compact('product'));
    }
}
