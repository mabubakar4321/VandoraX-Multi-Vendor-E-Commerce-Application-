<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
   use HasFactory;
   
   protected $fillable = [
        'order_number',
        'customer_id',
        'total_amount',
        'commission_amount',
        'vendor_count',
        'status',
        'payment_status',
        'payment_id',
        'notes',

   ];

   protected $casts = [
    'total_amount'=>'decimal:2',
    'commission_amount'=>'decimal:2'
   ];

     public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function vendorOrders()
    {
        return $this->hasMany(VendorOrder::class);
    }


    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function address()
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }


    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }


    public function isDelivered()
    {
        return $this->status === 'delivered';
    }  

    public function isPaid()
    {
        return $this->payment_status === 'completed';
    }  

    public function generateOrderNumber()
    {
       return 'ORD-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
