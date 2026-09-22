<?php

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductInventory;
use App\Models\Vendor;
use App\Models\Category;

echo "🚀 CREATING COMPREHENSIVE TEST DATA\n";
echo "=====================================\n\n";

$vendor = Vendor::first();
$category = Category::find(2);

if (!$vendor || !$category) {
    echo "❌ Missing vendor or category\n";
    exit;
}

echo "Using Vendor: " . $vendor->store_name . "\n";
echo "Using Category: " . $category->name . "\n\n";

// 1. PRODUCT WITH MULTIPLE IMAGES AND VARIANTS
echo "📦 TEST 1: Product with Multiple Images & Variants\n";
$product1 = Product::create([
    'vendor_id' => $vendor->id,
    'category_id' => $category->id,
    'name' => 'iPhone 15 Pro Max',
    'slug' => 'iphone-15-pro-max-' . uniqid(),
    'description' => 'Latest Apple flagship with A17 Pro chip, Pro camera system',
    'base_price' => 149999.00,
    'sku' => 'APP-IP15PM-' . rand(1000, 9999),
    'warranty' => '12 months',
    'delivery_time_days' => 3,
    'status' => 'published',
    'moderation_status' => 'approved',
    'is_visible' => true,
]);
echo "✅ Product created: {$product1->name}\n";

// Add 4 images
for ($i = 1; $i <= 4; $i++) {
    ProductImage::create([
        'product_id' => $product1->id,
        'file_name' => "iphone15pm-$i.jpg",
        'file_path' => "/storage/products/iphone15pm-$i.jpg",
        'alt_text' => "iPhone 15 Pro Max - View $i",
        'file_size' => 2048576,
        'mime_type' => 'image/jpeg',
        'display_order' => $i,
    ]);
}
echo "  ✓ 4 images added\n";

// Add variants
$colors = ['Midnight', 'Silver', 'Gold', 'Deep Purple'];
$storages = ['256GB', '512GB', '1TB'];
$variantCount = 0;

foreach ($colors as $colorIdx => $color) {
    foreach ($storages as $storageIdx => $storage) {
        $priceAdj = ($storageIdx * 15000);
        $variant = ProductVariant::create([
            'product_id' => $product1->id,
            'type' => 'Configuration',
            'value' => "$color - $storage",
            'sku' => 'APP-IP15PM-' . substr($color, 0, 3) . substr($storage, 0, 3),
            'price_adjustment' => $priceAdj,
        ]);

        ProductInventory::create([
            'product_id' => $product1->id,
            'variant_id' => $variant->id,
            'quantity' => rand(15, 50),
            'reserved_quantity' => rand(0, 5),
            'low_stock_threshold' => 10,
        ]);
        $variantCount++;
    }
}
echo "  ✓ $variantCount variants added (12 total)\n";

// Base inventory
ProductInventory::create([
    'product_id' => $product1->id,
    'quantity' => 100,
    'reserved_quantity' => 10,
    'low_stock_threshold' => 15,
]);
echo "  ✓ Base inventory: 100 units\n\n";

// 2. PRODUCT WITH FEWER VARIANTS
echo "📦 TEST 2: Product with Basic Variants\n";
$product2 = Product::create([
    'vendor_id' => $vendor->id,
    'category_id' => $category->id,
    'name' => 'Samsung Galaxy S24 Ultra',
    'slug' => 'samsung-s24-ultra-' . uniqid(),
    'description' => '200MP Main camera, Titanium frame, S Pen included',
    'base_price' => 139999.00,
    'sku' => 'SAM-S24U-' . rand(1000, 9999),
    'warranty' => '24 months',
    'delivery_time_days' => 2,
    'status' => 'published',
    'moderation_status' => 'approved',
    'is_visible' => true,
]);
echo "✅ Product created: {$product2->name}\n";

// Add 3 images
for ($i = 1; $i <= 3; $i++) {
    ProductImage::create([
        'product_id' => $product2->id,
        'file_name' => "s24ultra-$i.jpg",
        'file_path' => "/storage/products/s24ultra-$i.jpg",
        'alt_text' => "Galaxy S24 Ultra - View $i",
        'file_size' => 1536000,
        'mime_type' => 'image/jpeg',
        'display_order' => $i,
    ]);
}
echo "  ✓ 3 images added\n";

// Add 3 color variants
$s24Colors = ['Phantom Black', 'Titanium Gray', 'Titanium Blue'];
foreach ($s24Colors as $color) {
    $variant = ProductVariant::create([
        'product_id' => $product2->id,
        'type' => 'Color',
        'value' => $color,
        'sku' => 'SAM-S24U-' . strtoupper(substr($color, 0, 3)),
    ]);

    ProductInventory::create([
        'product_id' => $product2->id,
        'variant_id' => $variant->id,
        'quantity' => rand(20, 45),
        'reserved_quantity' => rand(0, 3),
        'low_stock_threshold' => 8,
    ]);
}
echo "  ✓ 3 color variants added\n";

ProductInventory::create([
    'product_id' => $product2->id,
    'quantity' => 85,
    'reserved_quantity' => 5,
    'low_stock_threshold' => 12,
]);
echo "  ✓ Base inventory: 85 units\n\n";

// 3. OUT OF STOCK PRODUCT
echo "📦 TEST 3: Out of Stock Product\n";
$product3 = Product::create([
    'vendor_id' => $vendor->id,
    'category_id' => $category->id,
    'name' => 'Google Pixel 8 Pro',
    'slug' => 'pixel-8-pro-' . uniqid(),
    'description' => 'Magic Eraser, Best Take, Face Unblur features',
    'base_price' => 129999.00,
    'sku' => 'GOO-P8P-' . rand(1000, 9999),
    'warranty' => '12 months',
    'delivery_time_days' => 5,
    'status' => 'published',
    'moderation_status' => 'approved',
    'is_visible' => true,
]);
echo "✅ Product created: {$product3->name}\n";

ProductImage::create([
    'product_id' => $product3->id,
    'file_name' => 'pixel8pro.jpg',
    'file_path' => '/storage/products/pixel8pro.jpg',
    'alt_text' => 'Pixel 8 Pro',
    'file_size' => 1024000,
    'mime_type' => 'image/jpeg',
    'display_order' => 1,
]);
echo "  ✓ 1 image added\n";

ProductInventory::create([
    'product_id' => $product3->id,
    'quantity' => 0,
    'reserved_quantity' => 0,
    'low_stock_threshold' => 5,
]);
echo "  ✓ Status: OUT OF STOCK (0 units)\n\n";

// 4. LOW STOCK PRODUCT
echo "📦 TEST 4: Low Stock Product\n";
$product4 = Product::create([
    'vendor_id' => $vendor->id,
    'category_id' => $category->id,
    'name' => 'OnePlus 12',
    'slug' => 'oneplus-12-' . uniqid(),
    'description' => '5000mAh battery, 100W SuperVOOC charging',
    'base_price' => 79999.00,
    'sku' => 'OPL-12-' . rand(1000, 9999),
    'warranty' => '12 months',
    'delivery_time_days' => 3,
    'status' => 'published',
    'moderation_status' => 'approved',
    'is_visible' => true,
]);
echo "✅ Product created: {$product4->name}\n";

ProductImage::create([
    'product_id' => $product4->id,
    'file_name' => 'oneplus12.jpg',
    'file_path' => '/storage/products/oneplus12.jpg',
    'alt_text' => 'OnePlus 12',
    'file_size' => 1200000,
    'mime_type' => 'image/jpeg',
    'display_order' => 1,
]);
echo "  ✓ 1 image added\n";

ProductInventory::create([
    'product_id' => $product4->id,
    'quantity' => 3,
    'reserved_quantity' => 1,
    'low_stock_threshold' => 5,
]);
echo "  ✓ Status: LOW STOCK (3 units, threshold: 5)\n\n";

// 5. DRAFT PRODUCT
echo "📦 TEST 5: Draft (Unpublished) Product\n";
$product5 = Product::create([
    'vendor_id' => $vendor->id,
    'category_id' => $category->id,
    'name' => 'Xiaomi 14 Ultra',
    'slug' => 'xiaomi-14-ultra-' . uniqid(),
    'description' => 'Upcoming flagship from Xiaomi',
    'base_price' => 89999.00,
    'sku' => 'XMI-14U-' . rand(1000, 9999),
    'warranty' => '12 months',
    'delivery_time_days' => 7,
    'status' => 'draft',
    'moderation_status' => 'pending',
    'is_visible' => false,
]);
echo "✅ Product created: {$product5->name}\n";
echo "  ✓ Status: DRAFT (not published)\n\n";

echo "=====================================\n";
echo "📊 FINAL STATISTICS\n";
echo "=====================================\n";
echo "✅ Total Products Created: 5\n";
echo "  - Published & Approved: 4\n";
echo "  - Draft: 1\n";
echo "  - With Images: 5\n";
echo "  - With Variants: 2 (12 + 3)\n";
echo "  - Out of Stock: 1\n";
echo "  - Low Stock: 1\n";
echo "  - Normal Stock: 2\n\n";

// Show inventory stats
$service = app(\App\Services\StockManagementService::class);
$stats = $service->getVendorStockStats($vendor->id);
echo "📈 INVENTORY STATS:\n";
echo "  - Total Products: " . $stats['total_products'] . "\n";
echo "  - In Stock: " . $stats['in_stock'] . "\n";
echo "  - Low Stock: " . $stats['low_stock'] . "\n";
echo "  - Out of Stock: " . $stats['out_of_stock'] . "\n";
echo "  - Total Items: " . $stats['total_items'] . "\n";
echo "  - Reserved: " . $stats['reserved_items'] . "\n\n";

echo "✅ TEST DATA CREATION COMPLETE!\n";
