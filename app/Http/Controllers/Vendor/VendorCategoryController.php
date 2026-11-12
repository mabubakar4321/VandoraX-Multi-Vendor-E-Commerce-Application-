<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorCategoryController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;

        $categories = Category::forVendor($vendor->id)
            ->active()
            ->ordered()
            ->get();

        return view('vendor.categories.my-categories', compact('categories', 'vendor'));
    }

    public function edit(Category $category)
    {
        $vendor = Auth::user()->vendor;

        if ((int) $category->vendor_id !== (int) $vendor->id) {
            abort(403, 'You can only edit your own categories.');
        }

        return view('vendor.categories.edit', compact('category', 'vendor'));
    }

    public function update(Request $request, Category $category)
    {
        $vendor = Auth::user()->vendor;

        if ((int) $category->vendor_id !== (int) $vendor->id) {
            abort(403, 'You can only edit your own categories.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $data = [
            'description' => $validated['description'] ?? null,
        ];

        if ($validated['name'] !== $category->name) {
            $data['name'] = $validated['name'];
            $base = Str::slug($validated['name']);
            $slug = $base;
            $n = 1;
            while (\App\Models\Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $base . '-' . $n;
                $n++;
            }
            $data['slug'] = $slug;
        }

        if ($request->hasFile('icon')) {
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            $data['icon'] = $request->file('icon')->store('categories/icons', 'public');
        }

        $category->update($data);

        return redirect()->route('vendor.categories.my')
            ->with('success', 'Category "' . $category->name . '" updated successfully.');
    }
}
