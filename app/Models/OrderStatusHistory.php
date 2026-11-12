<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'order_status_history';


    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'notes',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public static function record(
        Order $order,
        string $newStatus,
        ?string $notes = null,
        ?User $changedBy = null,
        string $oldStatus = null
    ): self {
        return static::create([
            'order_id'   => $order->id,
            'old_status' => $oldStatus ?? $order->status,
            'new_status' => $newStatus,
            'notes'      => $notes,
            'changed_by' => $changedBy?->id ?? auth()->id(),
            'changed_at' => now(),
        ]);
    }

    public function scopeForOrder($query, Order|int $order)
    {
        $orderId = $order instanceof Order ? $order->id : $order;
        return $query->where('order_id', $orderId)->orderBy('changed_at');
    }
}
