<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'description',
        'base_price',
        'sku',
        'warranty',
        'delivery_time_days',
        'status',
        'moderation_status',
        'moderation_reason',
        'moderated_by',
        'moderated_at',
        'is_visible',
        'meta_title',
        'meta_description',
        'keywords',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_visible' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'moderated_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('display_order');
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class)
            ->orderBy('display_order');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(ProductInventory::class);
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(ProductInventoryLog::class);
    }

    // ===== SCOPES =====

    public function scopePublished($query)
    {
        return $query
            ->where('status', 'published')
            ->where('is_visible', true);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeActive($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId)
            ->orWhere('subcategory_id', $categoryId);
    }

    public function scopeSearch($query, $term)
    {
        return $query
            ->where('name', 'like', "%{$term}%")
            ->orWhere('sku', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%");
    }

    // ===== METHODS =====

    public static function generateSlug($name)
    {
        $slug = \Str::slug($name);

        $count = self::where('slug', 'like', $slug . '%')->count();

        return $count > 0
            ? $slug . '-' . ($count + 1)
            : $slug;
    }

    public function mainImage()
    {
        return $this->images()
            ->where('is_main', true)
            ->first()
            ?? $this->images()->first();
    }

    public function getTotalStock()
    {
        return $this->variants()->sum('stock')
            ?? $this->inventory()->sum('quantity')
            ?? 0;
    }

    public function isOutOfStock()
    {
        return $this->getTotalStock() <= 0;
    }

    public function publish()
    {
        return $this->update([
            'status' => 'published',
        ]);
    }

    public function unpublish()
    {
        return $this->update([
            'status' => 'draft',
        ]);
    }
}