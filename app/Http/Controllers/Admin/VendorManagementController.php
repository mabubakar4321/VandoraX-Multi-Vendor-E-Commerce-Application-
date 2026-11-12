<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\VendorDocument;
use App\Services\DocumentUploadService;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VendorManagementController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with(['user', 'category'])
            ->withCount(['products', 'orders'])
            ->latest()
            ->paginate(20);

        $counts = Vendor::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.vendors.index', compact('vendors', 'counts'));
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['user', 'category', 'products', 'orders.order.customer', 'orders.order.payment']);
        $bankDetails = app(\App\Repositories\VendorBankDetailsRepository::class)->getDecrypted($vendor->id);
        $documents = app(\App\Repositories\VendorDocumentRepository::class)->getByVendorId($vendor->id);
        $categories = Category::active()->ordered()->get();

        return view('admin.vendors.manage', compact('vendor', 'bankDetails', 'documents', 'categories'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($vendor->user_id)],
            'phone_number' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone_number')->ignore($vendor->user_id)],
            'password' => 'nullable|string|min:8|confirmed',
            'store_name' => ['required', 'string', 'max:255', Rule::unique('vendors', 'store_name')->ignore($vendor->id)],
            'store_slug' => ['required', 'string', 'max:255', Rule::unique('vendors', 'store_slug')->ignore($vendor->id)],
            'store_description' => 'nullable|string|max:5000',
            'category_id' => 'nullable|exists:categories,id',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:pending,active,blocked,suspended',
            'is_verified' => 'nullable|boolean',
            'email_verified' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($vendor, $validated, $request) {
            $user = $vendor->user;
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone_number = $validated['phone_number'] ?? null;
            $user->is_active = $request->boolean('is_active');
            $user->email_verified_at = $request->boolean('email_verified') ? ($user->email_verified_at ?? now()) : null;
            if (!empty($validated['password'])) {
                $user->password = $validated['password'];
            }
            $user->save();

            $vendor->update([
                'store_name' => $validated['store_name'],
                'store_slug' => $validated['store_slug'],
                'store_description' => $validated['store_description'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'commission_rate' => $validated['commission_rate'],
                'status' => $validated['status'],
                'is_verified' => $request->boolean('is_verified'),
                'verification_date' => $request->boolean('is_verified') ? ($vendor->verification_date ?? now()) : null,
            ]);
        });

        return redirect()->route('admin.vendors.manage', $vendor)->with('success', 'Vendor information updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        DB::transaction(function () use ($vendor) {
            $user = $vendor->user;
            $vendor->delete();
            $user?->delete();
        });

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor and associated account deleted successfully.');
    }

    public function uploadDocument(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'document_type' => 'required|in:cnic_front,cnic_back,tax_certificate,bank_proof,store_photo',
            'document' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ]);

        $this->storeDocument($vendor, $request, $validated['document_type']);

        return back()->with('success', 'Vendor document uploaded and marked pending review.');
    }

    public function replaceDocument(Request $request, Vendor $vendor, VendorDocument $document)
    {
        abort_unless($document->vendor_id === $vendor->id, 404);

        $validated = $request->validate([
            'document_type' => 'required|in:cnic_front,cnic_back,tax_certificate,bank_proof,store_photo',
            'document' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ]);

        $this->deleteStoredFile($document->file_path);
        $this->storeDocument($vendor, $request, $validated['document_type'], $document);

        return back()->with('success', 'Vendor document replaced and marked pending review.');
    }

    public function deleteDocument(Vendor $vendor, VendorDocument $document)
    {
        abort_unless($document->vendor_id === $vendor->id, 404);

        $this->deleteStoredFile($document->file_path);
        $document->delete();

        return back()->with('success', 'Vendor document deleted successfully.');
    }

    private function storeDocument(Vendor $vendor, Request $request, string $documentType, ?VendorDocument $document = null): void
    {
        $file = $request->file('document');
        $result = app(DocumentUploadService::class)->uploadDocument($file, $vendor->id, $documentType);

        if (!$result['success']) {
            abort(422, $result['message']);
        }

        $data = [
            'vendor_id' => $vendor->id,
            'document_type' => $documentType,
            'file_path' => $result['path'],
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'pending',
            'rejection_reason' => null,
            'verified_by' => null,
            'verified_at' => null,
        ];

        if ($document) {
            $document->update($data);
        } else {
            VendorDocument::create($data);
        }
    }

    private function deleteStoredFile(string $path): void
    {
        if (Storage::disk('private')->exists($path)) {
            Storage::disk('private')->delete($path);
        }
    }
}
