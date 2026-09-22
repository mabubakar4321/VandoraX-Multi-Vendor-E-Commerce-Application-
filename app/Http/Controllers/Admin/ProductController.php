<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['vendor.user', 'category', 'images'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhereHas('vendor', function ($query) use ($search) {
                            $query->where('store_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('moderation_status', $request->string('status')))
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Product::count(),
            'pending' => Product::where('moderation_status', 'pending')->count(),
            'approved' => Product::where('moderation_status', 'approved')->count(),
            'rejected' => Product::where('moderation_status', 'rejected')->count(),
            'flagged' => Product::where('moderation_status', 'flagged')->count(),
        ];

        $categories = Category::main()->active()->ordered()->get();

        return view('admin.products.moderation', compact('products', 'stats', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load(['vendor.user', 'category.parent', 'subcategory', 'images', 'variants.inventory', 'specifications', 'inventory']);

        return view('admin.products.show', compact('product'));
    }

    public function approve(Product $product)
    {
        $product->update([
            'moderation_status' => 'approved',
            'moderation_reason' => null,
            'moderated_by' => Auth::id(),
            'moderated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Product approved successfully.');
    }

    public function reject(Request $request, Product $product)
    {
        $data = $request->validate(['reason' => 'required|string|max:1000']);
        $product->update([
            'moderation_status' => 'rejected',
            'moderation_reason' => $data['reason'],
            'moderated_by' => Auth::id(),
            'moderated_at' => now(),
            'is_visible' => false,
        ]);

        return redirect()->back()->with('success', 'Product rejected successfully.');
    }

    public function flag(Request $request, Product $product)
    {
        $data = $request->validate(['reason' => 'required|string|max:1000']);
        $product->update([
            'moderation_status' => 'flagged',
            'moderation_reason' => $data['reason'],
            'moderated_by' => Auth::id(),
            'moderated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Product flagged successfully.');
    }
}
