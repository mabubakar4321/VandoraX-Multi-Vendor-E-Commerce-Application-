<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'store_description',
        'logo_url',
        'banner_url',
        'category_id',
        'commission_rate',
        'total_sales',
        'total_orders',
        'average_rating',
        'total_followers',
        'status',
        'is_verified',
        'verification_date',
        'bad_reviews_count',
        'requested_unblock',
        'unblock_message',
        'joined_at',
    ];

    protected $casts = [
        'verification_date' => 'datetime',
        'joined_at' => 'datetime',
        'is_verified' => 'boolean',
        'commission_rate' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'average_rating' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function documents()
    {
        return $this->hasMany(VendorDocument::class);
    }

    public function bankDetails()
    {
        return $this->hasOne(VendorBankDetails::class);
    }

    public function statistics()
    {
        return $this->hasOne(VendorStatistics::class);
    }

    public function orders()
    {
        return $this->hasMany(VendorOrder::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function payouts()
    {
        return $this->hasMany(VendorPayout::class);
    }

    public function badReviews()
    {
        return $this->hasMany(VendorBadReview::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Methods
    public function isBlocked()
    {
        return $this->status === 'blocked';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function shouldAutoBlock()
    {
        $threshold = config('vandorax.bad_review_threshold');
        return $this->bad_reviews_count >= $threshold;
    }

    public function blockVendor($reason, $byUserId = null)
    {
        $this->update(['status' => 'blocked']);
        VendorBlock::create([
            'vendor_id' => $this->id,
            'block_type' => 'auto_bad_reviews',
            'reason' => $reason,
            'bad_review_count' => $this->bad_reviews_count,
            'blocked_by' => $byUserId,
        ]);
    }
}