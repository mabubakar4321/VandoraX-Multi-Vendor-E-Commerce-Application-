<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorBadReview extends Model
{
     use HasFactory;
protected $fillable = [
        'vendor_id',
        'review_id',
        'review_rating',
        'flagged_at',
    ];

    protected $casts = [
        'flagged_at' => 'datetime',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    // Scopes
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('flagged_at', '>=', now()->subDays($days));
    }

    // Methods
    public static function checkAndBlockVendor($vendorId)
    {
        $badReviewCount = static::where('vendor_id', $vendorId)->count();
        $threshold = config('vandorax.bad_review_threshold', 10);

        if ($badReviewCount >= $threshold) {
            $vendor = Vendor::find($vendorId);
            if ($vendor && !$vendor->isBlocked()) {
                $vendor->blockVendor('Auto-blocked: Reached ' . $badReviewCount . ' bad reviews');
            }
            return true;
        }
        return false;
    }
}
