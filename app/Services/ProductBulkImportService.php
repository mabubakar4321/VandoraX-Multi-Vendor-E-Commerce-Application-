<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBulkImport;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
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
        // Parse CSV or Excel file
        // Implementation depends on file format
        $file = \Storage::disk('public')->get($filePath);
        $rows = array_map('str_getcsv', explode("\n", $file));
        $headers = array_shift($rows);

        $data = [];
        foreach ($rows as $row) {
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }

        return $data;
    }

    protected function importProductRow($row, $vendorId)
    {
        return DB::transaction(function () use ($row, $vendorId) {
            // Validate required fields
            if (empty($row['name']) || empty($row['base_price'])) {
                throw new Exception('Missing required fields');
            }

            // Create product
            $product = Product::create([
                'vendor_id' => $vendorId,
                'category_id' => $row['category_id'] ?? 1,
                'name' => $row['name'],
                'slug' => Product::generateSlug($row['name']),
                'description' => $row['description'] ?? '',
                'base_price' => $row['base_price'],
                'sku' => $row['sku'] ?? \Str::random(10),
                'warranty' => $row['warranty'] ?? null,
                'delivery_time_days' => $row['delivery_time_days'] ?? 3,
                'status' => 'draft',
            ]);

            return $product;
        });
    }
}