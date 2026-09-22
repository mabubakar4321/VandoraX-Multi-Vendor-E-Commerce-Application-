<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductInventory;
use App\Models\Vendor;
use App\Models\Category;
use Illuminate\Console\Command;

class CreateTestData extends Command
{
    protected $signature = 'test:create-data';
    protected $description = 'Create comprehensive test data for product management';

    public function handle()
    {
        $this->info('🚀 CREATING COMPREHENSIVE TEST DATA');
        $this->line('=====================================');
        $this->newLine();

        $timestamp = time();
        $vendor = Vendor::first();
        $category = Category::find(2);

        if (!$vendor || !$category) {
            $this->error('❌ Missing vendor or category');
            return 1;
        }

        $this->info("Using Vendor: {$vendor->store_name}");
        $this->info("Using Category: {$category->name}");
        $this->newLine();

        // 1. PRODUCT WITH MULTIPLE IMAGES AND VARIANTS
        $this->info('📦 TEST 1: Product with Multiple Images & Variants');
        $product1 = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'iPhone 15 Pro Max',
            'slug' => 'iphone-15-pro-max-' . uniqid(),
            'description' => 'Latest Apple flagship with A17 Pro chip, Pro camera system',
            'base_price' => 149999.00,
            'sku' => 'APP-IP15PM-' . $timestamp,
            'warranty' => '12 months',
            'delivery_time_days' => 3,
            'status' => 'published',
            'moderation_status' => 'approved',
            'is_visible' => true,
        ]);
        $this->line("✅ Product created: {$product1->name}");

        // Add 4 images
        for ($i = 1; $i <= 4; $i++) {
            ProductImage::create([
                'product_id' => $product1->id,
                'image_path' => "products/iphone15pm-$i.jpg",
                'thumbnail_path' => "products/thumbnails/iphone15pm-$i.jpg",
                'alt_text' => "iPhone 15 Pro Max - View $i",
                'file_size' => 2048576,
                'display_order' => $i,
                'is_main' => $i === 1,
            ]);
        }
        $this->line('  ✓ 4 images added');

        // Add variants
        $colors = ['Midnight', 'Silver', 'Gold', 'Deep Purple'];
        $storages = ['256GB', '512GB', '1TB'];
        $variantCount = 0;

        foreach ($colors as $color) {
            foreach ($storages as $storageIdx => $storage) {
                $priceAdj = ($storageIdx * 15000);
                $variant = ProductVariant::create([
                    'product_id' => $product1->id,
                    'variant_name' => 'Configuration',
                    'variant_value' => "$color - $storage",
                    'sku' => 'APP-IP15PM-' . substr($color, 0, 3) . substr($storage, 0, 3) . '-' . $timestamp,
                    'price' => $priceAdj,
                    'stock' => rand(15, 50),
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
        $this->line("  ✓ $variantCount variants added (12 total)");

        // Base inventory
        ProductInventory::create([
            'product_id' => $product1->id,
            'quantity' => 100,
            'reserved_quantity' => 10,
            'low_stock_threshold' => 15,
        ]);
        $this->line('  ✓ Base inventory: 100 units');
        $this->newLine();

        // 2. PRODUCT WITH FEWER VARIANTS
        $this->info('📦 TEST 2: Product with Basic Variants');
        $product2 = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Samsung Galaxy S24 Ultra',
            'slug' => 'samsung-s24-ultra-' . uniqid(),
            'description' => '200MP Main camera, Titanium frame, S Pen included',
            'base_price' => 139999.00,
            'sku' => 'SAM-S24U-' . $timestamp,
            'warranty' => '24 months',
            'delivery_time_days' => 2,
            'status' => 'published',
            'moderation_status' => 'approved',
            'is_visible' => true,
        ]);
        $this->line("✅ Product created: {$product2->name}");

        // Add 3 images
        for ($i = 1; $i <= 3; $i++) {
            ProductImage::create([
                'product_id' => $product2->id,
                'image_path' => "products/s24ultra-$i.jpg",
                'thumbnail_path' => "products/thumbnails/s24ultra-$i.jpg",
                'alt_text' => "Galaxy S24 Ultra - View $i",
                'file_size' => 1536000,
                'display_order' => $i,
                'is_main' => $i === 1,
            ]);
        }
        $this->line('  ✓ 3 images added');

        // Add 3 color variants
        $s24Colors = ['Phantom Black', 'Titanium Gray', 'Titanium Blue'];
        foreach ($s24Colors as $idx => $color) {
            $variant = ProductVariant::create([
                'product_id' => $product2->id,
                'variant_name' => 'Color',
                'variant_value' => $color,
                'sku' => 'SAM-S24U-' . strtoupper(substr($color, 0, 3)) . '-' . $timestamp . '-' . $idx,
                'price' => 0,
                'stock' => rand(20, 45),
            ]);

            ProductInventory::create([
                'product_id' => $product2->id,
                'variant_id' => $variant->id,
                'quantity' => rand(20, 45),
                'reserved_quantity' => rand(0, 3),
                'low_stock_threshold' => 8,
            ]);
        }
        $this->line('  ✓ 3 color variants added');

        ProductInventory::create([
            'product_id' => $product2->id,
            'quantity' => 85,
            'reserved_quantity' => 5,
            'low_stock_threshold' => 12,
        ]);
        $this->line('  ✓ Base inventory: 85 units');
        $this->newLine();

        // 3. OUT OF STOCK PRODUCT
        $this->info('📦 TEST 3: Out of Stock Product');
        $product3 = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Google Pixel 8 Pro',
            'slug' => 'pixel-8-pro-' . uniqid(),
            'description' => 'Magic Eraser, Best Take, Face Unblur features',
            'base_price' => 129999.00,
            'sku' => 'GOO-P8P-' . $timestamp,
            'warranty' => '12 months',
            'delivery_time_days' => 5,
            'status' => 'published',
            'moderation_status' => 'approved',
            'is_visible' => true,
        ]);
        $this->line("✅ Product created: {$product3->name}");

        ProductImage::create([
            'product_id' => $product3->id,
            'image_path' => 'products/pixel8pro.jpg',
            'thumbnail_path' => 'products/thumbnails/pixel8pro.jpg',
            'alt_text' => 'Pixel 8 Pro',
            'file_size' => 1024000,
            'display_order' => 1,
            'is_main' => true,
        ]);
        $this->line('  ✓ 1 image added');

        ProductInventory::create([
            'product_id' => $product3->id,
            'quantity' => 0,
            'reserved_quantity' => 0,
            'low_stock_threshold' => 5,
        ]);
        $this->line('  ✓ Status: OUT OF STOCK (0 units)');
        $this->newLine();

        // 4. LOW STOCK PRODUCT
        $this->info('📦 TEST 4: Low Stock Product');
        $product4 = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'OnePlus 12',
            'slug' => 'oneplus-12-' . uniqid(),
            'description' => '5000mAh battery, 100W SuperVOOC charging',
            'base_price' => 79999.00,
            'sku' => 'OPL-12-' . $timestamp,
            'warranty' => '12 months',
            'delivery_time_days' => 3,
            'status' => 'published',
            'moderation_status' => 'approved',
            'is_visible' => true,
        ]);
        $this->line("✅ Product created: {$product4->name}");

        ProductImage::create([
            'product_id' => $product4->id,
            'image_path' => 'products/oneplus12.jpg',
            'thumbnail_path' => 'products/thumbnails/oneplus12.jpg',
            'alt_text' => 'OnePlus 12',
            'file_size' => 1200000,
            'display_order' => 1,
            'is_main' => true,
        ]);
        $this->line('  ✓ 1 image added');

        ProductInventory::create([
            'product_id' => $product4->id,
            'quantity' => 3,
            'reserved_quantity' => 1,
            'low_stock_threshold' => 5,
        ]);
        $this->line('  ✓ Status: LOW STOCK (3 units, threshold: 5)');
        $this->newLine();

        // 5. DRAFT PRODUCT
        $this->info('📦 TEST 5: Draft (Unpublished) Product');
        $product5 = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Xiaomi 14 Ultra',
            'slug' => 'xiaomi-14-ultra-' . uniqid(),
            'description' => 'Upcoming flagship from Xiaomi',
            'base_price' => 89999.00,
            'sku' => 'XMI-14U-' . $timestamp,
            'warranty' => '12 months',
            'delivery_time_days' => 7,
            'status' => 'draft',
            'moderation_status' => 'pending',
            'is_visible' => false,
        ]);
        $this->line("✅ Product created: {$product5->name}");
        $this->line('  ✓ Status: DRAFT (not published)');
        $this->newLine();

        $this->line('=====================================');
        $this->info('📊 FINAL STATISTICS');
        $this->line('=====================================');
        $this->line('✅ Total Products Created: 5');
        $this->line('  - Published & Approved: 4');
        $this->line('  - Draft: 1');
        $this->line('  - With Images: 5');
        $this->line('  - With Variants: 2 (12 + 3)');
        $this->line('  - Out of Stock: 1');
        $this->line('  - Low Stock: 1');
        $this->line('  - Normal Stock: 2');
        $this->newLine();

        // Show inventory stats
        $service = app(\App\Services\StockManagementService::class);
        $stats = $service->getVendorStockStats($vendor->id);
        $this->info('📈 INVENTORY STATS:');
        $this->line("  - Total Products: " . $stats['total_products']);
        $this->line("  - In Stock: " . $stats['in_stock']);
        $this->line("  - Low Stock: " . $stats['low_stock']);
        $this->line("  - Out of Stock: " . $stats['out_of_stock']);
        $this->line("  - Total Items: " . $stats['total_items']);
        $this->line("  - Reserved: " . $stats['reserved_items']);
        $this->newLine();

        $this->info('✅ TEST DATA CREATION COMPLETE!');
        return 0;
    }
}
