<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'currency',
        'payment_method',
        'payment_gateway',
        'transaction_id',
        'gateway_response',
        'status',
        'escrow_status',
        'jazzcash_account_number',
        'jazzcash_account_holder',
        'bank_account_holder',
        'bank_account_number',
        'bank_iban',
        'bank_name',
        'bank_payer_name',
        'bank_payment_app',
        'bank_payment_source',
        'bank_customer_account_number',
        'payment_slip_path',
        'payment_slip_original_name',
        'payment_slip_mime_type',
        'payment_slip_size',
        'paid_at',
        'released_at',
        'refunded_at',
        'refund_reason',
        'refunded_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'released_at' => 'datetime',
        'refunded_at' => 'datetime',
        'refunded_amount' => 'decimal:2',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function escrowAccount()
    {
        return $this->hasOne(\App\Models\EscrowAccount::class);
    }

    public function refunds()
    {
        return $this->hasMany(PaymentRefund::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Methods
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    public function releaseFromEscrow()
    {
        $this->update([
            'escrow_status' => 'released',
            'released_at' => now(),
        ]);
    }

    public function refund($reason)
    {
        $this->update([
            'status' => 'refunded',
            'escrow_status' => 'refunded',
            'refund_reason' => $reason,
            'refunded_at' => now(),
        ]);
    }
}
