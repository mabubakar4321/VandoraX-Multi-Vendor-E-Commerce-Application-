<?php

namespace App\Services;

use App\Repositories\VendorRepository;
use App\Repositories\VendorDocumentRepository;
use App\Notifications\VendorApprovedNotification;
use App\Notifications\VendorRejectedNotification;

class VendorApprovalService
{
    protected $vendorRepository;
    protected $documentRepository;

    public function __construct(
        VendorRepository $vendorRepository,
        VendorDocumentRepository $documentRepository
    ) {
        $this->vendorRepository = $vendorRepository;
        $this->documentRepository = $documentRepository;
    }

    /**
     * Approve vendor
     */
    public function approveVendor($vendorId, $notes = null)
    {
        $vendor = $this->vendorRepository->getWithRelations($vendorId);

        if (!$vendor) {
            return [
                'success' => false,
                'message' => 'Vendor not found',
            ];
        }

        try {
            // Update vendor status
            $this->vendorRepository->approveVendor($vendorId);

            // Approving the application approves all documents submitted with it.
            foreach ($this->documentRepository->getByVendorId($vendorId) as $document) {
                if ($document->status === 'pending') {
                    $this->documentRepository->approveDocument($document->id);
                }
            }

            // Send approval email
            $this->sendApprovalEmail($vendor);

            return [
                'success' => true,
                'message' => 'Vendor approved successfully',
                'vendor' => $vendor->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error approving vendor: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Reject vendor
     */
    public function rejectVendor($vendorId, $reason)
    {
        $vendor = $this->vendorRepository->getWithRelations($vendorId);

        if (!$vendor) {
            return [
                'success' => false,
                'message' => 'Vendor not found',
            ];
        }

        try {
            // Update vendor status
            $this->vendorRepository->rejectVendor($vendorId, $reason);

            // Send rejection email
            $this->sendRejectionEmail($vendor, $reason);

            return [
                'success' => true,
                'message' => 'Vendor rejected',
                'vendor' => $vendor->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error rejecting vendor: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Approve vendor document
     */
    public function approveDocument($documentId)
    {
        $this->documentRepository->approveDocument($documentId);

        return [
            'success' => true,
            'message' => 'Document approved',
        ];
    }

    /**
     * Reject vendor document
     */
    public function rejectDocument($documentId, $reason)
    {
        $this->documentRepository->rejectDocument($documentId, $reason);

        return [
            'success' => true,
            'message' => 'Document rejected',
        ];
    }

    /**
     * Send approval email
     */
    private function sendApprovalEmail($vendor)
    {
        $vendor->user->notify(new VendorApprovedNotification($vendor));
    }

    /**
     * Send rejection email
     */
    private function sendRejectionEmail($vendor, $reason)
    {
        $vendor->user->notify(new VendorRejectedNotification($vendor, $reason));
    }

    /**
     * Get pending vendors count
     */
    public function getPendingCount()
    {
        return $this->vendorRepository->countPending();
    }
}