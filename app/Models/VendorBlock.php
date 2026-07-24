<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'block_type',
        'reason',
        'bad_review_count',
        'blocked_by',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function blockedBy()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    // Scopes
    public function scopeAutoBlocked($query)
    {
        return $query->where('block_type', 'auto_bad_reviews');
    }

    public function scopeManualBlocked($query)
    {
        return $query->where('block_type', 'manual');
    }

    public function scopeSuspension($query)
    {
        return $query->where('block_type', 'suspension');
    }

    // Methods
    public function isAutoBlocked()
    {
        return $this->block_type === 'auto_bad_reviews';
    }

    public function isManualBlock()
    {
        return $this->block_type === 'manual';
    }
}
