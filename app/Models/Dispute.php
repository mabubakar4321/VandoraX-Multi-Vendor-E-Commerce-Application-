<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
     use HasFactory;

    protected $fillable = [
        'dispute_number',
        'order_id',
        'customer_id',
        'vendor_id',
        'dispute_type',
        'customer_claim',
        'vendor_response',
        'admin_decision',
        'admin_notes',
        'admin_decided_by',
        'decided_at',
        'refund_amount',
        'status',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'decided_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'admin_decided_by');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    // Methods
    public function resolve($decision, $refundAmount = null, $notes = null)
    {
        $this->update([
            'admin_decision' => $decision,
            'refund_amount' => $refundAmount,
            'admin_notes' => $notes,
            'admin_decided_by' => auth()->id(),
            'decided_at' => now(),
            'status' => 'resolved',
        ]);
    }
}
