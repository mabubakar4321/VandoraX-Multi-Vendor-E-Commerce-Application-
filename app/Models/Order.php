<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

     public function items(): HasMany
     {
         return $this->hasMany(OrderItem::class);
     }

     public function vendorOrders(): HasMany
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

     public function statusHistory()
     {
         return $this->hasMany(OrderStatusHistory::class);
     }

     public function scopePending($query)
     {
         return $query->where('status', 'pending');
     }

     public function scopeDelivered($query)
     {
         return $query->where('status', 'delivered');
     }

     public function scopeCancelled($query)
     {
         return $query->where('status', 'cancelled');
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

     public function isCancelled()
     {
         return $this->status === 'cancelled';
     }

      public function canCancel(): bool
      {
          if ($this->status === 'cancelled' || $this->status === 'delivered') {
              return false;
          }

          $anyConfirmed = $this->vendorOrders()
              ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
              ->exists();

          return !$anyConfirmed;
      }

         public function getEffectiveStatusAttribute(): string
         {
             if ($this->status !== 'pending') {
                 return $this->status;
             }

             $statuses = $this->relationLoaded('vendorOrders')
                 ? $this->vendorOrders->pluck('status')
                 : $this->vendorOrders()->pluck('status');

             if ($statuses->isNotEmpty() && $statuses->contains('cancelled')) {
                 return 'cancelled';
             }

             if ($statuses->isNotEmpty() && $statuses->every(fn ($status) => in_array($status, ['confirmed', 'processing', 'shipped', 'delivered'], true))) {
                 return 'confirmed';
             }

             return 'pending';
         }

     public function generateOrderNumber()
     {
        return 'ORD-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
     }
}
