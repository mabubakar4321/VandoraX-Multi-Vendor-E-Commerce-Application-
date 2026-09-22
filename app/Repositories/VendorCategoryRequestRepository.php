<?php

namespace App\Repositories;

use App\Models\VendorCategoryRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class VendorCategoryRequestRepository extends BaseRepository
{
    public function __construct(VendorCategoryRequest $model)
    {
        parent::__construct($model);
    }

    public function getPendingPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->pending()
            ->with(['vendor', 'vendor.user'])
            ->orderBy('requested_at', 'desc')
            ->paginate($perPage);
    }

    public function getVendorRequests(int $vendorId): Collection
    {
        return $this->model
            ->where('vendor_id', $vendorId)
            ->orderBy('requested_at', 'desc')
            ->get();
    }

    public function getVendorPendingRequests(int $vendorId): Collection
    {
        return $this->model
            ->where('vendor_id', $vendorId)
            ->pending()
            ->get();
    }

    public function hasPendingRequest(int $vendorId, string $categoryName): bool
    {
        return $this->model
            ->where('vendor_id', $vendorId)
            ->where('category_name', $categoryName)
            ->pending()
            ->exists();
    }

    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'pending' => $this->model->pending()->count(),
            'approved' => $this->model->approved()->count(),
            'rejected' => $this->model->rejected()->count(),
        ];
    }

    public function getByStatusPaginated(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('status', $status)
            ->with(['vendor', 'vendor.user', 'approvedBy'])
            ->orderBy('requested_at', 'desc')
            ->paginate($perPage);
    }

    public function search(string $query): Collection
    {
        return $this->model
            ->where('category_name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhereHas('vendor.user', fn ($q) => 
                $q->where('email', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%")
            )
            ->with(['vendor', 'vendor.user'])
            ->orderBy('requested_at', 'desc')
            ->get();
    }
}