<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id', 'variant_name', 'variant_value',
        'price', 'stock', 'sku', 'weight', 'dimensions',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(ProductInventory::class, 'variant_id');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(ProductInventoryLog::class, 'variant_id');
    }

    public function getDisplayName()
    {
        return "{$this->variant_name}: {$this->variant_value}";
    }
}