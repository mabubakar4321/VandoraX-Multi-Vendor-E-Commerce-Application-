<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
     use HasFactory;

    protected $fillable = [
        'product_id',
        'image_url',
        'image_alt_text',
        'display_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    // Methods
    public function makePrimary()
    {
        ProductImage::where('product_id', $this->product_id)
            ->update(['is_primary' => false]);
        
        $this->update(['is_primary' => true]);
    }
}
