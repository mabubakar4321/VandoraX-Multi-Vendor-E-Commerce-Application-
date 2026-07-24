<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorUnblockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'reason',
        'status',
        'admin_response',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
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

    // Methods
    public function approve($userId)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ]);

        // Unblock the vendor
        $this->vendor->update(['status' => 'active']);
    }

    public function reject($userId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'admin_response' => $reason,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ]);
    }
}
