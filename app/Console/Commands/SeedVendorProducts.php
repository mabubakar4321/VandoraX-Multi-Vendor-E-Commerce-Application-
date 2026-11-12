<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\ProductSpecification;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedVendorProducts extends Command
{
    protected $signature = 'products:seed-vendor-products {--force : Delete existing seeded products and re-create}';

    protected $description = 'Create 2 products per vendor category (with variants, specifications and inventory)';

    public function handle()
    {
        $this->info('SEEDING VENDOR PRODUCTS (2 PER CATEGORY)');
        $this->line('======================================');

        $vendors = Vendor::get();
        $created = 0;
        $errors = 0;

        foreach ($vendors as $vendor) {
            $categories = Category::forVendor($vendor->id)->get();

            foreach ($categories as $category) {
                $products = $this->productsFor($category->id);

                foreach ($products as $data) {
                    $existing = Product::where('name', $data['name'])
                        ->where('vendor_id', $vendor->id)
                        ->where('category_id', $category->id)
                        ->first();

                    if ($this->option('force')) {
                        $this->deleteSeeded($vendor->id, $category->id, $data['name']);
                        $existing = null;
                    }

                    if ($existing) {
                        $this->line('SKIP  ' . $data['name'] . ' (already exists)');
                        continue;
                    }

                    try {
                        $this->createProduct($vendor, $category, $data);
                        $this->line('OK    ' . $data['name'] . ' -> ' . $category->name);
                        $created++;
                    } catch (\Exception $e) {
                        $this->error('FAIL  ' . $data['name'] . ': ' . $e->getMessage());
                        $errors++;
                    }
                }
            }
        }

        $this->newLine();
        $this->info("DONE - created: {$created}, failed: {$errors}");

        return 0;
    }

    protected function deleteSeeded(int $vendorId, int $categoryId, string $name): void
    {
        $product = Product::where('name', $name)
            ->where('vendor_id', $vendorId)
            ->where('category_id', $categoryId)
            ->first();

        if (!$product) {
            return;
        }

        foreach ($product->variants as $variant) {
            ProductInventory::where('product_id', $product->id)
                ->where('variant_id', $variant->id)
                ->delete();
            $variant->delete();
        }

        ProductInventory::where('product_id', $product->id)->delete();
        $product->delete();
    }

    protected function createProduct(Vendor $vendor, Category $category, array $data): void
    {
        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::lower(Str::random(6)),
            'description' => $data['description'],
            'base_price' => $data['base_price'],
            'sku' => strtoupper(Str::slug($data['sku_prefix'], '')) . '-' . $vendor->id . '-' . $category->id,
            'warranty' => $data['warranty'] ?? null,
            'delivery_time_days' => $data['delivery_time_days'] ?? 3,
            'status' => 'published',
            'moderation_status' => 'approved',
            'is_visible' => true,
            'meta_title' => $data['name'],
            'meta_description' => Str::limit($data['description'], 150),
            'keywords' => $data['keywords'] ?? null,
        ]);

        ProductInventory::create([
            'product_id' => $product->id,
            'quantity' => $data['stock'] ?? 50,
            'reserved_quantity' => 0,
            'low_stock_threshold' => 10,
        ]);

        foreach ($data['variants'] ?? [] as $variant) {
            $variantSku = $product->sku . '-' . strtoupper(Str::slug($variant['variant_value'], ''));
            $v = ProductVariant::create(array_merge([
                'product_id' => $product->id,
                'variant_name' => $variant['variant_name'],
                'variant_value' => $variant['variant_value'],
                'price' => $variant['price'],
                'stock' => $variant['stock'] ?? 20,
                'sku' => $variantSku,
                'weight' => $variant['weight'] ?? null,
                'dimensions' => $variant['dimensions'] ?? null,
            ]));

            ProductInventory::create([
                'product_id' => $product->id,
                'variant_id' => $v->id,
                'quantity' => $variant['stock'] ?? 20,
                'reserved_quantity' => 0,
                'low_stock_threshold' => 10,
            ]);
        }

        foreach ($data['specifications'] ?? [] as $i => $spec) {
            ProductSpecification::create([
                'product_id' => $product->id,
                'spec_key' => $spec[0],
                'spec_value' => $spec[1],
                'display_order' => $i,
            ]);
        }
    }

    /**
     * Returns 2 product definitions (with variants & specs) per category.
     */
    protected function productsFor(int $categoryId): array
    {
        static $cache = null;

        if ($cache === null) {
            $catalog = [
                include base_path('database/seed_data/vendor_products_part1.php'),
                include base_path('database/seed_data/vendor_products_part2.php'),
                include base_path('database/seed_data/vendor_products_part3.php'),
                include base_path('database/seed_data/vendor_products_part4.php'),
            ];

            // Re-key by actual category id: each category id maps to its own product list
            $cache = [];
            foreach ($catalog as $part) {
                foreach ($part as $catId => $items) {
                    $cache[$catId] = $items;
                }
            }
        }

        return $cache[$categoryId] ?? [];
    }
}
