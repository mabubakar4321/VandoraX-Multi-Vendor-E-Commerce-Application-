<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundRecord extends Model
{
    protected $fillable = [
        'return_id', 'refund_amount', 'status', 'transaction_id', 'notes',
        'refund_source', 'failure_reason', 'processed_by', 'processed_at',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
