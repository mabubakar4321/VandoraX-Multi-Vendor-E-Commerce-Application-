<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Clothing & Fabric',
            'Mobile Phones & Accessories',
            'Electronics & Home Appliances',
            'Beauty & Personal Care',
            'Home & Kitchen',
            'Groceries & Food',
            'Books & Stationery',
            'Sports & Outdoor',
            'Jewellery & Watches',
            'Baby & Kids',
            'Health & Wellness',
            'Automotive & Bike Parts',
        ];

       foreach ($categories as $name) {
    Category::updateOrCreate(
        ['slug' => Str::slug($name)],
        [
            'name'      => $name,
            'is_active' => true,
        ]
    );
}

        $this->command->info(count($categories) . ' categories seeded.');
    }
}