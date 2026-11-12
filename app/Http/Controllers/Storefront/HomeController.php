<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;

class HomeController extends Controller
{
    public function index()
    {
        // Get all active categories with owning vendor
        $categories = Category::active()
            ->with(['parent', 'children', 'vendor'])
            ->ordered()
            ->get();

        // Get approved products with vendor details
        $products = Product::where('status', 'published')
            ->where('moderation_status', 'approved')
            ->with(['vendor', 'vendor.user', 'category', 'images'])
            ->latest()
            ->paginate(12);

        $vendors = Vendor::active()
            ->whereHas('products', function ($query) {
                $query->where('status', 'published')
                    ->where('moderation_status', 'approved')
                    ->where('is_visible', true);
            })
            ->with([
                'user',
                'category',
                'products' => function ($query) {
                    $query->where('status', 'published')
                        ->where('moderation_status', 'approved')
                        ->where('is_visible', true)
                        ->with('images')
                        ->latest();
                },
            ])
            ->latest('joined_at')
            ->take(6)
            ->get();

        return view('storefront.home-redesigned', compact('categories', 'products', 'vendors'));
    }
}
