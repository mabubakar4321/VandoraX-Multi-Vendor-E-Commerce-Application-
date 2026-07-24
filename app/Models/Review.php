<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'order_id',
        'vendor_id',
        'customer_id',
        'product_rating',
        'vendor_rating',
        'review_title',
        'review_text',
        'is_verified_purchase',
        'status',
        'is_helpful_count',
        'is_unhelpful_count',
        'rejected_reason',
    ];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePositive($query)
    {
        return $query->where('product_rating', '>=', 4);
    }

    public function scopeNegative($query)
    {
        return $query->where('product_rating', '<=', 2);
    }

    // Methods
    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    public function reject($reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_reason' => $reason,
        ]);
    }

    public function markHelpful()
    {
        $this->increment('is_helpful_count');
    }

    public function markUnhelpful()
    {
        $this->increment('is_unhelpful_count');
    }

    public function isBadReview()
    {
        return $this->vendor_rating <= 2;
    }
}
