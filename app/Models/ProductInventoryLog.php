<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInventoryLog extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $table = 'product_inventory_logs';

    protected $fillable = [
        'product_id',
        'variant_id',
        'quantity_before',
        'quantity_after',
        'quantity_change',
        'action_type',
        'reason',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'quantity_change' => 'integer',
        'created_at' => 'datetime',
    ];

    const ACTION_TYPES = [
        'manual_adjustment' => 'Manual Adjustment',
        'order_placed' => 'Order Placed',
        'order_cancelled' => 'Order Cancelled',
        'import' => 'Bulk Import',
        'return' => 'Customer Return',
        'damage' => 'Damage/Defect',
        'restock' => 'Restock',
        'initial' => 'Initial Stock',
    ];

    // ===== RELATIONSHIPS =====

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ===== SCOPES =====

    public function scopeRecent($query)
    {
        return $query->latest('created_at');
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForVariant($query, $variantId)
    {
        return $query->where('variant_id', $variantId);
    }

    public function scopeByActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    // ===== HELPERS =====

    public function getActionLabel()
    {
        return self::ACTION_TYPES[$this->action_type] ?? ucfirst(str_replace('_', ' ', $this->action_type));
    }

    public function getActionColor()
    {
        return match($this->action_type) {
            'manual_adjustment' => 'blue',
            'order_placed' => 'red',
            'order_cancelled' => 'green',
            'import' => 'purple',
            'return' => 'green',
            'damage' => 'orange',
            'restock' => 'green',
            'initial' => 'gray',
            default => 'gray'
        };
    }

    public function isIncrease()
    {
        return $this->quantity_change > 0;
    }

    public function isDecrease()
    {
        return $this->quantity_change < 0;
    }
}
