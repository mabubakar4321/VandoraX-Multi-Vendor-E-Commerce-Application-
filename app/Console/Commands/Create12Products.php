<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Create12Products extends Command
{
    protected $signature = 'products:create-12';
    protected $description = 'Create 12 complete products - one for each category';

    public function handle()
    {
        $this->info('📦 CREATING 12 COMPLETE PRODUCTS FOR ALL CATEGORIES');
        $this->line('===================================================');
        $this->newLine();

        $productsData = [
            [
                'category' => 'Electronics & Gadgets',
                'name' => 'Apple iPhone 15 Pro',
                'description' => 'Experience the power of A17 Pro. Stunning 6.1-inch Super Retina XDR display. Advanced camera system with 48MP main camera. Titanium design. Available in 5 colors.',
                'base_price' => 149999,
                'warranty' => '12 months',
                'delivery_time' => 2,
            ],
            [
                'category' => 'Fashion & Apparel',
                'name' => 'Premium Cotton Casual Shirt',
                'description' => '100% organic cotton casual shirt. Perfect for everyday wear. Available in multiple sizes and colors. Comfortable, breathable fabric. Machine washable.',
                'base_price' => 1999,
                'warranty' => '6 months',
                'delivery_time' => 3,
            ],
            [
                'category' => 'Home & Kitchen',
                'name' => 'Stainless Steel Cookware Set',
                'description' => 'Premium 12-piece non-stick cookware set. Heat-resistant handles. Dishwasher safe. Includes pots, pans, lids, and cooking utensils. Induction compatible.',
                'base_price' => 5999,
                'warranty' => '5 years',
                'delivery_time' => 4,
            ],
            [
                'category' => 'Beauty & Personal Care',
                'name' => 'Organic Skin Care Serum',
                'description' => 'Advanced organic serum with hyaluronic acid and vitamin C. Reduces wrinkles and improves skin texture. Suitable for all skin types. Dermatologist tested.',
                'base_price' => 1499,
                'warranty' => 'No warranty',
                'delivery_time' => 2,
            ],
            [
                'category' => 'Sports & Outdoors',
                'name' => 'Professional Yoga Mat',
                'description' => 'Premium yoga mat with non-slip surface. 6mm thick for comfort. Eco-friendly TPE material. Includes carrying strap. Perfect for yoga, pilates, and fitness.',
                'base_price' => 2499,
                'warranty' => '2 years',
                'delivery_time' => 3,
            ],
            [
                'category' => 'Books & Media',
                'name' => 'The Psychology of Success',
                'description' => 'Best-selling book about personal development and achieving success. Written by renowned psychologist. 350 pages. Hardcover edition with dust jacket.',
                'base_price' => 599,
                'warranty' => 'No warranty',
                'delivery_time' => 2,
            ],
            [
                'category' => 'Toys & Games',
                'name' => 'LEGO Architecture Set',
                'description' => '1000+ pieces. Build iconic architectural landmarks. Includes display stand. Ages 14+. High-quality plastic blocks. Collectible edition.',
                'base_price' => 3999,
                'warranty' => '2 years',
                'delivery_time' => 3,
            ],
            [
                'category' => 'Health & Wellness',
                'name' => 'Vitamin D3 Supplement',
                'description' => 'High-potency vitamin D3 2000 IU. 120 capsules per bottle. Supports bone health and immunity. Third-party tested. Non-GMO and gluten-free.',
                'base_price' => 799,
                'warranty' => 'No warranty',
                'delivery_time' => 2,
            ],
            [
                'category' => 'Automotive & Accessories',
                'name' => 'Car Phone Mount Holder',
                'description' => 'Adjustable phone mount for car dashboard or windshield. Universal compatibility with all phones. 360-degree rotation. Strong suction cup. Anti-slip pads.',
                'base_price' => 499,
                'warranty' => '1 year',
                'delivery_time' => 2,
            ],
            [
                'category' => 'Jewelry & Watches',
                'name' => 'Luxury Swiss Chronograph Watch',
                'description' => 'Premium Swiss-made watch. Stainless steel case and leather strap. Water-resistant up to 50m. Chronograph function. Sapphire crystal. Comes with gift box.',
                'base_price' => 24999,
                'warranty' => '10 years',
                'delivery_time' => 5,
            ],
        ];

        $vendor = Vendor::where('store_name', 'Bakar Store')->first();

        if (!$vendor) {
            $this->error('❌ Vendor "Bakar Store" not found');
            return 1;
        }

        $timestamp = time();
        $createdCount = 0;

        foreach ($productsData as $index => $data) {
            $category = Category::where('name', $data['category'])->first();

            if (!$category) {
                $this->line("⏭️  " . ($index + 1) . ". {$data['name']} - Category not found");
                continue;
            }

            $slug = Str::slug($data['name']) . '-' . $timestamp . '-' . ($index + 1);

            try {
                $product = Product::create([
                    'vendor_id' => $vendor->id,
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'slug' => $slug,
                    'description' => $data['description'],
                    'base_price' => $data['base_price'],
                    'sku' => strtoupper(Str::slug($data['name'], '')) . '-' . rand(1000, 9999),
                    'warranty' => $data['warranty'],
                    'delivery_time_days' => $data['delivery_time'],
                    'status' => 'published',
                    'moderation_status' => 'approved',
                    'is_visible' => true,
                ]);

                ProductInventory::create([
                    'product_id' => $product->id,
                    'quantity' => rand(50, 200),
                    'reserved_quantity' => 0,
                    'low_stock_threshold' => 10,
                ]);

                $this->line("✅ " . ($index + 1) . ". {$data['name']}");
                $this->line("   ├─ Category: {$category->name}");
                $this->line("   ├─ Price: ₹" . number_format($data['base_price']));
                $this->line("   ├─ SKU: {$product->sku}");
                $this->line("   └─ Stock: " . rand(50, 200) . " units");

                $createdCount++;

            } catch (\Exception $e) {
                $this->error("❌ " . ($index + 1) . ". {$data['name']} - Error: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->line('===================================================');
        $this->info('📊 SUMMARY');
        $this->line('===================================================');

        $totalProducts = Product::count();
        $publishedProducts = Product::where('status', 'published')->count();
        $totalValue = Product::sum('base_price');
        $avgPrice = Product::avg('base_price');

        $this->line("✅ Products Created: $createdCount");
        $this->line("✅ Total Products: $totalProducts");
        $this->line("✅ Published Products: $publishedProducts");
        $this->line("✅ Total Inventory Value: ₹" . number_format($totalValue));
        $this->line("✅ Average Price: ₹" . number_format($avgPrice, 2));
        $this->newLine();

        $this->info('🎉 PRODUCT CREATION COMPLETE!');

        return 0;
    }
}
