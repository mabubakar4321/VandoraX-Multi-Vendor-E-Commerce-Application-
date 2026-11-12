<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkImportProductRequest;
use App\Services\ProductBulkImportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductBulkImportController extends Controller
{
    protected $bulkImportService;

    public function __construct(ProductBulkImportService $bulkImportService)
    {
        $this->bulkImportService = $bulkImportService;
    }

    public function create()
    {
        return view('vendor.products.bulk-import');
    }

    public function store(BulkImportProductRequest $request)
    {
        try {
            $vendor = Auth::user()->vendor;
            $bulkImport = $this->bulkImportService->importFromFile($request->file('file'), $vendor->id);

            return redirect()->route('vendor.products.index')
                ->with('success', "Import completed! {$bulkImport->successful_rows} products imported.");
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function template()
    {
        $vendor = Auth::user()->vendor;
        abort_unless($vendor, 403, 'Vendor account not found.');

        $headers = [
            'name', 'description', 'category_id', 'base_price', 'stock', 'sku',
            'warranty', 'delivery_time_days', 'status', 'moderation_status',
            'is_visible', 'dimensions', 'weight', 'material', 'brand',
            'color_options', 'specifications_json', 'variants_json', 'image_paths',
        ];

        $products = \App\Models\Product::where('vendor_id', $vendor->id)
            ->with(['specifications', 'variants', 'images', 'inventory'])
            ->orderBy('id')
            ->get();

        $stream = fopen('php://temp', 'w+');
        fputcsv($stream, $headers);

        foreach ($products as $product) {
            $specifications = $product->specifications
                ->mapWithKeys(fn ($specification) => [$specification->spec_key => $specification->spec_value])
                ->toArray();

            $variants = $product->variants->map(function ($variant) {
                return [
                    'variant_name' => $variant->variant_name,
                    'variant_value' => $variant->variant_value,
                    'price' => $variant->price,
                    'stock' => $variant->stock,
                    'sku' => $variant->sku,
                ];
            })->values()->toArray();

            $baseInventory = $product->inventory->whereNull('variant_id')->first();
            $details = collect($specifications);

            fputcsv($stream, [
                $product->name,
                $product->description,
                $product->category_id,
                $product->base_price,
                $baseInventory?->quantity ?? 0,
                $product->sku,
                $product->warranty,
                $product->delivery_time_days,
                $product->status,
                $product->moderation_status,
                $product->is_visible ? 1 : 0,
                $details->get('Dimensions'),
                $details->get('Weight'),
                $details->get('Material'),
                $details->get('Brand'),
                $details->get('Color options'),
                json_encode($specifications, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                json_encode($variants, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                $product->images->pluck('image_path')->implode('|'),
            ]);
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vendor-products.csv"',
        ]);
    }
}