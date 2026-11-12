<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $fillable = [
        'return_id',
        'order_item_id',
        'quantity',
        'item_price',
        'refund_amount',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'item_price' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
