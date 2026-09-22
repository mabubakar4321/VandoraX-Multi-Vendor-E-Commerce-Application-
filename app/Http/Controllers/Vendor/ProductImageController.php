<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductImageService;
use Exception;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    protected $imageService;

    public function __construct(ProductImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function upload(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        try {
            $request->validate([
                'images' => 'required|array|max:10',
                'images.*' => 'image|mimes:jpeg,png,webp|max:5120',
            ]);

            foreach ($request->file('images') as $image) {
                $this->imageService->uploadImage($image, $product->id);
            }

            return redirect()->back()
                ->with('success', 'Images uploaded successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Image upload failed: ' . $e->getMessage());
        }
    }

    public function delete(Product $product, ProductImage $image)
    {
        $this->authorize('update', $product);

        try {
            $this->imageService->deleteImage($image->id);

            return redirect()->back()
                ->with('success', 'Image deleted successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Image deletion failed: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        try {
            $request->validate(['image_ids' => 'required|array']);
            $this->imageService->reorderImages($request->image_ids);

            return response()->json(['message' => 'Images reordered successfully!']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
