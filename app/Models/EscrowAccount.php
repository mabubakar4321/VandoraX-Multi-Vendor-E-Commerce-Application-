<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscrowAccount extends Model
{
    protected $fillable = [
        'payment_id',
        'amount',
        'status',
        'description',
        'held_at',
        'released_at',
        'refunded_at',
        'refunded_amount',
        'remaining_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'held_at' => 'datetime',
        'released_at' => 'datetime',
        'refunded_at' => 'datetime',
        'refunded_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopeHeld($q) { return $q->where('status', 'held'); }
    public function scopeReleased($q) { return $q->where('status', 'released'); }

    public function release(): void
    {
        $this->update(['status' => 'released', 'released_at' => now()]);
        $this->payment?->update(['escrow_status' => 'released', 'released_at' => now()]);
    }

    public function refund(string $reason = ''): void
    {
        $this->update(['status' => 'refunded', 'refunded_at' => now(), 'description' => $reason ?: $this->description]);
        $this->payment?->update(['escrow_status' => 'refunded', 'refunded_at' => now(), 'refund_reason' => $reason]);
    }
}
