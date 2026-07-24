<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     use HasFactory, SoftDeletes;

     protected $fillable = [
          'vendor_id',
        'category_id',
        'sub_category_id',
        'name',
        'slug',
        'description',
        'sku',
        'base_price',
        'stock_quantity',
        'warranty_info',
        'estimated_delivery_days',
        'average_rating',
        'total_reviews',
        'total_sales',
        'status',
        'is_featured',
        'meta_title',
        'meta_description',
     ];

     protected $casts = [
               
        'base_price' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'is_featured' => 'boolean',

     ];

     public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

   public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

     public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
   public function getPrimaryImage()
    {
        return $this->images()->where('is_primary', true)->first();
    }

    public function getAverageRating()
    {
        return round($this->reviews()->avg('product_rating'), 2);
    }
}
