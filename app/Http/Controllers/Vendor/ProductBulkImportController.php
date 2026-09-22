<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkImportProductRequest;
use App\Services\ProductBulkImportService;
use Illuminate\Support\Facades\Auth;
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
        $headers = ['Name', 'Description', 'Category ID', 'Base Price', 'Stock', 'SKU', 'Warranty', 'Delivery Days'];
        $csv = implode(',', $headers) . "\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product-template.csv"',
        ]);
    }
}