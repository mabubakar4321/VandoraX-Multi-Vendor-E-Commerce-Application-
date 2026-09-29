<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Get all active categories
        $categories = Category::active()
            ->with('parent', 'children')
            ->ordered()
            ->get();

        // Get approved products with vendor details
        $products = Product::where('status', 'published')
            ->where('moderation_status', 'approved')
            ->with(['vendor', 'vendor.user', 'category', 'images'])
            ->latest()
            ->paginate(12);

        return view('storefront.home-luxury', compact('categories', 'products'));
    }
}
