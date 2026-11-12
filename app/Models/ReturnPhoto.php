<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnPhoto extends Model
{
    protected $fillable = ['return_id', 'path', 'original_name', 'mime_type', 'size'];

    public function returnRequest()
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
    }
}
