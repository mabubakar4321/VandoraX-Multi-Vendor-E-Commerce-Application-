<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VendorApprovalService;
use App\Repositories\VendorRepository;
use App\Repositories\VendorDocumentRepository;
use App\Repositories\VendorBankDetailsRepository;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorApprovalController extends Controller
{
    protected $approvalService;
    protected $vendorRepository;
    protected $documentRepository;
    protected $bankDetailsRepository;

    public function __construct(
        VendorApprovalService $approvalService,
        VendorRepository $vendorRepository,
        VendorDocumentRepository $documentRepository,
        VendorBankDetailsRepository $bankDetailsRepository
    ) {
        $this->approvalService = $approvalService;
        $this->vendorRepository = $vendorRepository;
        $this->documentRepository = $documentRepository;
        $this->bankDetailsRepository = $bankDetailsRepository;
    }

    /**
     * Show pending vendors list
     */
    public function pendingVendors(Request $request)
    {
        $vendors = $this->vendorRepository->getPendingVendors();

        return view('admin.vendors.pending', [
            'vendors' => $vendors,
        ]);
    }

    /**
     * Show vendor details
     */
    public function showVendor($vendorId)
    {
        $vendor = $this->vendorRepository->getWithRelations($vendorId);

        if (!$vendor) {
            abort(404, 'Vendor not found');
        }

        $documents = $this->documentRepository->getByVendorId($vendorId);
        $documentStatus = $this->documentRepository->getDocumentStatus($vendorId);
        $bankDetails = $this->bankDetailsRepository->getDecrypted($vendorId);

        return view('admin.vendors.detail', compact('vendor', 'documents', 'documentStatus', 'bankDetails'));
    }

    /**
     * Approve vendor
     */
    public function approve(Request $request, $vendorId)
    {
        abort_unless(auth()->user()?->can('manage_vendors'), 403);

        $result = $this->approvalService->approveVendor(
            $vendorId,
            $request->input('notes')
        );

        if (!$result['success']) {
            return back()->withErrors(['message' => $result['message']]);
        }

        return back()->with('success', 'Vendor approved successfully!');
    }

    /**
     * Reject vendor
     */
    public function reject(Request $request, $vendorId)
    {
        abort_unless(auth()->user()?->can('manage_vendors'), 403);

        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        $result = $this->approvalService->rejectVendor($vendorId, $request->reason);

        if (!$result['success']) {
            return back()->withErrors(['message' => $result['message']]);
        }

        return back()->with('success', 'Vendor rejected.');
    }

    /**
     * Approve document
     */
    public function approveDocument(Request $request, $documentId)
    {
        abort_unless(auth()->user()?->can('manage_vendors'), 403);

        $result = $this->approvalService->approveDocument($documentId);

        if (!$result['success']) {
            return response()->json(['error' => $result['message']], 400);
        }

        if (! $request->expectsJson()) {
            return back()->with('success', $result['message']);
        }

        return response()->json(['success' => true, 'message' => $result['message']]);
    }

    /**
     * Reject document
     */
    public function rejectDocument(Request $request, $documentId)
    {
        abort_unless(auth()->user()?->can('manage_vendors'), 403);

        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $result = $this->approvalService->rejectDocument($documentId, $request->reason);

        if (!$result['success']) {
            return response()->json(['error' => $result['message']], 400);
        }

        if (! $request->expectsJson()) {
            return back()->with('success', $result['message']);
        }

        return response()->json(['success' => true, 'message' => $result['message']]);
    }

    /**
     * View vendor document
     */
    public function viewDocument($documentId)
    {
        abort_unless(auth()->user()?->can('manage_vendors'), 403);

        $document = \App\Models\VendorDocument::findOrFail($documentId);
        $disk = Storage::disk('private');

        abort_unless($disk->exists($document->file_path), 404);

        return response()->file($disk->path($document->file_path));
    }

    /**
     * Get vendors statistics
     */
    public function statistics()
    {
        $stats = [
            'pending' => $this->vendorRepository->countPending(),
            'active' => $this->vendorRepository->getActiveVendors()->count(),
            'blocked' => $this->vendorRepository->getBlockedVendors()->count(),
            'total' => Vendor::count(),
        ];

        return response()->json($stats);
    }
}