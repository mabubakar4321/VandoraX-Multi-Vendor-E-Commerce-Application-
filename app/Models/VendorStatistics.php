<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorStatistics extends Model
{
     use HasFactory;

    protected $fillable = [
        'vendor_id',
        'total_sales_amount',
        'total_orders_count',
        'total_products',
        'total_customers',
        'average_response_time',
        'return_rate',
        'cancellation_rate',
        'total_positive_reviews',
        'total_negative_reviews',
        'completion_rate',
        'last_updated_at',
    ];

    protected $casts = [
        'total_sales_amount' => 'decimal:2',
        'return_rate' => 'decimal:2',
        'cancellation_rate' => 'decimal:2',
        'completion_rate' => 'decimal:2',
        'last_updated_at' => 'datetime',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Methods
    public function updateStatistics()
    {
        $vendor = $this->vendor;
        
        $this->update([
            'total_sales_amount' => $vendor->total_sales,
            'total_orders_count' => $vendor->total_orders,
            'last_updated_at' => now(),
        ]);
    }
}
