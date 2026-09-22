<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DocumentUploadService
{
    protected $disk = 'private'; // Use private disk for security
    protected $maxFileSize = 5120; // 5MB
    protected $allowedMimes = ['jpeg', 'jpg', 'png', 'pdf'];

    /**
     * Upload document securely
     */
    public function uploadDocument(UploadedFile $file, $vendorId, $documentType)
    {
        // Validate file
        $validation = $this->validateFile($file);
        if (!$validation['success']) {
            return $validation;
        }

        try {
            // Create unique filename
            $filename = $this->generateFilename($vendorId, $documentType, $file);

            // Store file in private directory
            $path = Storage::disk('private')->putFileAs(
                "vendor-documents/{$vendorId}",
                $file,
                $filename
            );

            return [
                'success' => true,
                'message' => 'Document uploaded successfully',
                'path' => $path,
                'filename' => $filename,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error uploading document: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Validate file
     */
    protected function validateFile(UploadedFile $file)
    {
        // Check mime type
        if (!in_array($file->getClientOriginalExtension(), $this->allowedMimes)) {
            return [
                'success' => false,
                'message' => 'Invalid file type. Only JPG, PNG, and PDF allowed.',
            ];
        }

        // Check file size
        if ($file->getSize() > $this->maxFileSize * 1024) {
            return [
                'success' => false,
                'message' => 'File size must not exceed ' . $this->maxFileSize . 'MB',
            ];
        }

        return ['success' => true];
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename($vendorId, $documentType, UploadedFile $file)
    {
        $timestamp = now()->format('YmdHis');
        $extension = $file->getClientOriginalExtension();
        
        return "{$documentType}_{$timestamp}.{$extension}";
    }

    /**
     * Get document URL (secure, time-limited)
     */
    public function getDocumentUrl($path, $expiresIn = 60)
    {
        return Storage::disk('private')->temporaryUrl(
            $path,
            now()->addMinutes($expiresIn)
        );
    }

    /**
     * Delete document
     */
    public function deleteDocument($path)
    {
        try {
            if (Storage::disk('private')->exists($path)) {
                Storage::disk('private')->delete($path);
                return [
                    'success' => true,
                    'message' => 'Document deleted',
                ];
            }

            return [
                'success' => false,
                'message' => 'Document not found',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error deleting document: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Scan file for virus (optional - requires ClamAV)
     */
    public function scanForVirus(UploadedFile $file)
    {
        // TODO: Implement if needed
        // This requires ClamAV antivirus to be installed on server
        return ['clean' => true];
    }
}