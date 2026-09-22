<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'parent_id',
        'display_order',
        'is_active',
        'commission_rate',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->orderBy('display_order', 'asc');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class, 'category_id');
    }

    public function commissionHistory(): HasMany
    {
        return $this->hasMany(CategoryCommissionHistory::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ===== SCOPES =====

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSub($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')
            ->orderBy('name', 'asc');
    }

    // ===== METHODS =====

    public static function generateSlug(string $name, ?int $excludeId = null): string
    {
        $slug = \Str::slug($name);
        
        $count = self::where('slug', 'like', $slug . '%')
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->count();

        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        return $slug;
    }

    public static function slugExists(string $slug, ?int $excludeId = null): bool
    {
        return self::where('slug', $slug)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    public function canBeDeleted(): bool
    {
        if ($this->products()->count() > 0) {
            return false;
        }

        if ($this->children()->count() > 0) {
            return false;
        }

        if ($this->vendors()->count() > 0) {
            return false;
        }

        return true;
    }

    public function getDeletionReasons(): array
    {
        $reasons = [];

        if ($this->products()->count() > 0) {
            $reasons[] = "This category has {$this->products()->count()} product(s)";
        }

        if ($this->children()->count() > 0) {
            $reasons[] = "This category has {$this->children()->count()} subcategory(ies)";
        }

        if ($this->vendors()->count() > 0) {
            $reasons[] = "This category is assigned to {$this->vendors()->count()} vendor(s)";
        }

        return $reasons;
    }

    public function getFullPath(): string
    {
        if ($this->parent) {
            return $this->parent->getFullPath() . ' > ' . $this->name;
        }
        return $this->name;
    }

    public function isMainCategory(): bool
    {
        return is_null($this->parent_id);
    }

    public function isSubcategory(): bool
    {
        return !is_null($this->parent_id);
    }
}