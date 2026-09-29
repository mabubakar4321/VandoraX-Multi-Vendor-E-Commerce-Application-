<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

echo "🔧 Fixing images for products 19 and 24\n";
echo "========================================\n\n";

$imageData = [
    19 => [ // Stainless Steel Cookware Set
        'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&h=800&fit=crop&q=85',
        'https://images.unsplash.com/photo-1604335399105-a0c585fd81a1?w=800&h=800&fit=crop&q=85',
        'https://images.unsplash.com/photo-1585659722983-3a675dabf23d?w=800&h=800&fit=crop&q=85',
    ],
    24 => [ // Vitamin D3 Supplement - Skip if has images
        'https://images.unsplash.com/photo-1471864190281-a93a3070b6de?w=800&h=800&fit=crop&q=85',
    ],
];

foreach ($imageData as $productId => $imageUrls) {
    $product = Product::find($productId);

    if (!$product) {
        echo "❌ Product {$productId} not found\n";
        continue;
    }

    echo "📦 Processing: {$product->name}\n";

    // Delete old images
    foreach ($product->images as $img) {
        Storage::disk('public')->delete($img->image_path);
        $img->delete();
        echo "   🗑️  Deleted old image\n";
    }

    // Download and add new images
    foreach ($imageUrls as $order => $imageUrl) {
        try {
            echo "   📥 Downloading image " . ($order + 1) . "...\n";

            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
                ]
            ]);

            $imageContent = file_get_contents($imageUrl, false, $context);

            if ($imageContent === false || strlen($imageContent) < 10000) {
                echo "   ⚠️  Image too small or failed, skipping\n";
                continue;
            }

            // Generate unique filename
            $filename = $product->id . '-' . ($order + 1) . '-' . time() . rand(100, 999) . '.jpg';
            $path = 'products/' . $product->id . '/' . $filename;

            // Store image
            Storage::disk('public')->put($path, $imageContent);

            // Verify file was saved and has good size
            $savedPath = storage_path('app/public/' . $path);
            $fileSize = filesize($savedPath);

            if ($fileSize < 10000) {
                echo "   ⚠️  Saved file too small ({$fileSize} bytes), deleting\n";
                Storage::disk('public')->delete($path);
                continue;
            }

            // Create product image record
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'thumbnail_path' => $path,
                'alt_text' => $product->name . ' - Image ' . ($order + 1),
                'display_order' => $order + 1,
                'is_main' => $order === 0,
            ]);

            echo "   ✅ Image " . ($order + 1) . " added ({$fileSize} bytes)\n";

            // Small delay to avoid hitting rate limits
            sleep(1);

        } catch (Exception $e) {
            echo "   ❌ Error: {$e->getMessage()}\n";
        }
    }

    echo "\n";
}

echo "========================================\n";
echo "✨ Done!\n\n";

// Verify
foreach ([19, 24] as $productId) {
    $product = Product::find($productId);
    $imageCount = $product->images()->count();
    echo "Product {$productId} ({$product->name}): {$imageCount} images\n";
}
