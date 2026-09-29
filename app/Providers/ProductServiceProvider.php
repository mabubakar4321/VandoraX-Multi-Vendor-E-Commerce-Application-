<?php

namespace App\Providers;

use App\Repositories\ProductRepository;
use App\Repositories\ProductVariantRepository;
use App\Repositories\ProductInventoryRepository;
use App\Services\ProductService;
use App\Services\ProductImageService;
use App\Services\ProductBulkImportService;
use App\Services\ProductInventoryService;
use Illuminate\Support\ServiceProvider;

class ProductServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repositories
        $this->app->bind(ProductRepository::class, function ($app) {
            return new ProductRepository(new \App\Models\Product());
        });

        $this->app->bind(ProductVariantRepository::class, function ($app) {
            return new ProductVariantRepository(new \App\Models\ProductVariant());
        });

        $this->app->bind(ProductInventoryRepository::class, function ($app) {
            return new ProductInventoryRepository(new \App\Models\ProductInventory());
        });

        // Services
        $this->app->bind(ProductService::class, function ($app) {
            return new ProductService(
                $app->make(ProductRepository::class),
                $app->make(ProductInventoryService::class)
            );
        });

        $this->app->bind(ProductImageService::class, function ($app) {
            return new ProductImageService();
        });

        $this->app->bind(ProductBulkImportService::class, function ($app) {
            return new ProductBulkImportService();
        });

        $this->app->bind(ProductInventoryService::class, function ($app) {
            return new ProductInventoryService($app->make(ProductInventoryRepository::class));
        });
    }

    public function boot(): void
    {
        //
    }
}