<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ProductSpecification extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'spec_name',
        'spec_value',
        'display_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

     public function getDisplayName()
    {
        return $this->spec_name . ': ' . $this->spec_value;
    }
}
