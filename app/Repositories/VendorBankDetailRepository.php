<?php

namespace App\Repositories;

use App\Models\VendorBankDetails;
use Illuminate\Database\Eloquent\Collection;

class VendorBankDetailRepository extends BaseRepository
{
    public function __construct(VendorBankDetails $model)
    {
        parent::__construct($model);
    }

    public function findByVendorId($vendorId): Collection
    {
        return $this->query()
            ->where('vendor_id', $vendorId)
            ->get();
    }

    public function getPrimaryForVendor(
        $vendorId
    ): ?VendorBankDetails {
        return $this->query()
            ->where('vendor_id', $vendorId)
            ->primary()
            ->first();
    }

    public function updateOrCreateForVendor(
        $vendorId,
        array $data
    ): VendorBankDetails {
        return $this->model->updateOrCreate(
            [
                'vendor_id' => $vendorId,
                'is_primary' => true,
            ],
            $data
        );
    }

    public function markVerified(
        $id
    ): ?VendorBankDetails {
        return $this->update($id, [
            'is_verified' => true,
            'verification_date' => now(),
        ]);
    }
}