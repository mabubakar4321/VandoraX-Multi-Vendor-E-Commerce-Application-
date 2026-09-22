<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryImageController extends Controller
{
    public function edit(VendorCategoryRequest $categoryRequest)
    {
        $vendor = auth()->user()->vendor;

        if ($categoryRequest->vendor_id !== $vendor->id) {
            abort(403, 'Unauthorized');
        }

        if (!$categoryRequest->canUploadImage()) {
            return redirect()->back()->with('error', 'This category request has not been approved yet.');
        }

        return view('vendor.categories.upload-image', [
            'categoryRequest' => $categoryRequest,
        ]);
    }

    public function store(Request $request, VendorCategoryRequest $categoryRequest)
    {
        $vendor = auth()->user()->vendor;

        if ($categoryRequest->vendor_id !== $vendor->id) {
            abort(403, 'Unauthorized');
        }

        if (!$categoryRequest->canUploadImage()) {
            return response()->json(['error' => 'This category request has not been approved yet.'], 403);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,gif,webp|max:2048',
            'alt_text' => 'nullable|string|max:255',
        ]);

        // Delete old image if exists
        if ($categoryRequest->image_path) {
            Storage::disk('public')->delete($categoryRequest->image_path);
        }

        // Store new image
        $path = $request->file('image')->store(
            'vendor-categories/' . $vendor->id,
            'public'
        );

        $categoryRequest->update([
            'image_path' => $path,
            'image_alt_text' => $request->alt_text,
        ]);

        return redirect()->back()->with('success', 'Category image uploaded successfully!');
    }

    public function destroy(VendorCategoryRequest $categoryRequest)
    {
        $vendor = auth()->user()->vendor;

        if ($categoryRequest->vendor_id !== $vendor->id) {
            abort(403, 'Unauthorized');
        }

        if ($categoryRequest->image_path) {
            Storage::disk('public')->delete($categoryRequest->image_path);
            $categoryRequest->update([
                'image_path' => null,
                'image_alt_text' => null,
            ]);
        }

        return redirect()->back()->with('success', 'Category image deleted successfully!');
    }
}
