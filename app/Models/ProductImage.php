<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id', 'image_path', 'thumbnail_path',
        'is_main', 'display_order', 'alt_text', 'file_size',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrl()
    {
        return asset('storage/' . $this->image_path);
    }

    public function getThumbnailUrl()
    {
        return asset('storage/' . ($this->thumbnail_path ?? $this->image_path));
    }
}