<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'payout_id',
        'amount',
        'status',
        'payout_method',
        'reference',
        'rejection_reason',
        'notes',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(VendorPayout::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
