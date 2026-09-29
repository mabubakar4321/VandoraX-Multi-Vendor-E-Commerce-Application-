<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AddProductImages extends Command
{
    protected $signature = 'products:add-images';
    protected $description = 'Add product images from URLs';

    public function handle()
    {
        $this->info('🖼️  ADDING PRODUCT IMAGES');
        $this->line('========================');
        $this->newLine();

        $productImages = [
            'Apple iPhone 15 Pro' => [
                'https://images.unsplash.com/photo-1592286927505-1def25115558?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1511707267537-b85faf00021e?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&h=500&fit=crop',
            ],
            'Premium Cotton Casual Shirt' => [
                'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1596777684687-4193519ef60a?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1618354691551-418cb02291d8?w=500&h=500&fit=crop',
            ],
            'Stainless Steel Cookware Set' => [
                'https://images.unsplash.com/photo-1578500494198-246f612d03b3?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1541417904180-8175d6f407f7?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1593618998160-e34014e67546?w=500&h=500&fit=crop',
            ],
            'Organic Skin Care Serum' => [
                'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1596462502278-af242a95b598?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1570194676112-856c2c37f6dc?w=500&h=500&fit=crop',
            ],
            'Professional Yoga Mat' => [
                'https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1588286840104-8957b019727f?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1517836357463-d25ddfcbf042?w=500&h=500&fit=crop',
            ],
            'The Psychology of Success' => [
                'https://images.unsplash.com/photo-1507842217343-583b5f1d9d94?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=500&h=500&fit=crop',
            ],
            'LEGO Architecture Set' => [
                'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1519915212116-7cfef71f1d4e?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1632865486986-2e906ecf9b4e?w=500&h=500&fit=crop',
            ],
            'Vitamin D3 Supplement' => [
                'https://images.unsplash.com/photo-1587854692152-cbe660dbde0b?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1576091160550-112173f7f869?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1584308666744-24d5f3f2bbbb?w=500&h=500&fit=crop',
            ],
            'Car Phone Mount Holder' => [
                'https://images.unsplash.com/photo-1511707267537-b85faf00021e?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&h=500&fit=crop',
            ],
            'Luxury Swiss Chronograph Watch' => [
                'https://images.unsplash.com/photo-1523170335684-f032aff56298?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1503398092448-8a91eac1b04f?w=500&h=500&fit=crop',
            ],
            'Samsung Galaxy S24 Ultra' => [
                'https://images.unsplash.com/photo-1511707267537-b85faf00021e?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1592286927505-1def25115558?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1511391338551-61481537371d?w=500&h=500&fit=crop',
            ],
            'Google Pixel 8 Pro' => [
                'https://images.unsplash.com/photo-1511707267537-b85faf00021e?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1592286927505-1def25115558?w=500&h=500&fit=crop',
            ],
        ];

        $addedCount = 0;
        $totalImages = 0;

        foreach ($productImages as $productName => $imageUrls) {
            $product = Product::where('name', $productName)->first();

            if (!$product) {
                $this->line("⏭️  {$productName} - Product not found");
                continue;
            }

            // Skip if product already has images
            $existingImages = $product->images()->count();
            if ($existingImages > 0) {
                $this->line("⏭️  {$productName} - Already has {$existingImages} image(s)");
                continue;
            }

            $this->line("📦 {$productName}");

            foreach ($imageUrls as $order => $imageUrl) {
                try {
                    // Download image
                    $imageContent = @file_get_contents($imageUrl);

                    if ($imageContent === false) {
                        $this->line("   ⚠️  Image {$order + 1} - Download failed");
                        continue;
                    }

                    // Generate unique filename
                    $filename = $product->id . '-' . ($order + 1) . '-' . time() . '.jpg';
                    $path = 'products/' . $product->id . '/' . $filename;

                    // Store image
                    Storage::disk('public')->put($path, $imageContent);

                    // Create product image record
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'thumbnail_path' => $path,
                        'alt_text' => $productName . ' - Image ' . ($order + 1),
                        'display_order' => $order + 1,
                        'is_main' => $order === 0,
                    ]);

                    $this->line("   ✅ Image " . ($order + 1) . " added");
                    $totalImages++;

                } catch (\Exception $e) {
                    $this->line("   ❌ Image " . ($order + 1) . " - Error: {$e->getMessage()}");
                }
            }

            $addedCount++;
            $this->newLine();
        }

        $this->line('========================');
        $this->info('📊 SUMMARY');
        $this->line('========================');

        $totalProductsWithImages = Product::whereHas('images')->count();
        $totalImages = ProductImage::count();

        $this->line("✅ Products with images: $totalProductsWithImages");
        $this->line("✅ Total images added: $totalImages");
        $this->line("✅ Average images per product: " . ($totalProductsWithImages > 0 ? number_format($totalImages / $totalProductsWithImages, 1) : 0));
        $this->newLine();

        $this->info('🎉 PRODUCT IMAGES ADDED SUCCESSFULLY!');

        return 0;
    }
}
