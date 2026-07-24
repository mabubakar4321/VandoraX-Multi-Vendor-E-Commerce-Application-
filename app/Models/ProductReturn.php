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
    ];

    protected $casts = [
        'vendor_approved_at' => 'datetime',
        'returned_at' => 'datetime',
        'received_at' => 'datetime',
        'refund_approved_at' => 'datetime',
        'refund_processed_at' => 'datetime',
        'refund_amount' => 'decimal:2',
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
}
