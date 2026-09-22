<?php

namespace App\Repositories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class VendorRepository extends BaseRepository
{
    public function __construct(Vendor $model)
    {
        parent::__construct($model);
    }

    /**
     * Find vendor by user ID
     */
    public function findByUserId($userId): ?Vendor
    {
        return $this->query()
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Find vendor by store slug
     */
    public function findBySlug(string $slug): ?Vendor
    {
        return $this->query()
            ->where('store_slug', $slug)
            ->first();
    }

    /**
     * Get pending vendors (awaiting approval)
     */
    public function getPendingVendors(
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->query()
            ->with('user')
            ->where('status', 'pending')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get active vendors
     */
    public function getActiveVendors(): Collection
    {
        return $this->query()
            ->where('status', 'active')
            ->get();
    }

    /**
     * Get blocked vendors.
     */
    public function getBlockedVendors(): Collection
    {
        return $this->query()
            ->where('status', 'blocked')
            ->get();
    }

    /**
     * Approve vendor
     */
    public function approveVendor($id): ?Vendor
    {
        return $this->update($id, [
            'status' => 'active',
            'is_verified' => true,
            'verification_date' => now(),
        ]);
    }

    /**
     * Reject vendor
     */
    public function rejectVendor(
        $id,
        string $reason
    ): ?Vendor {
        return $this->update($id, [
            'status' => 'rejected',
            'is_verified' => false,
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Check if store name exists
     */
    public function storeNameExists(
        string $name,
        $excludeId = null
    ): bool {
        return $this->query()
            ->where('store_name', $name)
            ->when(
                $excludeId,
                fn ($q) => $q->where('id', '!=', $excludeId)
            )
            ->exists();
    }

    /**
     * Check if slug exists
     */
    public function slugExists(string $slug): bool
    {
        return $this->query()
            ->where('store_slug', $slug)
            ->exists();
    }

    /**
     * Generate unique slug for store
     * 
     * Example: "tech-hub" → "tech-hub" (if available)
     *          "tech-hub" → "tech-hub-2" (if taken)
     *          "tech-hub" → "tech-hub-3" (if taken)
     */
    public function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $i = 2;

        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Get vendor with all relationships (for admin detail view)
     */
    public function getWithRelations($id): ?Vendor
    {
        return $this->query()
            ->with([
                'user',
                'category',
                'documents',
                'bankDetails',
                'statistics',
            ])
            ->find($id);
    }

    /**
     * Count vendors by status
     */
    public function countByStatus(): Collection
    {
        return $this->query()
            ->groupBy('status')
            ->selectRaw('status, count(*) as total')
            ->get();
    }

    /**
     * Count pending vendors
     */
    public function countPending(): int
    {
        return $this->query()
            ->where('status', 'pending')
            ->count();
    }
}