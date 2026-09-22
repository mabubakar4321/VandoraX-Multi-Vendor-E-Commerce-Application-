<?php

namespace App\Services;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Exception;

class ProductImageService
{
    protected $disk = 'public';
    protected $path = 'products/images';
    protected $maxFileSize = 5242880; // 5MB

    public function uploadImage($file, $productId, $isMain = false)
    {
        // Validate file
        if (!$this->validateImage($file)) {
            throw new Exception('Invalid image format or size');
        }

        // Store file
        $fileName = $this->generateFileName($file);
        $filePath = $file->storeAs($this->path, $fileName, $this->disk);

        // Create thumbnail
        $thumbnailPath = $this->createThumbnail($filePath);

        // Save to database
        $productImage = ProductImage::create([
            'product_id' => $productId,
            'image_path' => $filePath,
            'thumbnail_path' => $thumbnailPath,
            'is_main' => $isMain,
            'display_order' => ProductImage::where('product_id', $productId)->count(),
            'file_size' => $file->getSize(),
        ]);

        return $productImage;
    }

    public function deleteImage($imageId)
    {
        $image = ProductImage::findOrFail($imageId);

        // Delete files from storage
        Storage::disk($this->disk)->delete($image->image_path);
        if ($image->thumbnail_path) {
            Storage::disk($this->disk)->delete($image->thumbnail_path);
        }

        // Delete from database
        return $image->delete();
    }

    public function reorderImages($imageIds)
    {
        foreach ($imageIds as $order => $imageId) {
            ProductImage::find($imageId)->update(['display_order' => $order]);
        }
        return true;
    }

    public function setMainImage($imageId)
    {
        $image = ProductImage::findOrFail($imageId);
        
        // Remove main flag from all product images
        ProductImage::where('product_id', $image->product_id)
            ->update(['is_main' => false]);

        // Set current as main
        return $image->update(['is_main' => true]);
    }

    protected function validateImage($file)
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        
        if (!in_array($file->getMimeType(), $allowed)) {
            return false;
        }

        if ($file->getSize() > $this->maxFileSize) {
            return false;
        }

        return true;
    }

    protected function generateFileName($file)
    {
        return time() . '-' . \Str::random(10) . '.' . $file->getClientOriginalExtension();
    }

    protected function createThumbnail($imagePath)
    {
        try {
            $fullPath = Storage::disk($this->disk)->path($imagePath);
            $imageManager = new ImageManager(new Driver());
            $image = $imageManager->read($fullPath);
            
            $thumbPath = str_replace(
                '/images/',
                '/images/thumbnails/',
                $imagePath
            );
            $thumbDirectory = dirname(Storage::disk($this->disk)->path($thumbPath));
            if (!is_dir($thumbDirectory)) {
                mkdir($thumbDirectory, 0755, true);
            }
            
            $image->scaleDown(300, 300)->save(Storage::disk($this->disk)->path($thumbPath));

            return $thumbPath;
        } catch (Exception $e) {
            return null;
        }
    }
}