<?php

namespace App\Repositories;

use App\Models\VendorDocument;

class VendorDocumentRepository extends BaseRepository
{
    public function __construct(VendorDocument $model)
    {
        parent::__construct($model);
    }

    // Get documents by vendor ID
    public function getByVendorId($vendorId)
    {
        return $this->model->where('vendor_id', $vendorId)->get();
    }

    // Get document by type
    public function getByType($vendorId, $type)
    {
        return $this->model
            ->where('vendor_id', $vendorId)
            ->where('document_type', $type)
            ->first();
    }

    // Get pending documents
    public function getPendingDocuments()
    {
        return $this->model->where('status', 'pending')->get();
    }

    // Approve document
    public function approveDocument($id)
    {
        return $this->update($id, [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    // Reject document
    public function rejectDocument($id, $reason = null)
    {
        return $this->update($id, [
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => now(),
        ]);
    }

    // Check if vendor has all required documents
    public function hasAllRequiredDocuments($vendorId)
    {
        $required = ['cnic', 'tax_certificate', 'bank_proof'];
        
        foreach ($required as $type) {
            $doc = $this->getByType($vendorId, $type);
            if (!$doc || $doc->status !== 'approved') {
                return false;
            }
        }
        
        return true;
    }

    // Get document approval status
    public function getDocumentStatus($vendorId)
    {
        $documents = $this->getByVendorId($vendorId);
        
        $status = [
            'total' => count($documents),
            'approved' => $documents->where('status', 'approved')->count(),
            'pending' => $documents->where('status', 'pending')->count(),
            'rejected' => $documents->where('status', 'rejected')->count(),
        ];
        
        return $status;
    }
}