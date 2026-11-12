<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
     use HasFactory;

    protected $table = 'returns';
    protected $fillable = [
        'return_number',
        'order_item_id',
        'customer_id',
        'vendor_id',
        'reason',
        'description',
        'status',
        'return_reason_detail',
        'vendor_response',
        'vendor_approved_at',
        'return_label_url',
        'returned_at',
        'received_at',
        'refund_approved_at',
        'refund_amount',
        'refund_processed_at',
        'return_shipping_address',
        'return_instructions',
        'return_tracking_number',
        'return_tracking_url',
        'rejection_reason',
        'inspection_notes',
        'refund_source',
        'vendor_debit_amount',
        'return_deadline',
        'rejected_at',
        'shipped_back_at',
        'refund_failed_at',
        'admin_decided_by',
        'admin_decided_at',
        'admin_decision_notes',
    ];

    protected $casts = [
        'vendor_approved_at' => 'datetime',
        'returned_at' => 'datetime',
        'received_at' => 'datetime',
        'refund_approved_at' => 'datetime',
        'refund_processed_at' => 'datetime',
        'refund_amount' => 'decimal:2',
        'vendor_debit_amount' => 'decimal:2',
        'return_deadline' => 'datetime',
        'rejected_at' => 'datetime',
        'shipped_back_at' => 'datetime',
        'refund_failed_at' => 'datetime',
        'admin_decided_at' => 'datetime',
    ];

    // Relationships
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function photos()
    {
        return $this->hasMany(ReturnPhoto::class, 'return_id');
    }

    public function refundRecords()
    {
        return $this->hasMany(RefundRecord::class, 'return_id');
    }

    public function returnItems()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(ReturnStatusHistory::class, 'return_id', 'id');
    }

    public function adminDecidedBy()
    {
        return $this->belongsTo(User::class, 'admin_decided_by');
    }

    public function disputes()
    {
        return $this->hasMany(Dispute::class, 'return_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'requested');
    }

    // Methods
    public function approveReturn()
    {
        $this->update([
            'status' => 'approved',
            'vendor_approved_at' => now(),
        ]);
    }

    public function rejectReturn($reason)
    {
        $this->update([
            'status' => 'rejected',
            'vendor_response' => $reason,
        ]);
    }

    public function processRefund($amount)
    {
        $this->update([
            'status' => 'refunded',
            'refund_amount' => $amount,
            'refund_processed_at' => now(),
        ]);
    }

    public function setStatus(string $status, ?string $notes = null): void
    {
        $oldStatus = $this->status;
        $this->update(['status' => $status]);
        ReturnStatusHistory::create([
            'return_id' => $this->id,
            'old_status' => $oldStatus,
            'new_status' => $status,
            'notes' => $notes,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);
    }
}
