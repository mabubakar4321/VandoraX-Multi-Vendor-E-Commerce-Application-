<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
'rejection_reason',
'bad_reviews_count',
        'requested_unblock',
        'unblock_message',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'verification_date' => 'datetime',
            'joined_at'         => 'datetime',
            'is_verified'       => 'boolean',
            'commission_rate'   => 'decimal:2',
            'total_sales'       => 'decimal:2',
            'average_rating'   => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VendorDocument::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bankDetails(): HasOne
    {
        return $this->hasOne(VendorBankDetails::class);
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(VendorStatistics::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(VendorOrder::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(VendorPayout::class);
    }

    public function badReviews(): HasMany
    {
        return $this->hasMany(VendorBadReview::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isApproved(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /*
    |--------------------------------------------------------------------------
    | Vendor Blocking
    |--------------------------------------------------------------------------
    */

    public function shouldAutoBlock(): bool
    {
        $threshold = config('vandorax.bad_review_threshold');

        return $this->bad_reviews_count >= $threshold;
    }

    public function blockVendor($reason, $byUserId = null): void
    {
        $this->update([
            'status' => 'blocked',
        ]);

        VendorBlock::create([
            'vendor_id'        => $this->id,
            'block_type'       => 'auto_bad_reviews',
            'reason'           => $reason,
            'bad_review_count' => $this->bad_reviews_count,
            'blocked_by'       => $byUserId,
        ]);
    }
}