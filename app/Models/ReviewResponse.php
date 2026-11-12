<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'vendor_id',
        'response_text',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}