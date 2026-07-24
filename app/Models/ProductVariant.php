<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
      use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_name',
        'variant_type',
        'variant_value',
        'price',
        'stock_quantity',
        'sku',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock_quantity', '>', 0);
    }

    // Methods
    public function isInStock()
    {
        return $this->stock_quantity > 0 && $this->is_available;
    }
}
