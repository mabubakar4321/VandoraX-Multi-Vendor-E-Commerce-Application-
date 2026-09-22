<?php

namespace App\Repositories;

use App\Models\VendorBankDetails;
use Illuminate\Support\Facades\Crypt;

class VendorBankDetailsRepository extends BaseRepository
{
    public function __construct(VendorBankDetails $model)
    {
        parent::__construct($model);
    }

    // Get bank details by vendor ID
    public function getByVendorId($vendorId)
    {
        return $this->model->where('vendor_id', $vendorId)->first();
    }

    // Check if IBAN already exists (prevent duplicates)
    public function ibanExists($iban, $excludeVendorId = null)
    {
        $query = $this->model->where('iban', $iban);
        if ($excludeVendorId) {
            $query->where('vendor_id', '!=', $excludeVendorId);
        }
        return $query->exists();
    }

    // Check if account number exists
    public function accountExists($accountNumber, $excludeVendorId = null)
    {
        $query = $this->model->where('account_number', $accountNumber);
        if ($excludeVendorId) {
            $query->where('vendor_id', '!=', $excludeVendorId);
        }
        return $query->exists();
    }

    // Verify bank account
    public function verifyAccount($vendorId)
    {
        return $this->update($vendorId, [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    // Make account primary
    public function makePrimary($vendorId)
    {
        // Unset all other as primary first
        $this->model->where('vendor_id', $vendorId)->update(['is_primary' => false]);
        
        // Set this one as primary
        return $this->update($vendorId, ['is_primary' => true]);
    }

    // Encrypt sensitive data
    public function createEncrypted(array $data)
    {
        // VendorBankDetails encrypts these fields through its model casts.
        return $this->create($data);
    }

    // Decrypt when retrieving
    public function getDecrypted($vendorId)
    {
        $record = $this->getByVendorId($vendorId);
        
        if ($record) {
            /** @var VendorBankDetails $record */
            return [
                'account_number' => $this->decryptValue($record->getRawOriginal('account_number')),
                'iban' => $this->decryptValue($record->getRawOriginal('iban')),
                'account_holder_name' => $record->account_holder_name,
                'bank_name' => $record->bank_name,
                'account_type' => $record->account_type,
            ];
        }
        
        return null;
    }

    /**
     * Read records created before the model encryption casts were added.
     */
    protected function decryptValue(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $decoded = $value;

        // Support both current model casts and older values encrypted twice.
        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $next = Crypt::decryptString($decoded);
            } catch (\Throwable) {
                break;
            }

            if (is_string($next) && preg_match('/^s:\d+:/', $next)) {
                $unserialized = @unserialize($next);
                if (is_string($unserialized)) {
                    $next = $unserialized;
                }
            }

            if (! is_string($next) || $next === $decoded) {
                break;
            }

            $decoded = $next;
        }

        return $decoded;
    }
}