<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'product_id',
        'position',
        'is_active',
    ];


    protected $casts = [
         'is_active' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class);
    }
        public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function moveUp()
    {
        if ($this->position > 0) {
            $this->position--;
            $this->save();
        }
    }

     public function moveDown()
    {
        $this->position++;
        $this->save();
    }
}

