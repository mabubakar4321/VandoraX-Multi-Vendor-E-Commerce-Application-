<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorUnblockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'improvements',
        'reason_to_unblock',
        'evidence_files',
        'reason',
        'admin_response',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'evidence_files' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ]);

        $vendor = $this->vendor;
        $vendor->update(['status' => 'active', 'bad_reviews_count' => 0, 'requested_unblock' => false, 'unblock_message' => null]);

        VendorBadReview::where('vendor_id', $vendor->id)->delete();
    }

    public function reject(int $userId, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'admin_response' => $reason,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}