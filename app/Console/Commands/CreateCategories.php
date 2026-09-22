<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;

class CreateCategories extends Command
{
    protected $signature = 'categories:create-defaults';
    protected $description = 'Create 10 default marketplace categories';

    public function handle()
    {
        $this->info('📂 CREATING 10 MARKETPLACE CATEGORIES');
        $this->line('=====================================');
        $this->newLine();

        $categories = [
            [
                'name' => 'Electronics & Gadgets',
                'description' => 'Smartphones, laptops, tablets, smartwatches, and other electronic devices',
                'icon' => '📱',
                'commission_rate' => 5.00,
            ],
            [
                'name' => 'Fashion & Apparel',
                'description' => 'Clothing, shoes, accessories, and fashion items for men, women, and kids',
                'icon' => '👔',
                'commission_rate' => 8.00,
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Furniture, appliances, cookware, bedding, and home décor items',
                'icon' => '🏠',
                'commission_rate' => 6.00,
            ],
            [
                'name' => 'Beauty & Personal Care',
                'description' => 'Skincare, cosmetics, haircare, fragrances, and personal hygiene products',
                'icon' => '💄',
                'commission_rate' => 7.00,
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment, fitness gear, outdoor gear, and camping supplies',
                'icon' => '⚽',
                'commission_rate' => 6.50,
            ],
            [
                'name' => 'Books & Media',
                'description' => 'Books, e-books, audiobooks, movies, music, and digital content',
                'icon' => '📚',
                'commission_rate' => 4.00,
            ],
            [
                'name' => 'Toys & Games',
                'description' => 'Toys, games, puzzles, educational toys, and gaming consoles',
                'icon' => '🎮',
                'commission_rate' => 7.50,
            ],
            [
                'name' => 'Health & Wellness',
                'description' => 'Vitamins, supplements, health devices, and wellness products',
                'icon' => '⚕️',
                'commission_rate' => 5.50,
            ],
            [
                'name' => 'Automotive & Accessories',
                'description' => 'Car accessories, motorcycle parts, automotive tools, and car care products',
                'icon' => '🚗',
                'commission_rate' => 6.00,
            ],
            [
                'name' => 'Jewelry & Watches',
                'description' => 'Rings, necklaces, bracelets, earrings, watches, and fine jewelry',
                'icon' => '💍',
                'commission_rate' => 9.00,
            ],
        ];

        $admin = auth()->user() ?? \App\Models\User::where('role', 'admin')->first();

        foreach ($categories as $index => $categoryData) {
            try {
                $category = Category::create([
                    'name' => $categoryData['name'],
                    'slug' => Category::generateSlug($categoryData['name']),
                    'description' => $categoryData['description'],
                    'icon' => $categoryData['icon'],
                    'commission_rate' => $categoryData['commission_rate'],
                    'is_active' => true,
                    'created_by' => $admin?->id ?? 1,
                    'display_order' => $index + 1,
                ]);

                $this->line("✅ {$index + 1}. {$category->name}");
                $this->line("   └─ Commission: {$category->commission_rate}% | Icon: {$category->icon}");
            } catch (\Exception $e) {
                $this->error("❌ Error creating {$categoryData['name']}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->line('=====================================');
        $this->info('📊 SUMMARY');
        $this->line('=====================================');

        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();
        $totalCommission = Category::avg('commission_rate');

        $this->line("✅ Total Categories: $totalCategories");
        $this->line("✅ Active Categories: $activeCategories");
        $this->line("📊 Average Commission Rate: " . number_format($totalCommission, 2) . '%');
        $this->newLine();

        $this->info('🎉 CATEGORY CREATION COMPLETE!');

        return 0;
    }
}
