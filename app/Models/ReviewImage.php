<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'image_url',
        'display_order',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function getImageUrl(): string
    {
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image_url);
    }
}
