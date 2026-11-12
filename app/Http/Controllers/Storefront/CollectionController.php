<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CollectionController extends Controller
{



    public function newArrivals()
    {
        $products = Product::where('status', 'published')
            ->where('moderation_status', 'approved')
            ->with(['vendor', 'vendor.user', 'category', 'images'])
            ->latest()
            ->paginate(16);

        $title = 'New Arrivals';
        $description = 'Discover our latest luxury pieces, fresh from our premium vendors';

        return view('storefront.collection', compact('products', 'title', 'description'));
    }




    public function bestsellers()
    {
        // Get products with highest views or random selection for now
        $products = Product::where('status', 'published')
            ->where('moderation_status', 'approved')
            ->with(['vendor', 'vendor.user', 'category', 'images'])
            ->inRandomOrder()
            ->paginate(16);

        $title = 'Bestsellers';
        $description = 'Our most loved pieces, handpicked by our luxury community';

        return view('storefront.collection', compact('products', 'title', 'description'));
    }

    public function summer()
    {
        // Filter summer-appropriate categories
        $summerCategories = ['Fashion', 'Beauty', 'Sports'];

        $products = Product::where('status', 'published')
            ->where('moderation_status', 'approved')
            ->whereHas('category', function($query) use ($summerCategories) {
                $query->whereIn('name', $summerCategories);
            })
            ->with(['vendor', 'vendor.user', 'category', 'images'])
            ->latest()
            ->paginate(16);

        $title = 'Summer Edition';
        $description = 'Light, elegant pieces perfect for the warm season';

        return view('storefront.collection', compact('products', 'title', 'description'));
    }

    public function category($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $products = Product::where('status', 'published')
            ->where('moderation_status', 'approved')
            ->where('category_id', $categoryId)
            ->with(['vendor', 'vendor.user', 'category', 'images'])
            ->latest()
            ->paginate(16);

        $title = $category->name;
        $description = 'Explore our curated ' . strtolower($category->name) . ' collection';

        return view('storefront.collection', compact('products', 'title', 'description', 'category'));
    }
}
