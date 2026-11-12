<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ReviewImage::class)->orderBy('display_order');
    }

    public function response(): HasOne
    {
        return $this->hasOne(ReviewResponse::class);
    }

    public function helpfulVotes(): HasMany
    {
        return $this->hasMany(ReviewHelpfulVote::class);
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

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePositive($query)
    {
        return $query->where('product_rating', '>=', 4);
    }

    public function scopeNegative($query)
    {
        return $query->where('product_rating', '<=', 2);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForVendor($query, int $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
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

    public function isBadReview(): bool
    {
        return $this->vendor_rating <= 2;
    }

    public function hasResponded(): bool
    {
        return $this->response()->exists();
    }

    public function getUserVote(int $userId): ?string
    {
        $vote = $this->helpfulVotes()->where('user_id', $userId)->first();
        return $vote?->vote_type;
    }
}