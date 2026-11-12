<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBulkImport;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\ProductInventory;
use App\Models\ProductSpecification;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductBulkImportService
{
    public function importFromFile($file, $vendorId)
    {
        $filePath = $file->store('bulk-imports', 'public');
        
        $bulkImport = ProductBulkImport::create([
            'vendor_id' => $vendorId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'status' => 'processing',
        ]);

        return $this->processImport($bulkImport);
    }

    protected function processImport(ProductBulkImport $bulkImport)
    {
        try {
            $data = $this->parseFile($bulkImport->file_path);
            $successCount = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                try {
                    $this->importProductRow($row, $bulkImport->vendor_id);
                    $successCount++;
                } catch (Exception $e) {
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            $bulkImport->update([
                'status' => 'completed',
                'total_rows' => count($data),
                'successful_rows' => $successCount,
                'failed_rows' => count($data) - $successCount,
                'error_report' => !empty($errors) ? implode("\n", $errors) : null,
            ]);

            return $bulkImport;
        } catch (Exception $e) {
            $bulkImport->update([
                'status' => 'failed',
                'error_report' => $e->getMessage(),
            ]);

            return $bulkImport;
        }
    }

    protected function parseFile($filePath)
    {
        $handle = fopen(Storage::disk('public')->path($filePath), 'rb');
        if ($handle === false) {
            throw new Exception('Unable to read the uploaded CSV file.');
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw new Exception('The CSV file is empty.');
        }

        $headers = array_map(function ($header) {
            $header = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header);
            return strtolower(trim(str_replace([' ', '-'], '_', $header)));
        }, $headers);

        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $row = array_pad($row, count($headers), null);
            $data[] = array_combine($headers, array_slice($row, 0, count($headers)));
        }

        fclose($handle);
        return $data;
    }

    protected function importProductRow($row, $vendorId)
    {
        return DB::transaction(function () use ($row, $vendorId) {
            // Validate required fields
            if (empty(trim((string) ($row['name'] ?? ''))) || !is_numeric($row['base_price'] ?? null)) {
                throw new Exception('Name and a numeric base_price are required.');
            }

            $category = Category::whereKey($row['category_id'] ?? null)
                ->where('is_active', true)
                ->where(function ($query) use ($vendorId) {
                    $query->where('vendor_id', $vendorId)->orWhereNull('vendor_id');
                })
                ->first();

            if (!$category) {
                throw new Exception('The category_id is not an active category available to this vendor.');
            }

            $variants = $this->decodeJson($row['variants_json'] ?? null, 'variants_json');
            $specifications = $this->decodeJson($row['specifications_json'] ?? null, 'specifications_json');
            foreach ([
                'Dimensions' => $row['dimensions'] ?? null,
                'Weight' => $row['weight'] ?? null,
                'Material' => $row['material'] ?? null,
                'Brand' => $row['brand'] ?? null,
                'Color options' => $row['color_options'] ?? null,
            ] as $key => $value) {
                if (filled($value) && !array_key_exists($key, $specifications)) {
                    $specifications[$key] = $value;
                }
            }

            // Create product
            $product = Product::create([
                'vendor_id' => $vendorId,
                'category_id' => $category->id,
                'name' => $row['name'],
                'slug' => Product::generateSlug($row['name']),
                'description' => $row['description'] ?? '',
                'base_price' => $row['base_price'],
                'sku' => $row['sku'] ?? \Str::random(10),
                'warranty' => $row['warranty'] ?? null,
                'delivery_time_days' => $row['delivery_time_days'] ?? 3,
                'status' => in_array($row['status'] ?? 'draft', ['draft', 'published'], true) ? $row['status'] : 'draft',
                'moderation_status' => $row['moderation_status'] ?? 'pending',
                'is_visible' => filter_var($row['is_visible'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            ]);

            foreach ($specifications as $key => $value) {
                if (is_string($key) && filled($value)) {
                    ProductSpecification::create([
                        'product_id' => $product->id,
                        'spec_key' => $key,
                        'spec_value' => (string) $value,
                    ]);
                }
            }

            $variantRows = [];
            foreach ($variants as $variant) {
                if (!is_array($variant) || empty($variant['variant_value'])) {
                    continue;
                }

                $createdVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $variant['variant_name'] ?? 'Option',
                    'variant_value' => $variant['variant_value'],
                    'price' => $variant['price'] ?? $product->base_price,
                    'stock' => max(0, (int) ($variant['stock'] ?? 0)),
                    'sku' => $variant['sku'] ?? \Str::random(10),
                ]);

                ProductInventory::create([
                    'product_id' => $product->id,
                    'variant_id' => $createdVariant->id,
                    'quantity' => max(0, (int) ($variant['stock'] ?? 0)),
                    'reserved_quantity' => 0,
                    'low_stock_threshold' => 5,
                ]);
                $variantRows[] = $createdVariant;
            }

            if (!$variantRows) {
                ProductInventory::create([
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'quantity' => max(0, (int) ($row['stock'] ?? 0)),
                    'reserved_quantity' => 0,
                    'low_stock_threshold' => 5,
                ]);
            }

            $imagePaths = array_filter(array_map('trim', explode('|', (string) ($row['image_paths'] ?? ''))));
            foreach ($imagePaths as $index => $imagePath) {
                if (Storage::disk('public')->exists($imagePath)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                        'is_main' => $index === 0,
                        'display_order' => $index,
                    ]);
                }
            }

            return $product;
        });
    }

    protected function decodeJson(?string $value, string $field): array
    {
        if (blank($value)) {
            return [];
        }

        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            throw new Exception("The {$field} column must contain valid JSON.");
        }

        return $decoded;
    }
}